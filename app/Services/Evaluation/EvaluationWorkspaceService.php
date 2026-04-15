<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;

class EvaluationWorkspaceService
{
    public function getWorkspace(DecisionSession $session, $user): array
    {
        // Validate assignment
        $assignment = $session->assignments()
            ->where('user_id', $user->id)
            ->first();

        abort_if(!$assignment, 403, 'User not assigned to this session.');

        // Get assigned criteria ids
        $assignedCriteriaIds = $session->assignments()
            ->where('user_id', $user->id)
            ->where('can_evaluate', true)
            ->pluck('criteria_id')
            ->toArray();

        // Load only allowed criteria (human + system)
        $allowedCriteria = $session->criteria()
            ->where('is_active', true)
            ->where('level', 2)
            ->where(function ($q) use ($assignedCriteriaIds) {
                $q->whereIn('id', $assignedCriteriaIds)
                    ->orWhere('evaluator_type', 'system');
            })
            ->orderBy('order')
            ->get();

        // Load alternatives
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Load existing evaluations (optional, nanti kita refine)
        $evaluations = $session->evaluationScores()
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('criteria_id');

        return [
            'decisionSession' => $session,
            'criteria'        => $allowedCriteria->values(),
            'alternatives'    => $alternatives,
            'evaluations'     => $evaluations,
            'tab'             => 'evaluasi-alternatif',
        ];
    }

    public function authorize(DecisionSession $session, $user, $evaluations)
    {
        foreach ($evaluations as $criteriaId => $alternatives) {

            $criteria = $session->criteria()->find($criteriaId);

            // Skip system evaluator (no assignment required)
            if ($criteria && $criteria->evaluator_type === 'system') {
                continue;
            }

            $hasAccess = $session->assignments()
                ->where('user_id', $user->id)
                ->where('can_evaluate', true)
                ->where('criteria_id', $criteriaId)
                ->exists();

            abort_if(!$hasAccess, 403, 'User not allowed to evaluate this criteria.');
        }
    }
}
