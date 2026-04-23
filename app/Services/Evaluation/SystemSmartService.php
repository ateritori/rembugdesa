<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use Illuminate\Support\Facades\DB;

class SystemSmartService
{
    public function calculate(DecisionSession $session): void
    {
        $rawScores = DB::table('evaluation_scores')
            ->where('decision_session_id', $session->id)
            ->whereIn('source', ['system', 'human'])
            ->get();

        // Keep human rows per user (NO early average). System remains single.
        $scores = $rawScores
            ->groupBy('criteria_id')
            ->map(function ($items) {
                $grouped = $items->groupBy('alternative_id')->map(function ($rows) {
                    $source = $rows->first()->source;

                    if ($source === 'human') {
                        // return all rows (per user) for this alternative
                        return $rows->map(function ($r) {
                            return (object)[
                                'alternative_id' => $r->alternative_id,
                                'value' => $r->value,
                                // optional: keep user_id for future debugging
                                'user_id' => $r->user_id ?? null,
                            ];
                        });
                    }

                    // system: single value
                    return collect([(object)[
                        'alternative_id' => $rows->first()->alternative_id,
                        'value' => $rows->first()->value,
                    ]]);
                });

                // flatten per-criteria list so downstream loops see each row (including per-user rows)
                return $grouped->flatten(1)->values();
            });

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

            // 🔥 ambil hanya nilai dari system untuk min-max
            $systemValues = $items
                ->filter(fn($r) => !isset($r->user_id)) // system tidak punya user_id
                ->pluck('value');

            // fallback jika tidak ada system (safety)
            $values = $systemValues->isNotEmpty()
                ? $systemValues
                : $items->pluck('value');

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

                // SMART: accumulate utility and count
                if (!isset($aggregatedScores[$item->alternative_id])) {
                    $aggregatedScores[$item->alternative_id] = [
                        'sum' => 0,
                        'count' => 0,
                    ];
                }

                $aggregatedScores[$item->alternative_id]['sum'] += $utility;
                $aggregatedScores[$item->alternative_id]['count']++;
            }
        }

        foreach ($aggregatedScores as $alternativeId => $data) {

            $avg = $data['count'] > 0
                ? $data['sum'] / $data['count']
                : 0;

            DB::table('evaluation_results')->updateOrInsert(
                [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $alternativeId,
                    'method'              => 'smart',
                    'user_id'             => null,
                ],
                [
                    'evaluation_score' => $avg,
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
