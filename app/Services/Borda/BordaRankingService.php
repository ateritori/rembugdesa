<?php

namespace App\Services\Borda;

use App\Models\DecisionSession;
use App\Models\SmartResultDm;
use App\Models\BordaResult;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BordaRankingService
{
    /**
     * Hitung dan simpan skor Borda untuk satu session keputusan
     *
     * @return array [alternative_id => ['score' => int, 'rank' => int]]
     */
    public function calculateAndPersist(DecisionSession $session): array
    {
        // Ambil semua hasil SMART per DM
        $smartResults = SmartResultDm::where('decision_session_id', $session->id)
            ->get();

        if ($smartResults->isEmpty()) {
            throw new InvalidArgumentException('Data SMART belum tersedia.');
        }

        // Group per DM
        $groupedByDm = $smartResults->groupBy('dm_id');

        $bordaScores = [];

        DB::transaction(function () use ($groupedByDm, $session, &$bordaScores) {

            foreach ($groupedByDm as $dmId => $results) {
                $n = $results->count();

                // Urutkan berdasarkan rank SMART
                $ordered = $results->sortBy('rank_dm')->values();

                foreach ($ordered as $index => $row) {
                    $altId = $row->alternative_id;

                    // Skor Borda: n - posisi
                    $bordaScores[$altId] ??= 0;
                    $bordaScores[$altId] += ($n - $index);
                }
            }

            // Urutkan skor Borda total
            arsort($bordaScores);

            // Simpan hasil
            $rank = 1;
            foreach ($bordaScores as $altId => $score) {
                BordaResult::updateOrCreate(
                    [
                        'decision_session_id' => $session->id,
                        'alternative_id'      => $altId,
                    ],
                    [
                        'borda_score' => $score,
                        'final_rank'  => $rank++,
                    ]
                );
            }
        });

        // Return hasil untuk kebutuhan UI / logging
        $final = [];
        foreach ($bordaScores as $altId => $score) {
            $final[$altId] = [
                'score' => $score,
                'rank'  => BordaResult::where('decision_session_id', $session->id)
                    ->where('alternative_id', $altId)
                    ->value('final_rank'),
            ];
        }

        return $final;
    }
}
