<?php

namespace App\Services\BORDA;

class BordaService
{
    /**
     * Agregasi preferensi kelompok menggunakan metode Borda
     *
     * @param array $allRankings  Array ranking dari setiap DM
     *                            Contoh:
     *                            [
     *                              ['A1','A2','A3'],
     *                              ['A2','A1','A3'],
     *                              ['A1','A3','A2'],
     *                            ]
     * @param int $candidateCount Jumlah alternatif
     * @return array Skor Borda terurut (descending)
     */
    public function aggregateVotes(array $allRankings, int $candidateCount): array
    {
        $bordaScores = [];

        // Iterasi setiap Decision Maker
        foreach ($allRankings as $rankingList) {

            // Iterasi ranking milik satu DM
            foreach ($rankingList as $rankIndex => $candidateId) {

                // Skema Borda: poin = N - posisi
                $points = $candidateCount - $rankIndex;

                if (!isset($bordaScores[$candidateId])) {
                    $bordaScores[$candidateId] = 0;
                }

                $bordaScores[$candidateId] += $points;
            }
        }

        // Urutkan skor dari tertinggi ke terendah
        arsort($bordaScores);

        return $bordaScores;
    }
}
