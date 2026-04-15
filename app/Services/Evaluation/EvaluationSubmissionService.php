<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EvaluationSubmissionService
{
    /**
     * Authorize user to submit evaluation
     */
    public function authorize(DecisionSession $session, User $user, array $evaluations): void
    {
        foreach ($evaluations as $criteriaId => $alternatives) {

            $hasAccess = $session->assignments()
                ->where('user_id', $user->id)
                ->where('can_evaluate', true)
                ->where('criteria_id', $criteriaId)
                ->exists();

            abort_if(!$hasAccess, 403, 'User not allowed to evaluate this criteria.');
        }
    }

    /**
     * Store evaluation data (human only)
     */
    public function submit(DecisionSession $session, User $user, array $evaluations): void
    {
        DB::transaction(function () use ($session, $user, $evaluations) {

            // Load criteria level 2
            $criteria = $session->criteria()
                ->where('is_active', true)
                ->where('level', 2)
                ->get()
                ->keyBy('id');

            // Get assigned criteria for this user
            $assignedCriteria = $session->assignments()
                ->where('user_id', $user->id)
                ->where('can_evaluate', true)
                ->pluck('criteria_id')
                ->toArray();

            $rows = [];
            $now = now();

            foreach ($evaluations as $criteriaId => $alternatives) {

                // Skip invalid criteria
                if (!isset($criteria[$criteriaId])) continue;

                $c = $criteria[$criteriaId];

                // Skip system evaluator (handled separately)
                if ($c->evaluator_type === 'system') continue;

                // Check assignment per criteria
                if (!in_array($criteriaId, $assignedCriteria)) continue;

                foreach ($alternatives as $alternativeId => $value) {

                    if ($value === null || $value === '') continue;

                    $rows[] = [
                        'decision_session_id' => $session->id,
                        'user_id'             => $user->id,
                        'criteria_id'         => $criteriaId,
                        'alternative_id'      => $alternativeId,
                        'value'               => (float) $value,
                        'source'              => 'human',
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
            }

            // Delete existing human evaluations for this user
            $session->evaluationScores()
                ->where('user_id', $user->id)
                ->where('source', 'human')
                ->delete();

            // Insert new evaluations
            if (!empty($rows)) {
                EvaluationScore::insert($rows);
            }
        });
    }
}
