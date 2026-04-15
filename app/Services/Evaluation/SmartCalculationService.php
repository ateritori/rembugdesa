<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;

class SmartCalculationService
{
    /**
     * Calculate SMART score per alternative (level 2 only)
     */
    public function calculate(DecisionSession $session, int $userId): array
    {
        if (!$userId) {
            throw new \Exception('User ID wajib dikirim ke SmartCalculationService');
        }

        // Load alternatives
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Load criteria level 2 assigned to this user
        $assignedCriteriaIds = $session->assignments()
            ->where('user_id', $userId)
            ->where('can_evaluate', true)
            ->pluck('criteria_id')
            ->filter()
            ->toArray();

        $criteria = $session->criteria()
            ->where('is_active', true)
            ->where('level', 2)
            ->whereIn('id', $assignedCriteriaIds)
            ->get()
            ->keyBy('id');

        // Load scoring rules
        $rules = $session->criteriaScoringRules()
            ->get()
            ->keyBy('criteria_id');

        // Load weights (assume latest weights per session)
        $weightRecord = $session->criteriaWeights()->latest()->first();

        if ($weightRecord) {
            $raw = $weightRecord->weights;

            if (is_string($raw)) {
                $weights = json_decode($raw, true) ?: [];
            } elseif (is_array($raw)) {
                $weights = $raw;
            } else {
                $weights = [];
            }
        } else {
            $weights = [];
        }

        // Normalize weight keys to int
        $weights = collect($weights)
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->all();

        if (empty($weights) && $criteria->count() > 0) {
            $equal = 1 / $criteria->count();
            foreach ($criteria as $cid => $c) {
                $weights[$cid] = $equal;
            }
        }

        // Load evaluation scores
        $scores = $session->evaluationScores()
            ->where('user_id', $userId)
            ->whereIn('criteria_id', $assignedCriteriaIds)
            ->get()
            ->groupBy('criteria_id');

        // Step 1: Normalize values per criteria
        $normalized = [];

        foreach ($scores as $criteriaId => $rows) {

            if (!isset($criteria[$criteriaId])) {
                continue;
            }

            $values = $rows->pluck('value')->toArray();

            if (empty($values)) {
                continue;
            }

            $rule = $rules[$criteriaId] ?? null;

            if ($rule && $rule->input_type === 'scale') {
                $min = $rule->scale_min;
                $max = $rule->scale_max;
            } else {
                $min = min($values);
                $max = max($values);
            }

            foreach ($rows as $row) {

                $type = $criteria[$criteriaId]->type ?? 'benefit';

                if ($max == $min) {
                    $norm = 1;
                } else {
                    if ($type === 'cost') {
                        $norm = ($max - $row->value) / ($max - $min);
                    } else {
                        $norm = ($row->value - $min) / ($max - $min);
                    }
                }

                // Apply utility function
                if ($rule) {
                    $r = $rule->curve_degree;

                    if ($rule->utility_function === 'concave') {
                        $r = $r ?? 0.5;
                        $norm = pow($norm, $r);
                    } elseif ($rule->utility_function === 'convex') {
                        $r = $r ?? 2;
                        $norm = pow($norm, $r);
                    }
                }

                $normalized[$row->alternative_id][$criteriaId] = $norm;
            }
        }


        // Step 2: Aggregate (equal weight per criteria)
        $upserts = [];

        $results = [];

        foreach ($alternatives as $altId => $alt) {

            $total = 0;

            foreach ($criteria as $criteriaId => $c) {

                $value = $normalized[$altId][$criteriaId] ?? null;

                if ($value === null) {
                    continue;
                }

                // Use sector (level 1) weight based on alternative
                $sectorId = $alt->criteria_id;
                $w = $weights[$sectorId] ?? 1;

                if (!$userId) {
                    throw new \Exception('User ID hilang saat proses penyimpanan SMART');
                }

                $upserts[] = [
                    'decision_session_id' => $session->id,
                    'user_id' => $userId,
                    'alternative_id' => $altId,
                    'criteria_id' => $criteriaId,
                    'method' => 'smart',
                    'evaluation_score' => $value,
                    'weighted_score' => $value * $w,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                $total += $value * $w;
            }

            $score = $total;

            $results[$altId] = round($score, 6);
        }

        if (!empty($upserts)) {
            EvaluationResult::upsert(
                $upserts,
                ['decision_session_id', 'user_id', 'alternative_id', 'criteria_id', 'method'],
                ['evaluation_score', 'weighted_score', 'updated_at']
            );
        }

        return $results;
    }
}
