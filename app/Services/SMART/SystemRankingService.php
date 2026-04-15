<?php

namespace App\Services\SMART;

use App\Models\DecisionSession;
use App\Models\SystemRanking;
use Illuminate\Support\Facades\Log;

class SystemRankingService
{
    public function calculate(DecisionSession $session, bool $persist = true): array
    {
        $alts = $session->alternatives;

        // 🔍 LOG START
        Log::info('SYSTEM RANKING START', [
            'session_id' => $session->id,
            'total_alternatives' => $alts->count(),
        ]);

        // Get group AHP weights (new structure)
        $groupWeight = $session->groupWeight;

        $sectorWeights = $groupWeight ? $groupWeight->weights : [];

        // 🔥 FIX: pastikan weights array (decode jika masih string JSON)
        if (is_string($sectorWeights)) {
            $sectorWeights = json_decode($sectorWeights, true) ?? [];
        }

        // 🔥 FIX: pastikan key sectorWeights bertipe integer
        $sectorWeights = collect($sectorWeights)
            ->mapWithKeys(fn($v, $k) => [(int) $k => $v])
            ->toArray();

        // 🔥 VALIDASI: pastikan sectorWeights berisi mapping criteria_id => weight
        if (empty($sectorWeights)) {
            throw new \Exception('AHP weights kosong / tidak valid untuk session ini');
        }


        if ($alts->isEmpty()) return [];

        // 1) ambil nilai
        $rab = $alts->pluck('rab');
        $cov = $alts->pluck('coverage');
        $ben = $alts->pluck('beneficiaries');

        // 2) min-max
        $minRab = $rab->min();
        $maxRab = $rab->max();
        $minCov = $cov->min();
        $maxCov = $cov->max();
        $minBen = $ben->min();
        $maxBen = $ben->max();

        // 3) bobot (sementara statis)
        $weights = [
            'rab' => 0.4,          // cost
            'coverage' => 0.3,     // benefit
            'beneficiaries' => 0.3 // benefit
        ];

        $scores = [];

        // 4) hitung utility + score
        foreach ($alts as $alt) {

            // 🔥 RAB = cost (pakai log transform)
            $uRab = 1 - (
                (log(max($alt->rab, 1)) - log(max($minRab, 1))) /
                max((log(max($maxRab, 1)) - log(max($minRab, 1))), 1)
            );

            // 🔥 Coverage = benefit
            $uCov = ($alt->coverage - $minCov) / max(($maxCov - $minCov), 1);

            // 🔥 Beneficiaries = benefit
            $uBen = ($alt->beneficiaries - $minBen) / max(($maxBen - $minBen), 1);


            // skor SMART internal (teknokratis)
            $baseScore =
                $weights['rab'] * $uRab +
                $weights['coverage'] * $uCov +
                $weights['beneficiaries'] * $uBen;

            // 🔥 ambil bobot sektor (AHP)
            $sectorId = (int) $alt->criteria_id;

            if (!array_key_exists($sectorId, $sectorWeights) && !array_key_exists((string)$sectorId, $sectorWeights)) {
                throw new \Exception("Sector weight tidak ditemukan untuk criteria_id: {$sectorId}");
            }

            $sectorWeight = $sectorWeights[$sectorId]
                ?? ($sectorWeights[(string) $sectorId] ?? 0);

            // skor final terbobot
            $score = $baseScore * $sectorWeight;

            // 🔍 LOG PER ITEM
            Log::info('SYSTEM RANKING ITEM', [
                'alt_id' => $alt->id,
                'base_score' => $baseScore,
                'sector_weight' => $sectorWeight,
                'final_score' => $score,
            ]);

            $scores[$alt->id] = [
                'score' => $score,
                'base_score' => $baseScore,
                'sector_weight' => $sectorWeight,
            ];
        }

        // 5) ranking
        uasort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        $rank = 1;
        foreach ($scores as $altId => &$data) {
            $data['rank'] = $rank++;
        }
        unset($data); // 🔥 FIX: clear reference to avoid value leakage

        // 6) simpan (optional)
        if ($persist) {
            SystemRanking::where('decision_session_id', $session->id)->delete();

            foreach ($scores as $altId => $data) {
                SystemRanking::create([
                    'decision_session_id' => $session->id,
                    'alternative_id' => $altId,
                    'smart_score' => $data['score'],
                    'rank_system' => $data['rank'],
                ]);
            }
        }

        return $scores;
    }
}
