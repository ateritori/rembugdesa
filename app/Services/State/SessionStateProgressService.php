<?php

namespace App\Services\State;

use App\Models\DecisionSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionStateProgressService
{
    public function calculate(DecisionSession $decisionSession): array
    {
        $decisionSession->load(['assignments']);

        $assignments = $decisionSession->assignments;

        $assignedDmIds = $assignments->pluck('user_id')->unique();
        $dms = User::whereIn('id', $assignedDmIds)->get();

        $dmProgress = $dms->map(function ($dm) use ($decisionSession, $assignments) {

            $hasPairwiseAssignment = $assignments
                ->where('user_id', $dm->id)
                ->where('can_pairwise', true)
                ->isNotEmpty();

            $hasEvaluateAssignment = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->isNotEmpty();

            $pairwiseDone = $hasPairwiseAssignment
                ? $dm->criteriaWeights()
                ->where('decision_session_id', $decisionSession->id)
                ->exists()
                : false;

            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) {
                $alternativeDone = false;
                $expected = 0;
                $actual = 0;
            } else {
                $expected = $assignmentCount;

                $actual = DB::table('evaluation_scores')
                    ->where('decision_session_id', $decisionSession->id)
                    ->where('user_id', $dm->id)
                    ->whereNotNull('user_id')
                    ->where('source', '!=', 'system')
                    ->distinct('criteria_id')
                    ->count('criteria_id');

                $alternativeDone = $actual >= $expected;
            }

            return [
                'id' => $dm->id,
                'name' => $dm->name,
                'has_pairwise' => $hasPairwiseAssignment,
                'has_evaluate' => $hasEvaluateAssignment,
                'pairwise' => $pairwiseDone,
                'alternative' => $alternativeDone,
                'expected' => $expected,
                'actual' => $actual,
            ];
        });

        // Aggregation
        $totalExpectedActions = 0;
        $totalActualActions = 0;

        foreach ($dms as $dm) {
            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            $totalExpectedActions += $assignmentCount;

            if ($assignmentCount === 0) continue;

            $actual = DB::table('evaluation_scores')
                ->where('decision_session_id', $decisionSession->id)
                ->where('user_id', $dm->id)
                ->whereNotNull('user_id')
                ->where('source', '!=', 'system')
                ->distinct('criteria_id')
                ->count('criteria_id');

            $totalActualActions += $actual;
        }

        $dmPairwiseDone = $dms->filter(function ($dm) use ($decisionSession, $assignments) {
            $hasPairwiseAssignment = $assignments
                ->where('user_id', $dm->id)
                ->where('can_pairwise', true)
                ->isNotEmpty();

            if (!$hasPairwiseAssignment) return false;

            return $dm->criteriaWeights()
                ->where('decision_session_id', $decisionSession->id)
                ->exists();
        })->count();

        $dmAltDone = $dms->filter(function ($dm) use ($decisionSession, $assignments) {

            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) return false;

            $actual = DB::table('evaluation_scores')
                ->where('decision_session_id', $decisionSession->id)
                ->where('user_id', $dm->id)
                ->whereNotNull('user_id')
                ->where('source', '!=', 'system')
                ->distinct('criteria_id')
                ->count('criteria_id');

            return $actual >= $assignmentCount;
        })->count();

        $pairwiseEligible = $assignments
            ->where('can_pairwise', true)
            ->pluck('user_id')
            ->unique()
            ->count();

        $altEligible = $assignments
            ->where('can_evaluate', true)
            ->pluck('user_id')
            ->unique()
            ->count();

        return [
            'dmProgress' => $dmProgress,
            'dmPairwiseDone' => $dmPairwiseDone,
            'dmAltDone' => $dmAltDone,
            'pairwiseEligible' => $pairwiseEligible,
            'altEligible' => $altEligible,
            'totalExpectedActions' => $totalExpectedActions,
            'totalActualActions' => $totalActualActions,
        ];
    }
}
