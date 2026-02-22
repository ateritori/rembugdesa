<?php

namespace App\Services\SMART;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\AlternativeEvaluation;
use App\Models\Criteria;
use App\Models\CriteriaWeight;

class ExplainableSmartService
{
    public function calculate(DecisionSession $session, User $dm): array
    {
        // 1️⃣ Ambil kriteria aktif
        $criteria = Criteria::where('decision_session_id', $session->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->with(['scoringRule.parameters'])
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

        // 4️⃣ Bentuk matrix (raw + utility dari database)
        $matrix = [];
        foreach ($evaluations as $eval) {
            $matrix[$eval->alternative_id][$eval->criteria_id] = [
                'raw'     => $eval->raw_value,
                'utility' => $eval->utility_value,
            ];
        }


        $result = [];

        // 6️⃣ Hitung SMART
        foreach ($matrix as $altId => $values) {

            $totalScore = 0;

            foreach ($criteria as $critId => $crit) {

                $data = $values[$critId] ?? ['raw' => 0, 'utility' => 0];

                $raw     = $data['raw'];
                $utility = $data['utility'];

                // 🔎 Lookup semantic berdasarkan raw_value
                $semantic = null;

                if ($crit->scoringRule && $crit->scoringRule->parameters) {

                    $semanticsParam = $crit->scoringRule->parameters
                        ->firstWhere('param_key', 'scale_semantics');

                    if ($semanticsParam && is_array($semanticsParam->param_value)) {
                        // Pastikan raw cocok dengan key skala (integer-based scale)
                        $rawKey = (string) (int) $raw;
                        $semantic = $semanticsParam->param_value[$rawKey] ?? null;
                    }
                }

                $weighted = $utility * ($weights[$critId] ?? 0);
                $totalScore += $weighted;

                $result[$altId]['criteria'][$critId] = [
                    'criteria_id'    => $critId,
                    'criteria_name'  => $crit->name,
                    'type'           => $crit->type,
                    'semantic'       => $semantic,
                    'raw'            => $raw,
                    'utility'        => round($utility, 4),
                    'weight'         => $weights[$critId] ?? 0,
                    'weighted_value' => round($weighted, 4),
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
