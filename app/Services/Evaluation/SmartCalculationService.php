<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;

class SmartCalculationService
{
    /**
     * Calculate SMART score per alternative (level 2 only)
     */
    public function calculate(DecisionSession $session, ?int $userId): array
    {
        if ($userId === 0) {
            throw new \Exception('User ID tidak valid');
        }

        // Load alternatives
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Load criteria level 2 assigned to this user
        if ($userId === null) {
            // System evaluates all level 2 criteria
            $assignedCriteriaIds = $session->criteria()
                ->where('is_active', true)
                ->where('level', 2)
                ->pluck('id')
                ->toArray();
        } else {
            $assignedCriteriaIds = $session->assignments()
                ->where('user_id', $userId)
                ->where('can_evaluate', true)
                ->pluck('criteria_id')
                ->filter()
                ->toArray();
        }

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

        // Load sector/group weights (AHP level sektor)
        $groupWeightRecord = $session->groupWeight;

        if ($groupWeightRecord) {
            $rawGroup = $groupWeightRecord->weights;

            if (is_string($rawGroup)) {
                $groupWeights = json_decode($rawGroup, true) ?: [];
            } elseif (is_array($rawGroup)) {
                $groupWeights = $rawGroup;
            } else {
                $groupWeights = [];
            }
        } else {
            $groupWeights = [];
        }

        // Normalize JSON keys → integer sector_id (STRICT)
        $groupWeights = collect($groupWeights)
            ->mapWithKeys(function ($v, $k) {
                if (!is_numeric($k)) {
                    throw new \Exception("Invalid sector key in JSON: {$k}");
                }
                return [(int)$k => (float)$v];
            })
            ->all();

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

        // Scores for this DM (for utility calculation)
        $scoresQuery = $session->evaluationScores()
            ->whereIn('criteria_id', $assignedCriteriaIds);

        if ($userId === null) {
            $scoresQuery->whereNull('user_id');
        } else {
            $scoresQuery->where('user_id', $userId);
        }

        $scores = $scoresQuery->get()->groupBy('criteria_id');


        // Step 1: Normalize values per criteria
        $normalized = [];
        $debug = [];

        foreach ($scores as $criteriaId => $rows) {

            if (!isset($criteria[$criteriaId])) {
                continue;
            }

            // Use LOCAL values (per DM) for normalization (Excel mode)
            $values = $rows->pluck('value')->toArray();

            if (empty($values)) {
                continue;
            }

            $rule = $rules[$criteriaId] ?? null;

            if ($rule && $rule->input_type === 'scale') {
                // Excel logic: skala selalu 1–5
                $min = 1;
                $max = 5;
            } else {
                $min = min($values);
                $max = max($values);
            }

            foreach ($rows as $row) {

                $type = $criteria[$criteriaId]->type ?? 'benefit';

                if ($max == $min || ($max - $min) == 0) {
                    $norm = 0;
                } else {
                    if ($type === 'cost') {
                        $norm = ($max - $row->value) / ($max - $min);
                    } else {
                        $norm = ($row->value - $min) / ($max - $min);
                    }
                }

                $norm = max(0, min(1, $norm));

                $normalizedValue = $norm;

                // Apply utility function (SOURCE OF TRUTH)
                $utilityValue = $normalizedValue;

                if ($rule && $normalizedValue !== null) {
                    $deg = $rule->curve_degree;

                    if ($rule->utility_function === 'concave') {
                        $deg = $deg ?? 0.2;
                        $utilityValue = pow($normalizedValue, $deg);
                    } elseif ($rule->utility_function === 'convex') {
                        $deg = $deg ?? 4;
                        $utilityValue = pow($normalizedValue, $deg);
                    }
                }

                $normalized[$row->alternative_id][$criteriaId] = $utilityValue;

                $debug[$userId][$row->alternative_id][] = [
                    'criteria_id' => $criteriaId,
                    'raw_value' => $row->value,
                    'min' => $min,
                    'max' => $max,
                    'normalized' => $normalizedValue,
                    'utility' => $utilityValue,
                ];
            }
        }


        // Step 2: Aggregate (equal weight per criteria)
        // NOTE: debug mode active → per-criteria detail returned
        $upserts = [];

        $results = [];

        foreach ($alternatives as $altId => $alt) {

            // STRICT mapping: alternative.criteria_id MUST match JSON key
            $sectorId = (int) $alt->criteria_id;

            if (!isset($groupWeights[$sectorId])) {
                // debug safeguard: force visibility if mapping fails
                throw new \Exception("Sector weight not found for sector_id={$sectorId}");
            }

            $sectorWeight = (float) $groupWeights[$sectorId];

            $total = 0;

            foreach ($criteria as $criteriaId => $c) {

                $value = $normalized[$altId][$criteriaId] ?? null;

                if ($value === null) {
                    continue;
                }

                // weight tidak digunakan di Excel mode
                $w = 0;

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

                // $total += $value * $w; // Removed as per instructions
            }

            // Simple average (Excel mode, equal weight)
            $vals = array_values($normalized[$altId] ?? []);

            $total = count($vals) > 0 ? array_sum($vals) / count($vals) : 0;

            // Apply sector weight
            $score = $total * $sectorWeight;

            // keep full precision (round only at presentation layer)
            $results[$altId] = $score;

            // Save final SMART score (criteria_id = null)
            $upserts[] = [
                'decision_session_id' => $session->id,
                'user_id' => $userId,
                'alternative_id' => $altId,
                'criteria_id' => null,
                'method' => 'smart',
                'evaluation_score' => $score,
                'weighted_score' => $score,
                'sector_id' => $sectorId,
                'sector_weight' => $sectorWeight,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }

        if (!empty($upserts)) {
            EvaluationResult::upsert(
                $upserts,
                ['decision_session_id', 'user_id', 'alternative_id', 'criteria_id', 'method'],
                ['evaluation_score', 'weighted_score', 'updated_at']
            );
        }

        return [
            'results' => $results,
            'normalized' => $normalized,
            'debug' => $debug,
        ];
    }
}
