<?php

namespace App\Services\SAW;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\AlternativeEvaluation;
use App\Models\Criteria;
use App\Models\CriteriaWeight;

class SawLogService
{
    public function calculate(DecisionSession $session, User $dm): array
    {
        // 1️⃣ Ambil kriteria aktif
        $criteria = Criteria::where('decision_session_id', $session->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->keyBy('id');

        if ($criteria->isEmpty()) {
            return [];
        }

        // 2️⃣ Ambil bobot global
        $weightModel = CriteriaWeight::where('decision_session_id', $session->id)
            ->whereNull('dm_id')
            ->first();

        if (!$weightModel) {
            return [];
        }

        $weights = $weightModel->weights;

        // 3️⃣ Ambil evaluasi DM
        $evaluations = AlternativeEvaluation::where('decision_session_id', $session->id)
            ->where('dm_id', $dm->id)
            ->get();

        if ($evaluations->isEmpty()) {
            return [];
        }

        // 4️⃣ Bentuk matrix raw value
        $matrix = [];
        foreach ($evaluations as $eval) {
            $matrix[$eval->alternative_id][$eval->criteria_id] = $eval->raw_value;
        }

        // 5️⃣ Hitung min & max per kriteria (untuk DM ini saja)
        $minValues = [];
        $maxValues = [];

        foreach ($criteria as $critId => $crit) {
            $values = [];

            foreach ($matrix as $altId => $altValues) {
                $values[] = $altValues[$critId] ?? 0;
            }

            $minValues[$critId] = min($values);
            $maxValues[$critId] = max($values);
        }

        $result = [];

        // 6️⃣ Hitung SAW (Min-Max Normalization)
        foreach ($matrix as $altId => $values) {

            $totalScore = 0;

            foreach ($criteria as $critId => $crit) {

                $raw = $values[$critId] ?? 0;

                $min = $minValues[$critId] ?? 0;
                $max = $maxValues[$critId] ?? 0;

                // Tentukan tipe kriteria (benefit / cost)
                $type = $crit->type ?? 'benefit';

                if ($max == 0 && $min == 0) {
                    $normalized = 0;
                } else {
                    if ($type === 'cost') {
                        // cost → min / value
                        $normalized = $raw != 0 ? $min / $raw : 0;
                    } else {
                        // benefit → value / max
                        $normalized = $max != 0 ? $raw / $max : 0;
                    }
                }

                $weighted = $normalized * ($weights[$critId] ?? 0);
                $totalScore += $weighted;

                $result[$altId]['criteria'][$critId] = [
                    'criteria_id' => $critId,
                    'criteria_name' => $crit->name,
                    'type' => $type,
                    'raw' => $raw,
                    'normalized' => round($normalized, 4),
                    'weight' => $weights[$critId] ?? 0,
                    'weighted' => round($weighted, 4),
                ];
            }

            $result[$altId]['total_score'] = round($totalScore, 4);
        }

        // 7️⃣ Ranking
        $scores = collect($result)->pluck('total_score')->toArray();
        arsort($scores);

        $rank = 1;
        foreach ($scores as $altId => $score) {
            $result[$altId]['rank'] = $rank++;
        }

        return $result;
    }
}
