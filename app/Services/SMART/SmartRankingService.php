<?php

namespace App\Services\SMART;

use App\Models\DecisionSession;
use App\Models\User;

use App\Models\AlternativeEvaluation;
use App\Models\CriteriaWeight;
use InvalidArgumentException;

class SmartRankingService
{
    /**
     * Hitung skor SMART per alternatif untuk SATU DM
     *
     * @return array [alternative_id => score]
     */
    public function calculate(DecisionSession $decisionSession, User $dm): array
    {
        // Ambil bobot kelompok
        $groupWeight = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')
            ->first();

        if (! $groupWeight) {
            throw new InvalidArgumentException('Bobot kriteria kelompok belum tersedia.');
        }

        $weights = $groupWeight->weights;

        // 🔥 FILTER DM (INI KUNCI)
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $dm->id)
            ->get();

        if ($evaluations->isEmpty()) {
            return [];
        }

        $scores = [];

        foreach ($evaluations as $eval) {
            $altId  = $eval->alternative_id;
            $critId = $eval->criteria_id;

            if (! isset($weights[$critId])) {
                continue;
            }

            $scores[$altId] ??= 0;
            $scores[$altId] += $weights[$critId] * (float) $eval->utility_value;
        }

        return $scores;
    }
}
