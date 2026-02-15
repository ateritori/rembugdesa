<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\BordaResult;
use App\Models\SmartResultDm;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use App\Services\AHP\AhpGroupWeightService;
use App\Services\Result\DecisionResultService;
use App\Services\SAW\SawRankingService;
use App\Services\Borda\BordaRankingService;

class DecisionSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])
            ->except(['result']);
    }

    public function index()
    {
        $sessions = DecisionSession::latest()->get();
        return view('decision-sessions.index', compact('sessions'));
    }

    public function control(Request $request, DecisionSession $decisionSession)
    {
        $decisionSession->load(['dms', 'alternatives', 'criterias']);

        // ===== DATA DASHBOARD UTAMA =====
        $assignedDmCount = $decisionSession->dms()->count();

        // Cek DM yang sudah menyelesaikan seluruh matriks penilaian
        $totalExpectation = $decisionSession->alternatives()->count() * $decisionSession->criterias()->count();
        $dmEvaluationsDone = $decisionSession->dms()
            ->whereHas('alternativeEvaluations', function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id);
            }, '>=', $totalExpectation)
            ->count();

        // ===== LOGIKA TAB (HASIL & ANALISIS) =====
        $tab = $request->query('tab');
        $borda = collect();
        $sawBorda = collect();

        if ($decisionSession->status === 'closed') {
            $resultService = app(DecisionResultService::class);

            if ($tab === 'hasil-akhir' || $tab === 'analisis') {
                $borda = $resultService->borda($decisionSession);
            }

            if ($tab === 'analisis') {
                $sawService = app(SawRankingService::class);
                $sawBorda = $resultService->sawBordaBenchmark($decisionSession, $sawService);
            }
        }

        return view('control.index', compact(
            'decisionSession',
            'assignedDmCount',
            'dmEvaluationsDone',
            'borda',
            'sawBorda'
        ));
    }

    public function create()
    {
        return view('decision-sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        DecisionSession::create([
            'name'       => $request->name,
            'year'       => $request->year,
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session created successfully.');
    }

    public function edit(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403, 'Hanya session berstatus draft yang bisa diubah.');
        return view('decision-sessions.edit', compact('decisionSession'));
    }

    public function update(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        $decisionSession->update([
            'name' => $request->name,
            'year' => $request->year,
        ]);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session updated.');
    }

    public function activate(DecisionSession $decisionSession, AhpGroupWeightService $ahpGroupWeightService)
    {
        $validStatuses = ['draft', 'configured'];
        abort_unless(in_array($decisionSession->status, $validStatuses), 403);

        if ($decisionSession->criterias()->where('is_active', true)->count() < 2) {
            return back()->withErrors('Minimal 2 kriteria aktif diperlukan.');
        }

        if ($decisionSession->alternatives()->where('is_active', true)->count() < 2) {
            return back()->withErrors('Minimal 2 alternatif aktif diperlukan.');
        }

        if ($decisionSession->dms()->count() < 1) {
            return back()->withErrors('Minimal 1 decision maker diperlukan.');
        }

        $activeCriteria = $decisionSession->criterias()
            ->where('is_active', true)
            ->with(['scoringRule.parameters'])
            ->get();

        if (!$activeCriteria->every(fn($c) => $c->scoringRule?->isComplete())) {
            return back()->withErrors('Aturan scoring kriteria belum lengkap.');
        }

        if ($decisionSession->status === 'configured') {
            try {
                $ahpGroupWeightService->aggregate($decisionSession);
            } catch (\Throwable $e) {
                return back()->withErrors('Agregasi bobot kriteria gagal: ' . $e->getMessage());
            }
        }

        $nextStatus = ($decisionSession->status === 'draft') ? 'configured' : 'scoring';
        $decisionSession->update(['status' => $nextStatus]);

        return back()->with('success', $nextStatus === 'scoring' ? 'Penilaian terbuka.' : 'Sesi terkonfigurasi.');
    }

    public function close(DecisionSession $decisionSession, BordaRankingService $bordaRankingService)
    {
        abort_if($decisionSession->status !== 'scoring', 403, 'Sesi belum siap ditutup.');

        $dmCount = $decisionSession->dms()->count();
        $smartDmCount = SmartResultDm::where('decision_session_id', $decisionSession->id)
            ->distinct('dm_id')
            ->count('dm_id');

        abort_if($dmCount !== $smartDmCount, 403, 'Masih ada DM yang belum menyelesaikan penilaian.');

        DB::transaction(function () use ($decisionSession, $bordaRankingService) {
            $bordaRankingService->calculateAndPersist($decisionSession);
            $decisionSession->update(['status' => 'closed']);
        });

        return redirect()->route('decision-sessions.index')->with('success', 'Sesi ditutup.');
    }

    public function result(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'closed', 403);

        $results = BordaResult::where('decision_session_id', $decisionSession->id)
            ->with('alternative')
            ->orderBy('final_rank')
            ->get();

        return view('decision-sessions.result', compact('decisionSession', 'results'));
    }

    public function destroy(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403, 'Hanya draft yang bisa dihapus.');
        $decisionSession->delete();
        return redirect()->route('decision-sessions.index')->with('success', 'Deleted.');
    }

    // ================= PENUGASAN DM =================

    public function assignDms(DecisionSession $decisionSession)
    {
        try {
            $dms = User::role('dm')->get();
        } catch (RoleDoesNotExist $e) {
            $dms = collect();
        }

        $assignedDmIds = $decisionSession->dms()->pluck('users.id')->toArray();

        return view('decision-sessions.assign-dms', compact('decisionSession', 'dms', 'assignedDmIds'));
    }

    public function storeAssignedDms(Request $request, DecisionSession $decisionSession)
    {
        abort_if(in_array($decisionSession->status, ['closed']), 403);

        $request->validate([
            'dm_ids' => 'nullable|array',
            'dm_ids.*' => 'exists:users,id'
        ]);

        try {
            $dmIds = User::role('dm')->whereIn('id', $request->input('dm_ids', []))->pluck('id')->toArray();
        } catch (RoleDoesNotExist $e) {
            $dmIds = [];
        }

        $decisionSession->dms()->sync($dmIds);

        return redirect()
            ->route('decision-sessions.assign-dms', $decisionSession->id)
            ->with('success', 'Daftar DM diperbarui.');
    }
}
