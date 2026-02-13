<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;

class DecisionMakerController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        abort_if(
            $decisionSession->status === 'draft',
            403
        );
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

        $dmHasCompleted = ! is_null($criteriaWeights);

        // Alias for legacy views expecting $criterias
        $criterias = $criteria;

        // Workspace is the default tab (hard reset entry point)
        if (request()->boolean('reset')) {
            // explicit reset from dashboard
        }
        $activeTab = null;

        return view('dms.index', compact(
            'decisionSession',
            'criteria',
            'criterias',
            'existingPairwise',
            'criteriaWeights',
            'dmHasCompleted',
            'activeTab'
        ));
    }
    public function weights(DecisionSession $decisionSession)
    {
        return redirect()->route('dms.index', $decisionSession->id);
    }
    public function groupWeights(DecisionSession $decisionSession)
    {
        abort_if(
            ! $decisionSession->dms()->where('users.id', auth()->id())->exists(),
            403,
            'Anda tidak ditugaskan pada sesi ini.'
        );

        $groupResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')
            ->first();

        $criteriaWeights = CriteriaWeight::where(
            'decision_session_id',
            $decisionSession->id
        )
            ->where('dm_id', auth()->id())
            ->first();

        $dmHasCompleted = ! is_null($criteriaWeights);

        $activeTab = 'group-weights';

        return view('dms.index', compact(
            'decisionSession',
            'groupResult',
            'dmHasCompleted',
            'activeTab'
        ));
    }
}
