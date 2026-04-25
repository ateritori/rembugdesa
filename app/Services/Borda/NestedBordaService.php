<?php

namespace App\Services\Borda;

class NestedBordaService
{
    /**
     * Compute Nested Borda scores from SMART traces.
     *
     * Steps:
     * 1) Build domain decision-makers (DM) matrices from traces.
     * 2) For each DM, convert scores to Borda points using RANK.AVG.
     * 3) Aggregate Borda within each domain.
     * 4) Convert domain aggregates to Borda and sum across domains.
     * 5) Produce a final deterministic ranking (no ties).
     *
     * @param array $traces
     * @return array{
     *   final_scores: array<int,float>,
     *   ranking: array<int,array{score: float, rank: int}>,
     *   domain_borda: array<int,array<int,float>>,
     *   domain_dm: array<int,array<int,array<int,float>>>,
     *   domain_aggregate: array<int,array<int,float>>
     * }
     */
    public function calculateFromTraces(array $traces): array
    {
        // ================================
        // 1. Build domainDM from SMART traces
        // ================================
        $domainDM = [];
        $allGlobalAltIds = []; // Track all alternative IDs seen globally (kept for completeness)

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

                // Normalize precision to reduce floating-point noise
                $score = (float) sprintf('%.10f', $altData['final_score']);
                $domainDM[$domainId][$userId][$altId] = $score;

                // Record global alternative IDs
                $allGlobalAltIds[$altId] = true;
            }
        }

        if (empty($domainDM)) {
            throw new \Exception('Borda failed: domainDM is empty. Ensure SMART trace has valid data.');
        }

        // ================================
        // 2. Helper: compute Borda points using RANK.AVG (ties receive average rank)
        // Example: [90, 80, 80, 70] -> ranks [1, 2.5, 2.5, 4]
        // ================================
        $rankAndBorda = function (array $scores) {
            // Ensure deterministic processing order by key
            ksort($scores);
            $n = count($scores);

            // Build sorted (desc) value list for ranking reference
            $allValues = array_map(function ($v) {
                return (float) sprintf('%.10f', $v);
            }, array_values($scores));
            rsort($allValues);

            $borda = [];
            foreach ($scores as $altId => $val) {
                $val = (float) sprintf('%.10f', $val);

                // RANK.AVG: calculate average position for tied values
                $positions = [];
                foreach ($allValues as $index => $v) {
                    if ($v == $val) {
                        $positions[] = $index + 1; // positions start at 1
                    }
                }

                // average position
                $rank = array_sum($positions) / count($positions);

                // Borda score: (n - rank + 1)
                $borda[$altId] = $n - $rank + 1;
            }

            return $borda;
        };

        // ================================
        // 3. Borda among DMs within each domain
        // ================================
        $domainBorda = [];
        $domainAggregate = [];

        foreach ($domainDM as $domainId => $dms) {
            $aggregate = [];

            foreach ($dms as $userId => $alts) {
                // Compute Borda per DM, then aggregate within the domain
                $borda = $rankAndBorda($alts);

                foreach ($borda as $altId => $val) {
                    $aggregate[$altId] = ($aggregate[$altId] ?? 0) + $val;
                }
            }

            // Finalize domain-level Borda from aggregated scores (deterministic order)
            ksort($aggregate);
            $domainAggregate[$domainId] = $aggregate;
            $domainBorda[$domainId] = $rankAndBorda($aggregate);
        }

        // ================================
        // 4. Borda among domains (Final Aggregation)
        // ================================
        $finalScores = [];

        foreach ($domainBorda as $domainId => $scores) {
            foreach ($scores as $altId => $val) {
                $finalScores[$altId] = ($finalScores[$altId] ?? 0) + $val;
            }
        }

        // ================================
        // 5. Final ranking (deterministic, no ties)
        // ================================
        $adjustedScores = [];

        foreach ($finalScores as $altId => $score) {
            // Add a tiny epsilon based on altId to break ties deterministically
            $adjustedScores[$altId] = (float) sprintf('%.10f', $score) + ($altId * 1e-12);
        }

        // Sort descending (numeric)
        arsort($adjustedScores, SORT_NUMERIC);

        $ranking = [];
        $rank = 1;

        foreach ($adjustedScores as $altId => $adjScore) {
            $ranking[$altId] = [
                'score' => (float) sprintf('%.10f', $finalScores[$altId]), // Keep original (unadjusted) score
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
