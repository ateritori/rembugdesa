<?php

namespace App\Services\Borda;

use App\Models\BordaAggregation;

class FinalBordaAggregationService
{
    public function calculate($session, $method)
    {
        $method = strtoupper($method);

        // 🔹 ambil semua hasil group + system
        $rows = BordaAggregation::where('decision_session_id', $session->id)
            ->where('method', $method)
            ->whereIn('level', ['group', 'system'])
            ->get()
            ->groupBy('source');

        // 🔹 ambil skor strategis (untuk tie breaker)
        $strategicScores = BordaAggregation::where('decision_session_id', $session->id)
            ->where('method', $method)
            ->where('level', 'group')
            ->where('source', 'strategis')
            ->pluck('borda_score', 'alternative_id');

        $finalScores = [];

        foreach ($rows as $source => $items) {

            foreach ($items as $row) {

                $altId = $row->alternative_id;

                if (!isset($finalScores[$altId])) {
                    $finalScores[$altId] = 0;
                }

                $finalScores[$altId] += $row->borda_score;
            }
        }

        // 🔹 ubah ke array
        $items = [];

        foreach ($finalScores as $altId => $score) {
            $items[] = [
                'alternative_id' => $altId,
                'score' => $score,
            ];
        }

        // 🔥 SORT + TIE BREAKER STRATEGIS
        usort($items, function ($a, $b) use ($strategicScores) {

            // utama: skor final
            if ($b['score'] !== $a['score']) {
                return $b['score'] <=> $a['score'];
            }

            // tie breaker: strategis
            $aStrat = $strategicScores[$a['alternative_id']] ?? 0;
            $bStrat = $strategicScores[$b['alternative_id']] ?? 0;

            return $bStrat <=> $aStrat;
        });

        // 🔹 assign rank + borda
        $n = count($items);
        $rank = 1;

        foreach ($items as $item) {

            BordaAggregation::updateOrCreate(
                [
                    'decision_session_id' => $session->id,
                    'method' => $method,
                    'level' => 'final',
                    'source' => 'final',
                    'alternative_id' => $item['alternative_id'],
                ],
                [
                    'borda_score' => $item['score'],
                    'rank' => $rank,
                ]
            );

            $rank++;
        }
    }
}
