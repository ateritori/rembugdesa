<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;

class TopsisCalculationService
{
    public function calculate(DecisionSession $session, int $userId): array
    {
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Assigned criteria
        $assignedCriteriaIds = $session->assignments()
            ->where('user_id', $userId)
            ->where('can_evaluate', true)
            ->pluck('criteria_id')
            ->filter()
            ->toArray();

        $criteria = $session->criteria()
            ->where('level', 2)
            ->whereIn('id', $assignedCriteriaIds)
            ->get()
            ->keyBy('id');

        // Weight sektor
        $weightRecord = $session->criteriaWeights()->latest()->first();
        $weights = [];

        if ($weightRecord) {
            $raw = $weightRecord->weights;
            $weights = is_string($raw) ? json_decode($raw, true) : $raw;
        }

        $weights = collect($weights)
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->all();

        // Raw scores
        $scores = $session->evaluationScores()
            ->where('user_id', $userId)
            ->whereIn('criteria_id', $assignedCriteriaIds)
            ->get()
            ->groupBy('criteria_id');

        // STEP 1: Normalisasi vector
        $normalized = [];

        foreach ($scores as $criteriaId => $rows) {

            $denominator = sqrt(
                $rows->sum(fn($r) => pow($r->value, 2))
            );

            foreach ($rows as $row) {
                $normalized[$row->alternative_id][$criteriaId] =
                    $denominator == 0 ? 0 : $row->value / $denominator;
            }
        }

        // STEP 2: Weighted matrix
        $weighted = [];

        foreach ($alternatives as $altId => $alt) {

            foreach ($criteria as $criteriaId => $c) {

                $val = $normalized[$altId][$criteriaId] ?? null;
                if ($val === null) continue;

                $sectorId = $alt->criteria_id;
                $w = $weights[$sectorId] ?? 1;

                $weighted[$altId][$criteriaId] = $val * $w;
            }
        }

        // STEP 3: Ideal solutions
        $idealPositive = [];
        $idealNegative = [];

        foreach ($criteria as $criteriaId => $c) {

            $column = collect($weighted)
                ->pluck($criteriaId)
                ->filter();

            if ($column->isEmpty()) continue;

            if ($c->type === 'cost') {
                $idealPositive[$criteriaId] = $column->min();
                $idealNegative[$criteriaId] = $column->max();
            } else {
                $idealPositive[$criteriaId] = $column->max();
                $idealNegative[$criteriaId] = $column->min();
            }
        }

        // STEP 4 & 5: Distance & preference
        $results = [];

        $upserts = [];

        foreach ($alternatives as $altId => $alt) {

            $dPlus = 0;
            $dMinus = 0;

            foreach ($criteria as $criteriaId => $c) {

                $val = $weighted[$altId][$criteriaId] ?? null;
                if ($val === null) continue;

                $dPlus += pow($val - $idealPositive[$criteriaId], 2);
                $dMinus += pow($val - $idealNegative[$criteriaId], 2);

                $upserts[] = [
                    'decision_session_id' => $session->id,
                    'user_id' => $userId,
                    'alternative_id' => $altId,
                    'criteria_id' => $criteriaId,
                    'method' => 'topsis',
                    'evaluation_score' => $val,
                    'weighted_score' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

            $dPlus = sqrt($dPlus);
            $dMinus = sqrt($dMinus);

            $score = ($dPlus + $dMinus) == 0
                ? 0
                : $dMinus / ($dPlus + $dMinus);

            $results[$altId] = $score;
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
