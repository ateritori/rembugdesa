<?php

namespace App\Services\SMART;

use App\Models\{DecisionSession, User, AlternativeEvaluation, CriteriaWeight, SmartResultDm};
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
            ->where('dm_id', $dm->id)->get();

        if ($evaluations->isEmpty()) return [];

        $scores = [];
        foreach ($evaluations as $eval) {
            $altId = $eval->alternative_id;
            $critId = $eval->criteria_id;
            if (!isset($normalizedWeights[$critId])) continue;

            $scores[$altId] = ($scores[$altId] ?? 0) + ($normalizedWeights[$critId] * (float) $eval->utility_value);
        }

        arsort($scores);
        $ranked = [];
        $upsertData = [];
        $rank = 1;

        foreach ($scores as $altId => $score) {
            $finalScore = round($score, 6);
            $ranked[$altId] = ['score' => $finalScore, 'rank' => $rank];
            if ($persist) {
                $upsertData[] = [
                    'decision_session_id' => $session->id,
                    'dm_id' => $dm->id,
                    'alternative_id' => $altId,
                    'smart_score' => $finalScore,
                    'rank_dm' => $rank,
                    'updated_at' => now(),
                ];
            }
            $rank++;
        }

        if ($persist && !empty($upsertData)) {
            SmartResultDm::upsert($upsertData, ['decision_session_id', 'dm_id', 'alternative_id'], ['smart_score', 'rank_dm', 'updated_at']);
        }

        return $ranked;
    }
}
