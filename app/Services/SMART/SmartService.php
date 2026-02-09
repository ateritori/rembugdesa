<?php

namespace App\Services\SMART;

use App\Models\AlternativeEvaluation;
use App\Models\CriteriaWeight;
use InvalidArgumentException;

class SmartRankingService
{
    /**
     * Hitung skor SMART per alternatif
     *
     * @return array [alternative_id => score]
     */
    public function calculate(int $decisionSessionId): array
    {
        // Ambil bobot kriteria kelompok
        $groupWeight = CriteriaWeight::where('decision_session_id', $decisionSessionId)
            ->whereNull('dm_id')
            ->first();

        if (! $groupWeight) {
            throw new InvalidArgumentException('Bobot kriteria kelompok belum tersedia.');
        }

        $weights = $groupWeight->weights; // [criteria_id => weight]

        // Ambil seluruh utilitas penilaian alternatif
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSessionId)
            ->get();

        if ($evaluations->isEmpty()) {
            throw new InvalidArgumentException('Data penilaian alternatif belum tersedia.');
        }

        $scores = [];

        foreach ($evaluations as $eval) {
            $altId = $eval->alternative_id;
            $critId = $eval->criteria_id;

            if (! isset($weights[$critId])) {
                continue; // safety
            }

            $scores[$altId] ??= 0;
            $scores[$altId] += $weights[$critId] * (float) $eval->utility_value;
        }

        return $scores;
    }
}
