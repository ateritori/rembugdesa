<?php

namespace App\Services\SMART;

use App\Models\{DecisionSession, User, AlternativeEvaluation, CriteriaWeight, DmScore};
use InvalidArgumentException;

class SmartRankingService
{
    public function calculate(DecisionSession $session, User $dm, bool $persist = false): array
    {
        $groupWeight = CriteriaWeight::where('decision_session_id', $session->id)->whereNull('dm_id')->first();
        if (!$groupWeight || empty($groupWeight->weights)) {
            throw new InvalidArgumentException('Bobot kriteria kelompok belum tersedia.');
        }

        $rawWeights = $groupWeight->weights;
        $totalRawWeight = array_sum($rawWeights);
        if ($totalRawWeight <= 0) throw new InvalidArgumentException('Total bobot nol.');

        // Normalisasi Bobot
        $normalizedWeights = [];
        foreach ($rawWeights as $critId => $val) {
            $normalizedWeights[$critId] = (float) $val / $totalRawWeight;
        }

        $evaluations = AlternativeEvaluation::where('decision_session_id', $session->id)
            ->where('dm_id', auth()->id())
            ->with('alternative')
            ->get();

        if ($evaluations->isEmpty()) return [];

        // Persiapan min-max untuk kriteria numeric bebas (berbasis raw_value)
        $numericStats = [];
        foreach ($evaluations as $eval) {
            $rule = $eval->criteria->scoringRule ?? null;
            if (!$rule || $rule->input_type !== 'numeric') continue;

            // numeric dengan min-max eksplisit dihitung di service transform
            if ($rule->getParameter('value_min') !== null && $rule->getParameter('value_max') !== null) {
                continue;
            }

            $critId = $eval->criteria_id;
            $val = (float) $eval->raw_value;

            if (!isset($numericStats[$critId])) {
                $numericStats[$critId] = ['min' => $val, 'max' => $val];
            } else {
                $numericStats[$critId]['min'] = min($numericStats[$critId]['min'], $val);
                $numericStats[$critId]['max'] = max($numericStats[$critId]['max'], $val);
            }
        }

        $scores = [];
        foreach ($evaluations as $eval) {
            $altId = $eval->alternative_id;
            $sectorId = $eval->alternative->criteria_id ?? null;
            if (!$sectorId || !isset($normalizedWeights[$sectorId])) continue;

            $utility = (float) $eval->utility_value;

            $rule = $eval->criteria->scoringRule ?? null;

            if ($rule && $rule->input_type === 'numeric') {
                // numeric bebas → hitung utility di sini (0–1)
                if ($rule->getParameter('value_min') === null || $rule->getParameter('value_max') === null) {
                    if (isset($numericStats[$eval->criteria_id])) {
                        $min = $numericStats[$eval->criteria_id]['min'];
                        $max = $numericStats[$eval->criteria_id]['max'];

                        if ($max > $min) {
                            $utility = ($eval->raw_value - $min) / ($max - $min);
                        } else {
                            $utility = 1.0;
                        }
                    }
                }
            } else {
                // scale (0–100) → normalisasi ke 0–1
                $utility = $utility / 100;
            }

            $scores[$altId] = ($scores[$altId] ?? 0)
                + ($normalizedWeights[$sectorId] * (float) $utility);
        }

        // Deterministic sorting: smart_score DESC, alternative_id ASC
        uasort($scores, function ($a, $b) use ($scores) {
            if ($a === $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        });

        // Enforce secondary order by alternative_id ASC
        $scores = collect($scores)
            ->sortBy([
                fn($score, $altId) => -$score,
                fn($score, $altId) => $altId,
            ])
            ->all();

        $ranked = [];
        $upsertData = [];
        $rank = 1;

        foreach ($scores as $altId => $score) {
            $finalScore = round($score, 6);
            $ranked[$altId] = [
                'score' => $finalScore,
                'rank' => $rank,
            ];

            if ($persist) {
                $upsertData[] = [
                    'decision_session_id' => $session->id,
                    'dm_id' => $dm->id,
                    'alternative_id' => $altId,
                    'method' => 'smart',
                    'score' => $finalScore,
                    'updated_at' => now(),
                ];
            }
            $rank++;
        }

        if ($persist && !empty($upsertData)) {
            DmScore::upsert($upsertData, ['decision_session_id', 'dm_id', 'alternative_id', 'method'], ['score', 'updated_at']);
        }

        return $ranked;
    }
}
