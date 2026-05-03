<?php

namespace App\Services\Analysis;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DecisionInsightService
{
    public function build($decisionSession, $userId, $smartTraces, $smartBorda, $bordaService)
    {
        // Default structure
        $insight = [
            'changes' => [],
            'top' => null,
            'has_changes' => false,
            'note' => null,
            'preference' => [],
            'spearman' => null,
            'label' => null,
        ];

        if (empty($smartTraces) || is_null($smartBorda)) {
            return $insight;
        }

        try {

            // 🔥 Rank Average function (tie-aware)
            $computeRankAvg = function ($scores) {
                arsort($scores);
                $ranks = [];
                $i = 1;

                while (!empty($scores)) {
                    $value = current($scores);

                    $ties = array_keys($scores, $value, true);
                    $count = count($ties);

                    $avgRank = ($i + ($i + $count - 1)) / 2;

                    foreach ($ties as $key) {
                        $ranks[$key] = $avgRank;
                        unset($scores[$key]);
                    }

                    $i += $count;
                }

                return $ranks;
            };

            // =========================================
            // 🔥 LOO (Leave-One-Out) Analysis
            // =========================================

            $smartTracesWithout = $smartTraces;
            unset($smartTracesWithout[$userId]);

            if (!empty($smartTracesWithout)) {

                $smartBordaWithout = $bordaService->calculateFromTraces($smartTracesWithout);

                $scoreFull = collect($smartBorda['ranking'])->pluck('score', 'alternative_id')->toArray();
                $scoreWithout = collect($smartBordaWithout['ranking'])->pluck('score', 'alternative_id')->toArray();

                $rankFull = $computeRankAvg($scoreFull);
                $rankWithout = $computeRankAvg($scoreWithout);

                $changes = [];

                foreach ($rankFull as $altId => $rFull) {
                    $rWithout = $rankWithout[$altId] ?? null;

                    if (!is_null($rWithout)) {
                        $diff = $rWithout - $rFull;

                        if ($diff != 0) {
                            $changes[] = [
                                'alternative_id' => $altId,
                                'diff' => $diff,
                            ];
                        }
                    }
                }

                $changes = collect($changes)
                    ->sortByDesc(fn($x) => abs($x['diff']))
                    ->values()
                    ->take(3)
                    ->toArray();

                $topImpact = $changes[0] ?? null;

                $insight['changes'] = $changes;
                $insight['top'] = $topImpact;
                $insight['has_changes'] = !empty($changes);
                $insight['note'] = empty($changes)
                    ? 'Perubahan kecil pada penilaian tidak memengaruhi peringkat akhir karena terdapat nilai yang sama (ties) antar alternatif.'
                    : null;
            }

            // =========================================
            // 🔥 Preference (DM vs Final)
            // =========================================

            $dmTrace = collect($smartTraces[$userId] ?? []);

            $dmScores = $dmTrace
                ->pluck('final_score', 'alternative_id')
                ->toArray();

            if (!empty($dmScores)) {

                $rankDm = $computeRankAvg($dmScores);

                $scoreFinal = collect($smartBorda['ranking'])
                    ->pluck('score', 'alternative_id')
                    ->toArray();

                $rankFinal = $computeRankAvg($scoreFinal);

                $preferenceDiff = [];

                foreach ($rankDm as $altId => $rDm) {
                    $rFinal = $rankFinal[$altId] ?? null;

                    if (!is_null($rFinal)) {
                        $diffPref = $rFinal - $rDm;

                        if ($diffPref != 0) {
                            $preferenceDiff[] = [
                                'alternative_id' => $altId,
                                'diff' => $diffPref,
                                'rank_dm' => $rDm,
                                'rank_final' => $rFinal,
                            ];
                        }
                    }
                }

                $preferenceDiff = collect($preferenceDiff)
                    ->sortByDesc(fn($x) => abs($x['diff']))
                    ->values()
                    ->take(3)
                    ->toArray();

                $insight['preference'] = $preferenceDiff;

                // =========================================
                // 🔥 Spearman Correlation
                // =========================================

                $n = count($rankDm);
                $sum_d2 = 0;

                foreach ($rankDm as $altId => $r1) {
                    $r2 = $rankFinal[$altId] ?? null;

                    if (!is_null($r2)) {
                        $d = $r1 - $r2;
                        $sum_d2 += pow($d, 2);
                    }
                }

                $spearman = $n > 1
                    ? 1 - ((6 * $sum_d2) / ($n * (($n * $n) - 1)))
                    : null;

                $label = match (true) {
                    $spearman >= 0.8 => 'Sangat sejalan',
                    $spearman >= 0.6 => 'Cukup sejalan',
                    $spearman >= 0.4 => 'Sedang',
                    default => 'Rendah',
                };

                $insight['spearman'] = $spearman;
                $insight['label'] = $label;
            }
        } catch (\Exception $e) {
            Log::error('DecisionInsightService Error: ' . $e->getMessage());
        }

        return $insight;
    }
}
