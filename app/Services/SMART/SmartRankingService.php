<?php

namespace App\Services\SMART;

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
    public function calculate(int $decisionSessionId, int $dmId): array
    {
        // Ambil bobot kelompok
        $groupWeight = CriteriaWeight::where('decision_session_id', $decisionSessionId)
            ->whereNull('dm_id')
            ->first();

        if (! $groupWeight) {
            throw new InvalidArgumentException('Bobot kriteria kelompok belum tersedia.');
        }

        $weights = $groupWeight->weights;

        // 🔥 FILTER DM (INI KUNCI)
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSessionId)
            ->where('dm_id', $dmId)
            ->get();

        if ($evaluations->isEmpty()) {
            throw new InvalidArgumentException('Penilaian DM belum tersedia.');
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
