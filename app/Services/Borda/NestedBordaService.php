<?php

namespace App\Services\Borda;

class NestedBordaService
{
    /**
     * Menghitung Nested Borda dengan logika RANK.EQ Excel
     */
    public function calculateFromTraces(array $traces): array
    {
        // ================================
        // 1. Build domainDM dari SMART traces
        // ================================
        $domainDM = [];
        $allGlobalAltIds = []; // Untuk memastikan n (jumlah alternatif) konsisten

        foreach ($traces as $userId => $trace) {
            if (isset($trace['alternatives'])) {
                $alternatives = $trace['alternatives'];
            } elseif (is_array($trace) && isset($trace[0]) && is_array($trace[0]) && isset($trace[0]['alternative_id'])) {
                $alternatives = collect($trace)->keyBy('alternative_id')->toArray();
            } else {
                continue;
            }

            $firstAlt = reset($alternatives);
            $domainId = $trace['domain_id'] ?? ($firstAlt['steps'][0]['domain_id'] ?? (($userId === 'system' || $userId === null) ? 3 : 0));

            foreach ($alternatives as $altId => $altData) {
                if (!isset($altData['final_score'])) continue;

                // Rounding di awal untuk menghindari noise floating point (standard Excel)
                $score = (float) sprintf('%.10f', $altData['final_score']);
                $domainDM[$domainId][$userId][$altId] = $score;

                // Simpan ID alternatif yang muncul secara global
                $allGlobalAltIds[$altId] = true;
            }
        }

        if (empty($domainDM)) {
            throw new \Exception('Borda gagal: domainDM kosong. Pastikan SMART trace memiliki data valid.');
        }

        // ================================
        // 2. Helper RANK.EQ + BORDA
        // ================================
        /**
         * Logika RANK.EQ:
         * Jika ada nilai sama, mereka mendapat ranking yang sama.
         * Contoh: [90, 80, 80, 70] -> Rank: [1, 2, 2, 4]
         */
        $rankAndBorda = function (array $scores) {
            // 1. Sortir berdasarkan ID agar urutan pemrosesan selalu konsisten
            ksort($scores);
            $n = count($scores);

            // 2. Ambil semua value dan urutkan dari BESAR ke KECIL untuk referensi rank
            $allValues = array_map(function ($v) {
                return (float) sprintf('%.10f', $v);
            }, array_values($scores));
            rsort($allValues);

            $borda = [];
            foreach ($scores as $altId => $val) {
                $val = (float) sprintf('%.10f', $val);

                // 3. Logika RANK.EQ: Cari posisi pertama nilai ini di array yang sudah di-sort DESC
                // PHP array index mulai dari 0, jadi rank adalah index + 1
                $rank = 1;
                foreach ($allValues as $index => $v) {
                    if ($v > $val) {
                        $rank++;
                    } else {
                        break; // Karena sudah di-sort DESC, jika tidak lebih besar, hentikan
                    }
                }

                // 4. Rumus Borda: (n - rank + 1)
                // Contoh: n=24, Rank=1 -> Borda=24. Rank=2 -> Borda=23.
                $borda[$altId] = $n - $rank + 1;
            }

            return $borda;
        };

        // ================================
        // 3. Borda antar DM dalam setiap domain
        // ================================
        $domainBorda = [];
        $domainAggregate = [];

        foreach ($domainDM as $domainId => $dms) {
            $aggregate = [];

            foreach ($dms as $userId => $alts) {
                // 🔥 FIX: gunakan BORDA per DM lalu dijumlah (sesuai Nested Borda)
                $borda = $rankAndBorda($alts);

                foreach ($borda as $altId => $val) {
                    $aggregate[$altId] = ($aggregate[$altId] ?? 0) + $val;
                }
            }

            // Finalisasi ranking di level domain (menggunakan RANK.EQ lagi)
            // Penting: ksort agar urutan ID tidak merusak konsistensi saat ada tie
            ksort($aggregate);
            $domainAggregate[$domainId] = $aggregate; // 🔥 simpan agregasi 7 DM
            $domainBorda[$domainId] = $rankAndBorda($aggregate);
        }

        // ================================
        // 4. Borda antar domain (Final Aggregation)
        // ================================
        $finalScores = [];

        foreach ($domainBorda as $domainId => $scores) {
            foreach ($scores as $altId => $val) {
                $finalScores[$altId] = ($finalScores[$altId] ?? 0) + $val;
            }
        }

        // ================================
        // 5. Ranking Akhir — Unique ranking (no tie, no jump) with deterministic epsilon
        // ================================
        // 🔥 Unique ranking (no tie, no jump) with deterministic epsilon
        $adjustedScores = [];

        foreach ($finalScores as $altId => $score) {
            // tambah epsilon kecil berbasis altId agar stabil & deterministik
            $adjustedScores[$altId] = (float) sprintf('%.10f', $score) + ($altId * 1e-12);
        }

        // sort descending secara numerik
        arsort($adjustedScores, SORT_NUMERIC);

        $ranking = [];
        $rank = 1;

        foreach ($adjustedScores as $altId => $adjScore) {
            $ranking[$altId] = [
                'score' => (float) sprintf('%.10f', $finalScores[$altId]), // gunakan score asli
                'rank'  => $rank++,
            ];
        }

        return [
            'final_scores' => $finalScores,
            'ranking' => $ranking,
            'domain_borda' => $domainBorda,
            'domain_dm' => $domainDM,
            'domain_aggregate' => $domainAggregate
        ];
    }
}
