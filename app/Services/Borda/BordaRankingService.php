<?php

namespace App\Services\Borda;

use App\Models\DecisionSession;
use App\Models\DmScore;
use App\Models\DecisionResult;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BordaRankingService
{
    /**
     * Hitung dan simpan skor Borda untuk satu session keputusan.
     * Mendukung SMART dan SAW sebagai sumber data
     * Menggunakan pendekatan: Skor = n - (posisi_index)
     * * @param DecisionSession $session
     * @param string $source
     * @return array [alternative_id => ['score' => int, 'rank' => int]]
     */
    public function calculateAndPersist(DecisionSession $session, string $source = 'SMART'): array
    {
        $method = strtoupper($source) === 'SAW' ? DmScore::METHOD_SAW : DmScore::METHOD_SMART;

        $results = $session->dmScores()
            ->where('method', $method)
            ->get();

        if ($results->isEmpty()) {
            throw new InvalidArgumentException("Data {$source} belum tersedia untuk sesi ini.");
        }

        // 2. Kelompokkan berdasarkan Decision Maker (DM)
        $groupedByDm = $results->groupBy('dm_id');

        // 3. Proses Kalkulasi dan Persistensi dalam satu Transaksi
        return DB::transaction(function () use ($groupedByDm, $session, $source) {
            $aggregatedScores = [];

            foreach ($groupedByDm as $dmId => $results) {
                $n = $results->count();

                // Urutkan ulang secara koleksi untuk memastikan index 0 adalah skor tertinggi
                $ordered = $results->sortByDesc('score')->values();

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

            foreach ((array) $aggregatedScores as $altId => $score) {
                // Update atau Buat record baru di database
                $signature = md5(
                    $session->id . '|' .
                        $altId . '|' .
                        DecisionResult::SOURCE_BORDA . '|' .
                        DecisionResult::AGGREGATION_FLAT . '|' .
                        ($source === 'SAW'
                            ? DecisionResult::PIPELINE_SAW_BORDA
                            : DecisionResult::PIPELINE_SMART_BORDA)
                );

                DecisionResult::updateOrCreate(
                    ['signature' => $signature],
                    [
                        'decision_session_id' => $session->id,
                        'alternative_id'      => $altId,
                        'source_method'       => DecisionResult::SOURCE_BORDA,
                        'aggregation_method'  => DecisionResult::AGGREGATION_FLAT,
                        'pipeline'            => ($source === 'SAW'
                            ? DecisionResult::PIPELINE_SAW_BORDA
                            : DecisionResult::PIPELINE_SMART_BORDA),
                        'score'               => $score,
                        'rank'                => $rank,
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

    /**
     * Agregasi Borda berdasarkan kategori DM (misal: partisipatif, strategis)
     * Menggunakan hasil SMART (score) yang sudah ada
     */
    public function aggregateByCategory(DecisionSession $session, string $kategori, string $method): array
    {
        // Ambil data SMART dengan eager loading dm.profile
        $smartResults = $session->dmScores()
            ->with(['dm.profile'])
            ->where('method', $method)
            ->get();

        if ($smartResults->isEmpty()) {
            throw new InvalidArgumentException('Data SMART belum tersedia untuk sesi ini.');
        }

        // Filter DM berdasarkan kategori dari user_profiles
        $filtered = $smartResults->filter(function ($row) use ($kategori) {
            return $row->dm && $row->dm->profile?->kategori_dm === $kategori;
        });

        if ($filtered->isEmpty()) {
            return [];
        }

        // Kelompokkan per DM
        $groupedByDm = $filtered->groupBy('dm_id');

        $aggregatedScores = [];

        foreach ($groupedByDm as $dmId => $results) {
            $n = $results->count();

            $ordered = $results->sortByDesc('score')->values();

            foreach ($ordered as $index => $row) {
                $altId = $row->alternative_id;

                if (!isset($aggregatedScores[$altId])) {
                    $aggregatedScores[$altId] = 0;
                }

                $aggregatedScores[$altId] += ($n - $index);
            }
        }

        arsort($aggregatedScores);

        return $aggregatedScores;
    }

    /**
     * Nested Borda Final: agregasi antar kategori
     */
    public function nestedFinal(DecisionSession $session, string $source = 'SMART'): array
    {
        $method = strtoupper($source) === 'SAW'
            ? DmScore::METHOD_SAW
            : DmScore::METHOD_SMART;

        $categories = ['partisipatif', 'strategis', 'teknokratis'];

        $categoryScores = [
            'partisipatif' => [],
            'strategis' => [],
            'teknokratis' => [],
        ];

        // teknokratis dari system_rankings
        $hasRankSystem = DB::getSchemaBuilder()->hasColumn('system_rankings', 'rank_system');
        $orderCol = $hasRankSystem ? 'rank_system' : 'rank';

        $systemRanking = DB::table('system_rankings')
            ->where('decision_session_id', $session->id)
            ->orderBy($orderCol)
            ->get();

        $techScores = [];
        if ($systemRanking && $systemRanking->isNotEmpty()) {
            $n = $systemRanking->count();
            foreach ($systemRanking as $i => $row) {
                $techScores[$row->alternative_id] = $n - $i;
            }
        }
        $categoryScores['teknokratis'] = $techScores;

        // partisipatif & strategis
        foreach (['partisipatif', 'strategis'] as $kategori) {
            try {
                $scores = $this->aggregateByCategory($session, $kategori, $method);
                $categoryScores[$kategori] = is_array($scores) ? $scores : [];
            } catch (\Throwable $e) {
                $categoryScores[$kategori] = [];
            }
        }

        // Build rankings (array of altId ordered)
        $rankings = [];

        // Masyarakat ranking (from partisipatif Borda scores)
        $masyarakat = $categoryScores['partisipatif'] ?? [];
        arsort($masyarakat);
        $rankings['masyarakat'] = array_keys($masyarakat);

        // Kades ranking (direct from single DM with kategori_dm = strategis)
        $kadesScores = $session->dmScores()
            ->where('method', $method)
            ->whereHas('dm.profile', function ($q) {
                $q->where('kategori_dm', 'strategis');
            })
            ->get()
            ->groupBy('alternative_id')
            ->map(fn($rows) => $rows->avg('score'))
            ->sortDesc();

        $rankings['kades'] = array_keys($kadesScores->toArray());
        // Note: do not use aggregateByCategory for kades to avoid Borda distortion.

        // Sistem/Teknokratis ranking (from system_rankings)
        $teknokratis = $categoryScores['teknokratis'] ?? [];
        arsort($teknokratis);
        $rankings['sistem'] = array_keys($teknokratis);

        // Persist PARTISIPATIF only (tracking)
        $partisipatifScores = $categoryScores['partisipatif'] ?? [];
        arsort($partisipatifScores);
        $rankPart = 1;
        foreach ($partisipatifScores as $altId => $score) {
            $signature = md5(
                $session->id . '|' .
                    $altId . '|' .
                    'PARTISIPATIF' . '|' .
                    $source
            );

            DecisionResult::updateOrCreate(
                ['signature' => $signature],
                [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $altId,
                    'source_method'       => DecisionResult::SOURCE_BORDA,
                    'aggregation_method'  => 'CATEGORY',
                    'pipeline'            => $source === 'SAW'
                        ? 'SAW+PARTISIPATIF'
                        : 'SMART+PARTISIPATIF',
                    'score'               => $score,
                    'rank'                => $rankPart,
                ]
            );

            $rankPart++;
        }

        // Final Borda across 3 pillars (equal weight)
        $finalScores = [];

        foreach ($rankings as $name => $rankingList) {
            $n = count($rankingList);
            if ($n === 0) continue;

            foreach ($rankingList as $index => $altId) {
                if (!isset($finalScores[$altId])) {
                    $finalScores[$altId] = 0;
                }
                // Borda points: n - index (index 0 gets n)
                $finalScores[$altId] += ($n - $index);
            }
        }

        arsort($finalScores);

        // Persist final nested Borda results
        $rank = 1;
        foreach ($finalScores as $altId => $score) {
            $signature = md5(
                $session->id . '|' .
                    $altId . '|' .
                    DecisionResult::SOURCE_BORDA . '|' .
                    'NESTED' . '|' .
                    ($source === 'SAW' ? 'SAW+FINAL' : 'SMART+FINAL')
            );

            DecisionResult::updateOrCreate(
                ['signature' => $signature],
                [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $altId,
                    'source_method'       => DecisionResult::SOURCE_BORDA,
                    'aggregation_method'  => 'NESTED',
                    'pipeline'            => $source === 'SAW'
                        ? 'SAW+FINAL'
                        : 'SMART+FINAL',
                    'score'               => $score,
                    'rank'                => $rank,
                ]
            );

            $rank++;
        }

        return $finalScores;
    }
}
