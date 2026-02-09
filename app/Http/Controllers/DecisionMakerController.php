<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;

class DecisionMakerController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        abort_if(
            ! $decisionSession->dms()->where('users.id', auth()->id())->exists(),
            403,
            'Anda tidak ditugaskan pada sesi ini.'
        );

        // DM workspace entry point
        // All role-sensitive logic is handled in views by session status

        // Load criteria for pairwise
        $criteria = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Load existing pairwise judgments for this DM
        $existingPairwise = \App\Models\CriteriaPairwise::where(
            'decision_session_id',
            $decisionSession->id
        )
            ->where('dm_id', auth()->id())
            ->get()
            ->keyBy(fn($p) => $p->criteria_id_1 . '-' . $p->criteria_id_2);

        // Load criteria weights (if any)
        $criteriaWeights = \App\Models\CriteriaWeight::where(
            'decision_session_id',
            $decisionSession->id
        )
            ->where('dm_id', auth()->id())
            ->first();

        // Alias for legacy views expecting $criterias
        $criterias = $criteria;

        return view('dms.index', compact(
            'decisionSession',
            'criteria',
            'criterias',
            'existingPairwise',
            'criteriaWeights'
        ));
    }
}
