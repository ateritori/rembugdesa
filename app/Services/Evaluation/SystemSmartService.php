<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use Illuminate\Support\Facades\DB;

class SystemSmartService
{
    public function calculate(DecisionSession $session): void
    {
        $scores = DB::table('evaluation_scores')
            ->where('decision_session_id', $session->id)
            ->where('source', 'system')
            ->get()
            ->groupBy('criteria_id');

        $criteria = DB::table('criteria')
            ->where('decision_session_id', $session->id)
            ->get()
            ->keyBy('id');

        $rules = DB::table('criteria_scoring_rules')
            ->where('decision_session_id', $session->id)
            ->get()
            ->keyBy('criteria_id');

        // Load weights (AHP/group weight)
        $weightRecord = DB::table('criteria_weights')
            ->where('decision_session_id', $session->id)
            ->latest('id')
            ->first();

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

        $weights = collect($weights)
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->all();

        $aggregatedScores = [];

        foreach ($scores as $criteriaId => $items) {

            $rule = $rules[$criteriaId] ?? null;
            $crit = $criteria[$criteriaId];

            $values = $items->pluck('value');
            $min = $values->min();
            $max = $values->max();

            foreach ($items as $item) {

                // NORMALISASI
                if ($max == $min) {
                    $normalized = 1;
                } else {
                    if ($crit->type === 'cost') {
                        $normalized = ($max - $item->value) / ($max - $min);
                    } else {
                        $normalized = ($item->value - $min) / ($max - $min);
                    }
                }

                // UTILITY
                $utility = $this->utility($normalized, $rule);

                // Ambil bobot sektor (level 1) dari alternative
                $alt = DB::table('alternatives')->where('id', $item->alternative_id)->first();
                $sectorId = $alt->criteria_id ?? null;
                $w = $weights[$sectorId] ?? 1;

                $weighted = $utility * $w;

                if (!isset($aggregatedScores[$item->alternative_id])) {
                    $aggregatedScores[$item->alternative_id] = 0;
                }
                $aggregatedScores[$item->alternative_id] += $weighted;
            }
        }

        foreach ($aggregatedScores as $alternativeId => $score) {
            DB::table('evaluation_aggregations')->updateOrInsert(
                [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $alternativeId,
                    'method'              => 'smart',
                    'user_id'             => null,
                ],
                [
                    'score'      => $score,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    protected function utility($x, $rule)
    {
        if (!$rule) return $x;

        switch ($rule->utility_function) {
            case 'concave':
                return pow($x, $rule->curve_degree ?? 0.5);

            case 'convex':
                return pow($x, $rule->curve_degree ?? 2);

            default:
                return $x;
        }
    }
}
