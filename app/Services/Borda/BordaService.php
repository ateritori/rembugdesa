<?php

namespace App\Services\Borda;

use InvalidArgumentException;

class BordaRankingService
{
    /**
     * Hitung skor Borda dari skor SMART
     *
     * @param array $smartScores [alternative_id => score]
     * @return array [alternative_id => borda_score]
     */
    public function calculate(array $smartScores): array
    {
        if (empty($smartScores)) {
            throw new InvalidArgumentException('Skor SMART kosong.');
        }

        // Urutkan alternatif berdasarkan skor SMART (desc)
        arsort($smartScores);

        $n = count($smartScores);
        $bordaScores = [];

        $rank = 0;
        foreach ($smartScores as $altId => $score) {
            // Skor Borda: (n - rank)
            $bordaScores[$altId] = $n - $rank;
            $rank++;
        }

        return $bordaScores;
    }

    /**
     * Ambil peringkat akhir dari skor Borda
     *
     * @param array $bordaScores
     * @return array [alternative_id => rank]
     */
    public function ranking(array $bordaScores): array
    {
        arsort($bordaScores);

        $ranking = [];
        $rank = 1;

        foreach ($bordaScores as $altId => $score) {
            $ranking[$altId] = $rank++;
        }

        return $ranking;
    }
}
