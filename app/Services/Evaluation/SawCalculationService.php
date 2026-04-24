<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;

class SawCalculationService
{
    /**
     * Calculate SAW score per alternative (level 2 only)
     */
    public function calculate(DecisionSession $session, int $userId): array
    {
        if (!$userId) {
            throw new \Exception('User ID wajib dikirim ke SawCalculationService');
        }

        // Load alternatives
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Load assigned criteria (level 2)
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

        // Load weights (level 1 / sector)
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

        // Normalize weight keys
        $weights = collect($weights)
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->all();

        // Load evaluation scores
        $scores = $session->evaluationScores()
            ->where('user_id', $userId)
            ->whereIn('criteria_id', $assignedCriteriaIds)
            ->get()
            ->groupBy('criteria_id');

        // Step 1: Normalisasi SAW (tanpa utility)
        $normalized = [];

        foreach ($scores as $criteriaId => $rows) {

            if (!isset($criteria[$criteriaId])) {
                continue;
            }

            $values = $rows->pluck('value')->toArray();

            if (empty($values)) {
                continue;
            }

            $max = max($values);
            $min = min($values);

            foreach ($rows as $row) {

                $type = $criteria[$criteriaId]->type ?? 'benefit';

                if ($type === 'cost') {
                    $norm = $row->value == 0 ? 0 : $min / $row->value;
                } else {
                    $norm = $max == 0 ? 0 : $row->value / $max;
                }

                $normalized[$row->alternative_id][$criteriaId] = $norm;
            }
        }

        // Step 2: Aggregation
        $results = [];
        $upserts = [];

        foreach ($alternatives as $altId => $alt) {

            $total = 0;
            $count = 0;

            foreach ($criteria as $criteriaId => $c) {

                $value = $normalized[$altId][$criteriaId] ?? null;

                if ($value === null) {
                    continue;
                }

                $upserts[] = [
                    'decision_session_id' => $session->id,
                    'user_id' => $userId,
                    'alternative_id' => $altId,
                    'criteria_id' => $criteriaId,
                    'method' => 'saw',
                    'evaluation_score' => $value,
                    'weighted_score' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                $total += $value;
                $count++;
            }

            // 🔹 SAW score = rata-rata normalized (sama seperti trace)
            $sawScore = $count > 0 ? $total / $count : 0;

            // 🔹 weight sektor diaplikasikan SEKALI di akhir
            $sectorId = (int) $alt->criteria_id;
            $weight = $weights[$sectorId] ?? 1;

            $finalScore = $sawScore * $weight;

            $results[$altId] = round($finalScore, 6);
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
