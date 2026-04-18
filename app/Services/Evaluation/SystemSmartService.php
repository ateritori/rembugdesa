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

                // SMART murni (tanpa bobot sektor)
                $score = $utility;

                if (!isset($aggregatedScores[$item->alternative_id])) {
                    $aggregatedScores[$item->alternative_id] = 0;
                }

                $aggregatedScores[$item->alternative_id] += $score;
            }
        }

        // Simpan sebagai hasil SMART (siap untuk weighted)
        foreach ($aggregatedScores as $alternativeId => $score) {
            DB::table('evaluation_results')->updateOrInsert(
                [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $alternativeId,
                    'method'              => 'smart',
                    'user_id'             => null,
                ],
                [
                    'evaluation_score' => $score,
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
