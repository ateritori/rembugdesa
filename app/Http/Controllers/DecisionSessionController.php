<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\Criteria;
use App\Models\BordaResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use App\Services\AHP\AhpGroupWeightService;

class DecisionSessionController extends Controller
{
    /**
     * Middleware sudah didefinisikan di Route,
     * namun tetap bagus sebagai proteksi lapis kedua.
     */
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

    /**
     * Sesuai dengan Route::get('/.../control', [DecisionSessionController::class, 'control'])
     * Mengarah ke folder: resources/views/control/index.blade.php
     */
    public function control(DecisionSession $decisionSession)
    {
        $decisionSession->load(['dms', 'alternatives', 'criterias']);

        // 1. Total DM yang ditugaskan
        $assignedDmCount = $decisionSession->dms()->count();

        // 2. Hitung berapa DM yang sudah melengkapi semua penilaian alternatif
        // Asumsi: Kita cek di tabel/relasi alternative_evaluations
        $dmEvaluationsDone = $decisionSession->dms()
            ->whereHas('alternativeEvaluations', function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id);
            }, '=', $decisionSession->alternatives()->count() * $decisionSession->criterias()->count())
            ->count();

        return view('control.index', compact('decisionSession', 'assignedDmCount', 'dmEvaluationsDone'));
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
        // Edit detail session hanya boleh saat draft
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

    public function activate(
        DecisionSession $decisionSession,
        AhpGroupWeightService $ahpGroupWeightService
    ) {
        // 1. Valid status transition guard
        $validStatuses = ['draft', 'configured'];

        abort_unless(
            in_array($decisionSession->status, $validStatuses),
            403,
            'Sesi tidak dapat diaktifkan pada tahap ini.'
        );

        // 2. Core readiness validation
        if ($decisionSession->criterias()->where('is_active', true)->count() < 2) {
            return back()->withErrors('Minimal 2 kriteria aktif diperlukan.');
        }

        if ($decisionSession->alternatives()->where('is_active', true)->count() < 2) {
            return back()->withErrors('Minimal 2 alternatif aktif diperlukan.');
        }

        if ($decisionSession->dms()->count() < 1) {
            return back()->withErrors('Minimal 1 decision maker diperlukan.');
        }

        // 3. Scoring rule completeness validation
        $activeCriteria = $decisionSession->criterias()
            ->where('is_active', true)
            ->with(['scoringRule.parameters'])
            ->get();

        if (!$activeCriteria->every(fn($c) => $c->scoringRule?->isComplete())) {
            return back()->withErrors('Aturan scoring kriteria belum lengkap.');
        }

        // 4. PATCH: aggregate AHP group weights exactly once
        if ($decisionSession->status === 'configured') {
            try {
                $ahpGroupWeightService->aggregate($decisionSession);
            } catch (\Throwable $e) {
                return back()->withErrors(
                    'Agregasi bobot kriteria gagal: ' . $e->getMessage()
                );
            }
        }

        // 5. Status transition
        $nextStatus = ($decisionSession->status === 'draft')
            ? 'configured'
            : 'scoring';

        $decisionSession->update(['status' => $nextStatus]);

        return back()->with(
            'success',
            $nextStatus === 'scoring'
                ? 'Penilaian alternatif telah dibuka.'
                : 'Sesi berhasil dikonfigurasi.'
        );
    }

    public function close(
        DecisionSession $decisionSession,
        \App\Services\Borda\BordaRankingService $bordaRankingService
    ) {
        // 1. Status guard: hanya boleh ditutup dari tahap scoring
        abort_if(
            ! in_array($decisionSession->status, ['scoring']),
            403,
            'Sesi belum siap ditutup.'
        );

        // 2. Pastikan semua DM sudah punya hasil SMART
        $dmCount = $decisionSession->dms()->count();

        $smartDmCount = \App\Models\SmartResultDm::where('decision_session_id', $decisionSession->id)
            ->distinct('dm_id')
            ->count('dm_id');

        abort_if(
            $dmCount !== $smartDmCount,
            403,
            'Masih ada DM yang belum menyelesaikan penilaian.'
        );

        // 3. Hitung & simpan Borda + kunci sesi (atomic)
        \Illuminate\Support\Facades\DB::transaction(function () use (
            $decisionSession,
            $bordaRankingService
        ) {
            $bordaRankingService->calculateAndPersist($decisionSession);
            $decisionSession->update(['status' => 'closed']);
        });

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil difinalisasi dan dikunci.');
    }

    public function result(DecisionSession $decisionSession)
    {
        // Guard: hasil hanya boleh dilihat jika sesi sudah final
        abort_if(
            $decisionSession->status !== 'closed',
            403,
            'Hasil hanya tersedia setelah sesi difinalisasi.'
        );

        $results = BordaResult::where('decision_session_id', $decisionSession->id)
            ->with('alternative')
            ->orderBy('final_rank')
            ->get();

        return view('decision-sessions.result', compact(
            'decisionSession',
            'results'
        ));
    }

    public function destroy(DecisionSession $decisionSession)
    {
        // Hanya izinkan hapus jika masih draft
        abort_if($decisionSession->status !== 'draft', 403, 'Session yang sudah berjalan tidak bisa dihapus.');

        $decisionSession->delete();

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session deleted.');
    }

    // ================= PENUGASAN DM =================

    public function assignDms(DecisionSession $decisionSession)
    {
        try {
            // Ambil user dengan role dm jika role ada
            $dms = User::role('dm')->get();
        } catch (RoleDoesNotExist $e) {
            // Jika role dm belum ada sama sekali
            $dms = collect();
        }

        // Ambil ID DM yang sudah terhubung dengan session ini
        $assignedDmIds = $decisionSession->dms()
            ->pluck('users.id')
            ->toArray();

        return view('decision-sessions.assign-dms', compact(
            'decisionSession',
            'dms',
            'assignedDmIds'
        ));
    }

    public function storeAssignedDms(Request $request, DecisionSession $decisionSession)
    {
        // Proteksi: tidak boleh ubah DM jika session sudah dikunci
        abort_if(in_array($decisionSession->status, ['closed']), 403, 'Tidak bisa mengubah DM pada sesi yang sudah dikunci.');

        $request->validate([
            'dm_ids' => 'nullable|array',
            'dm_ids.*' => 'exists:users,id'
        ]);

        try {
            // Ambil hanya user yang benar-benar punya role dm
            $dmIds = User::role('dm')
                ->whereIn('id', $request->input('dm_ids', []))
                ->pluck('id')
                ->toArray();
        } catch (RoleDoesNotExist $e) {
            // Jika role dm tidak ada, pastikan pivot dikosongkan
            $dmIds = [];
        }

        // Sinkronisasi pivot dengan aman
        $decisionSession->dms()->sync($dmIds);

        return redirect()
            ->route('decision-sessions.assign-dms', $decisionSession->id)
            ->with('success', 'Daftar Decision Maker berhasil diperbarui.');
    }
}
