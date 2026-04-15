<?php

namespace App\Services\Borda;

class BordaService
{
    public function calculate(array $items)
    {
        // sort descending
        usort($items, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $n = count($items);
        $rank = 1;

        $results = [];

        foreach ($items as $item) {

            $results[] = [
                'alternative_id' => $item['alternative_id'],
                'rank' => $rank,
                'borda_score' => $n - $rank + 1,
            ];

            $rank++;
        }

        return $results;
    }
}
