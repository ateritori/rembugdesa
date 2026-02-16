<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\BordaResult;
use App\Models\CriteriaWeight;
use App\Models\AlternativeEvaluation;
use App\Models\SmartResultDm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AHP\AhpGroupWeightService;
use App\Services\Result\DecisionResultService;
use App\Services\SAW\SawRankingService;
use App\Services\BORDA\BordaRankingService;

class DecisionSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['result']);
    }

    /**
     * Daftar semua sesi keputusan.
     */
    public function index()
    {
        $sessions = DecisionSession::withCount(['dms', 'alternatives'])
            ->latest()
            ->get();

        return view('decision-sessions.index', compact('sessions'));
    }

    /**
     * Detail Sesi Keputusan
     */
    public function show(DecisionSession $decisionSession)
    {
        return view('decision-sessions.show', compact('decisionSession'));
    }

    /**
     * Panel Kontrol Sesi (Dashboard Admin)
     */
    public function control(Request $request, DecisionSession $decisionSession)
    {
        $decisionSession->load(['dms', 'alternatives', 'criteria']);

        $assignedDmCount = $decisionSession->dms->count();
        $totalExpectation = $decisionSession->alternatives->count() * $decisionSession->criteria->count();

        $dmEvaluationsDone = $decisionSession->dms()
            ->whereHas('alternativeEvaluations', function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id);
            }, '>=', $totalExpectation)
            ->count();

        $tab = $request->query('tab');
        $borda = collect();
        $sawBorda = collect();
        $smartByDm = collect();

        $resultService = app(DecisionResultService::class);

        if ($decisionSession->status === 'closed') {
            if ($tab === 'hasil-akhir' || $tab === 'analisis') {
                $borda = $resultService->borda($decisionSession);
            }

            if ($tab === 'analisis') {
                $sawService = app(SawRankingService::class);
                $sawBorda = $resultService->sawBordaBenchmark($decisionSession, $sawService);
                $smartByDm = $resultService->smartByDm($decisionSession);
            }
        }

        return view('control.index', compact(
            'decisionSession',
            'assignedDmCount',
            'dmEvaluationsDone',
            'borda',
            'sawBorda',
            'smartByDm'
        ));
    }

    /**
     * Form Buat Sesi Keputusan
     */
    public function create()
    {
        return view('decision-sessions.create');
    }

    /**
     * Store new session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        $session = DecisionSession::create(array_merge($validated, [
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]));

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil dibuat.');
    }

    /**
     * Aktivasi Sesi
     */
    public function activate(DecisionSession $decisionSession, AhpGroupWeightService $ahpGroupWeightService)
    {
        $validStatuses = ['draft', 'configured'];
        abort_unless(in_array($decisionSession->status, $validStatuses), 403);

        if ($decisionSession->status === 'configured') {
            try {
                $decisionSession->getConnection()->transaction(function () use ($decisionSession, $ahpGroupWeightService) {
                    $ahpGroupWeightService->aggregate($decisionSession);
                });
            } catch (\Throwable $e) {
                Log::error('AHP Aggregation Error: ' . $e->getMessage());
                return back()->with('error', 'Gagal melakukan agregasi kriteria: ' . $e->getMessage());
            }
        }

        $nextStatus = ($decisionSession->status === 'draft') ? 'configured' : 'scoring';
        $decisionSession->update(['status' => $nextStatus]);

        return back()->with('success', 'Status sesi diperbarui.');
    }

    /**
     * Menutup sesi: Agregasi Borda (Full Eloquent Transaction)
     */
    public function close(DecisionSession $decisionSession, BordaRankingService $bordaRankingService)
    {
        abort_if($decisionSession->status !== 'scoring', 403);

        $assignedDmIds = $decisionSession->dms()->pluck('users.id')->toArray();
        $totalAssigned = count($assignedDmIds);
        $alternativeCount = $decisionSession->alternatives()->count();

        $expectedSmartCount = $totalAssigned * $alternativeCount;
        $actualSmartCount = SmartResultDm::where('decision_session_id', $decisionSession->id)->count();

        if ($actualSmartCount < $expectedSmartCount) {
            return back()->with('error', "Penutupan gagal. Data penilaian SMART belum lengkap.");
        }

        try {
            $decisionSession->getConnection()->transaction(function () use ($decisionSession, $bordaRankingService) {
                $bordaRankingService->calculateAndPersist($decisionSession);
                $decisionSession->update(['status' => 'closed']);
            });

            return redirect()
                ->route('control.index', [
                    'decisionSession' => $decisionSession->id,
                    'tab' => 'analisis'
                ])
                ->with('success', 'Sesi resmi ditutup. Hasil Borda telah disimpan.');
        } catch (\Exception $e) {
            Log::error('Borda Calculation Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    /**
     * Tampilan Publik Hasil Akhir
     */
    public function result(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'closed', 403);

        $resultService = app(DecisionResultService::class);
        $results = $resultService->borda($decisionSession);

        return view('decision-sessions.result', compact('decisionSession', 'results'));
    }

    /**
     * Form Penugasan DM
     */
    public function assignDms(DecisionSession $decisionSession)
    {
        $dms = User::role('dm')->get();
        $assignedDmIds = $decisionSession->dms()->pluck('users.id')->toArray();

        // Diarahkan ke folder assign-dms/index sesuai permintaan
        return view('assign-dms.index', compact('decisionSession', 'dms', 'assignedDmIds'));
    }

    /**
     * Simpan Penugasan DM
     */
    public function storeAssignedDms(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status === 'closed', 403);
        $validDmIds = User::role('dm')->whereIn('id', $request->input('dm_ids', []))->pluck('id');
        $decisionSession->dms()->sync($validDmIds);

        return redirect()->route('control.index', $decisionSession->id)->with('success', 'Daftar DM diperbarui.');
    }

    /**
     * Form Edit Sesi Keputusan
     */
    public function edit(DecisionSession $decisionSession)
    {
        return view('decision-sessions.edit', compact('decisionSession'));
    }

    /**
     * Update Sesi Keputusan
     */
    public function update(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status === 'closed', 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        $decisionSession->update($validated);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil diperbarui.');
    }

    /**
     * Hapus Sesi Keputusan
     */
    public function destroy(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

        $decisionSession->delete();

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil dihapus.');
    }
}
