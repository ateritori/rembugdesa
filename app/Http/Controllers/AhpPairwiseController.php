<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AHP\AhpIndividualSubmissionService;

class AhpPairwiseController extends Controller
{
    /**
     * Show pairwise comparison workspace.
     */
    public function index(DecisionSession $decisionSession)
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('dm'), 403);

        // 1. Ambil kriteria aktif
        $criterias = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // 2. Ambil detail pairwise untuk repopulate slider
        $existingPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->mapWithKeys(function ($item) {
                $key = min($item->criteria_id_1, $item->criteria_id_2) . '-' .
                    max($item->criteria_id_1, $item->criteria_id_2);
                return [$key => $item];
            });

        // 3. Ambil hasil bobot (untuk tampilan progress bar/output)
        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->first();

        // 4. Hitung kelengkapan
        $criteriaCount = $criterias->count();
        $requiredPairs = $criteriaCount > 1 ? ($criteriaCount * ($criteriaCount - 1)) / 2 : 0;

        // SESUAIKAN DISINI: Ganti nama variabel agar cocok dengan Blade Anda
        $dmHasCompleted = $requiredPairs > 0 && $existingPairwise->count() >= $requiredPairs;

        // 5. Logika Readonly
        $pairwiseReadOnly = $decisionSession->status !== 'configured';

        return view('dms.index', [
            'decisionSession'   => $decisionSession,
            'criterias'         => $criterias,
            'existingPairwise'  => $existingPairwise,
            'criteriaWeights'   => $criteriaWeights, // Dibutuhkan oleh view output bobot
            'dmHasCompleted'    => $dmHasCompleted,  // Nama variabel disesuaikan untuk Blade
            'pairwiseReadOnly'  => $pairwiseReadOnly,
            'activeTab'         => 'pairwise',
        ]);
    }

    /**
     * Store pairwise comparison submitted by Decision Maker.
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        AhpIndividualSubmissionService $service
    ) {
        $user = Auth::user();

        abort_if(! $user || ! $user->hasRole('dm'), 403);
        abort_if($decisionSession->status !== 'configured', 403);

        $frontendPairs = json_decode($request->input('debug_frontend'), true);

        if (! is_array($frontendPairs)) {
            return back()
                ->withInput()
                ->with('error', 'Data frontend tidak valid.');
        }

        try {
            $result = $service->submit(
                $decisionSession,
                $user,
                $frontendPairs,
                $request->input('pairwise', [])
            );
        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('dms.weights.index', $decisionSession->id)
            ->with(
                'success',
                'Penilaian berhasil disimpan. CR = ' . round($result['cr'], 4)
            );
    }
}
