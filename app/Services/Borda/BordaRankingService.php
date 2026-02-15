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
     * Hitung dan simpan skor Borda untuk satu session keputusan.
     * Menggunakan pendekatan: Skor = n - (posisi_index)
     * * @param DecisionSession $session
     * @return array [alternative_id => ['score' => int, 'rank' => int]]
     */
    public function calculateAndPersist(DecisionSession $session): array
    {
        // 1. Ambil data SMART, pastikan sudah ada rank_dm
        $smartResults = SmartResultDm::where('decision_session_id', $session->id)
            ->orderBy('rank_dm')
            ->get();

        if ($smartResults->isEmpty()) {
            throw new InvalidArgumentException('Data SMART belum tersedia untuk sesi ini.');
        }

        // 2. Kelompokkan berdasarkan Decision Maker (DM)
        $groupedByDm = $smartResults->groupBy('dm_id');
        $bordaScores = [];

        // 3. Proses Kalkulasi dan Persistensi dalam satu Transaksi
        return DB::transaction(function () use ($groupedByDm, $session) {
            $aggregatedScores = [];

            foreach ($groupedByDm as $dmId => $results) {
                $n = $results->count();

                // Urutkan ulang secara koleksi untuk memastikan index 0 adalah rank_dm terkecil
                $ordered = $results->sortBy('rank_dm')->values();

                foreach ($ordered as $index => $row) {
                    $altId = $row->alternative_id;

                    if (!isset($aggregatedScores[$altId])) {
                        $aggregatedScores[$altId] = 0;
                    }

                    /**
                     * Metodologi Borda:
                     * Jika n = 5, peringkat 1 (index 0) dapat 5 poin, peringkat 5 (index 4) dapat 1 poin.
                     */
                    $aggregatedScores[$altId] += ($n - $index);
                }
            }

            // 4. Urutkan dari skor tertinggi ke terendah
            arsort($aggregatedScores);

            $finalResults = [];
            $rank = 1;

            foreach ($aggregatedScores as $altId => $score) {
                // Update atau Buat record baru di database
                BordaResult::updateOrCreate(
                    [
                        'decision_session_id' => $session->id,
                        'alternative_id'      => $altId,
                    ],
                    [
                        'borda_score' => $score,
                        'final_rank'  => $rank,
                    ]
                );

                // Masukkan ke array output (tanpa query ulang ke DB)
                $finalResults[$altId] = [
                    'score' => $score,
                    'rank'  => $rank,
                ];

                $rank++;
            }

            return $finalResults;
        });
    }
}
