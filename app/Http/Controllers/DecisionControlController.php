<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;

class AhpPairwiseController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        $user = auth()->user();
        abort_if(! $user, 403);

        // Ambil kriteria aktif untuk session ini
        $criterias = $decisionSession->criterias()->where('is_active', true)->get();

        // Ambil hasil pairwise DM (jika ada)
        $existingPairwise = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->mapWithKeys(function ($item) {
                $key = min($item->criteria_a_id, $item->criteria_b_id)
                    . '-' .
                    max($item->criteria_a_id, $item->criteria_b_id);

                return [$key => $item];
            });

        // Flag status untuk kebutuhan workspace & nav
        $status = $decisionSession->status;

        // Apakah DM sudah menyelesaikan seluruh perbandingan kriteria
        $activeCriteriaCount = $criterias->count();
        $requiredPairs = $activeCriteriaCount > 1
            ? ($activeCriteriaCount * ($activeCriteriaCount - 1)) / 2
            : 0;

        $hasCompletedPairwise = $requiredPairs > 0
            && $existingPairwise->count() >= $requiredPairs;

        // Mode read-only jika:
        // - status bukan configured
        // - atau pairwise sudah lengkap
        $pairwiseReadOnly = $status !== 'configured' || $hasCompletedPairwise;

        return view('dms.index', [
            'decisionSession'     => $decisionSession,
            'criterias'           => $criterias,
            'existingPairwise'    => $existingPairwise,
            'hasCompletedPairwise' => $hasCompletedPairwise,
            'pairwiseReadOnly'    => $pairwiseReadOnly,
            'activeTab'           => 'pairwise',
        ]);
    }
}
