<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use Illuminate\Support\Facades\DB;

class SystemSawService
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

        // Prefetch alternatives (avoid N+1)
        $alternatives = DB::table('alternatives')
            ->where('decision_session_id', $session->id)
            ->get()
            ->keyBy('id');

        $aggregatedScores = [];

        foreach ($scores as $criteriaId => $items) {

            $crit = $criteria[$criteriaId];

            $values = $items->pluck('value');
            $min = $values->min();
            $max = $values->max();

            foreach ($items as $item) {

                if ($max == $min) {
                    $normalized = 1;
                } else {
                    if ($crit->type === 'cost') {
                        $normalized = ($max - $item->value) / ($max - $min);
                    } else {
                        $normalized = ($item->value - $min) / ($max - $min);
                    }
                }

                // Ambil bobot sektor (level 1 dari alternative)
                $alt = $alternatives[$item->alternative_id] ?? null;
                $sectorId = $alt->criteria_id ?? null;
                $w = $weights[$sectorId] ?? 1;

                $weighted = $normalized * $w;

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
                    'method'              => 'saw',
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
}
