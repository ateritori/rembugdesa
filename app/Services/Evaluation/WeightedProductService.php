<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;

class WeightedProductService
{
    public function calculate(DecisionSession $session, int $userId): array
    {
        if (!$userId) {
            throw new \Exception('User ID wajib dikirim ke WP Service');
        }

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
            ->where('is_active', true)
            ->where('level', 2)
            ->whereIn('id', $assignedCriteriaIds)
            ->get()
            ->keyBy('id');

        // Weights (level 1 / sektor)
        $weightRecord = $session->criteriaWeights()->latest()->first();

        $weights = [];
        if ($weightRecord) {
            $raw = $weightRecord->weights;
            $weights = is_string($raw) ? json_decode($raw, true) : $raw;
        }

        $weights = collect($weights)
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->all();

        // Load raw scores
        $scores = $session->evaluationScores()
            ->where('user_id', $userId)
            ->whereIn('criteria_id', $assignedCriteriaIds)
            ->get()
            ->groupBy('alternative_id');

        $results = [];
        $upserts = [];

        foreach ($alternatives as $altId => $alt) {

            $product = 1;

            foreach ($criteria as $criteriaId => $c) {

                $row = $scores[$altId]
                    ->firstWhere('criteria_id', $criteriaId) ?? null;

                if (!$row) continue;

                $value = $row->value;

                if ($value <= 0) {
                    $value = 0.0001; // avoid zero product
                }

                $sectorId = $alt->criteria_id;
                $w = $weights[$sectorId] ?? 1;

                // Cost → negatif
                if ($c->type === 'cost') {
                    $w = -$w;
                }

                $score = pow($value, $w);

                $upserts[] = [
                    'decision_session_id' => $session->id,
                    'user_id' => $userId,
                    'alternative_id' => $altId,
                    'criteria_id' => $criteriaId,
                    'method' => 'wp',
                    'evaluation_score' => $value,
                    'weighted_score' => $score,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                $product *= $score;
            }

            $results[$altId] = $product;
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
