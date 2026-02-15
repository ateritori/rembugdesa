<?php

namespace App\Services\SMART;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\AlternativeEvaluation;
use App\Models\CriteriaWeight;
use App\Models\SmartResultDm;
use InvalidArgumentException;

class SmartRankingService
{
    /**
     * Hitung SMART per DM.
     * Opsional: simpan atau update ke tabel smart_results_dm.
     *
     * @return array [alternative_id => ['score' => float, 'rank' => int]]
     */
    public function calculate(
        DecisionSession $session,
        User $dm,
        bool $persist = false
    ): array {
        // 1. Ambil bobot kriteria kelompok
        $groupWeight = CriteriaWeight::where('decision_session_id', $session->id)
            ->whereNull('dm_id')
            ->first();

        if (! $groupWeight) {
            throw new InvalidArgumentException('Bobot kriteria kelompok belum tersedia.');
        }

        $weights = $groupWeight->weights;

        // 2. Ambil penilaian DM
        $evaluations = AlternativeEvaluation::where('decision_session_id', $session->id)
            ->where('dm_id', $dm->id)
            ->get();

        if ($evaluations->isEmpty()) {
            return [];
        }

        // 3. Hitung skor SMART
        $scores = [];

        foreach ($evaluations as $eval) {
            $altId  = $eval->alternative_id;
            $critId = $eval->criteria_id;

            if (! isset($weights[$critId])) {
                continue;
            }

            $scores[$altId] ??= 0;
            $scores[$altId] +=
                $weights[$critId] * (float) $eval->utility_value;
        }

        // 4. Ranking
        arsort($scores);

        $ranked = [];
        $rank = 1;

        foreach ($scores as $altId => $score) {
            $ranked[$altId] = [
                'score' => round($score, 6),
                'rank'  => $rank++,
            ];
        }

        // 5. Persist (opsional)
        if ($persist) {
            foreach ($ranked as $altId => $data) {
                SmartResultDm::updateOrCreate(
                    [
                        'decision_session_id' => $session->id,
                        'dm_id'               => $dm->id,
                        'alternative_id'      => $altId,
                    ],
                    [
                        'smart_score' => $data['score'],
                        'rank_dm'     => $data['rank'],
                    ]
                );
            }
        }

        return $ranked;
    }
}
