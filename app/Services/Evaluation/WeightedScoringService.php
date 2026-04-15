<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;

class WeightedScoringService
{
    protected SmartCalculationService $smartService;

    public function __construct(SmartCalculationService $smartService)
    {
        $this->smartService = $smartService;
    }

    /**
     * Apply AHP sector weights to SMART scores
     */
    public function calculate(DecisionSession $session): array
    {
        // 1. Get SMART scores (level 2)
        $smartScores = $this->smartService->calculate($session);

        // 2. Get AHP group weights (level 1)
        $groupWeight = $session->groupWeight;
        abort_if(!$groupWeight || empty($groupWeight->weights), 400, 'AHP weights not found.');

        $weights = $groupWeight->weights;

        // 3. Load alternatives (to get sector via criteria_id)
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // 4. Apply weighting
        $results = [];

        foreach ($smartScores as $altId => $smartScore) {

            if (!isset($alternatives[$altId])) {
                continue;
            }

            $sectorId = $alternatives[$altId]->criteria_id;

            $weight = $weights[$sectorId] ?? 0;

            $finalScore = $smartScore * $weight;

            $results[] = [
                'alternative_id' => $altId,
                'smart_score'    => round($smartScore, 6),
                'weight'         => $weight,
                'final_score'    => round($finalScore, 6),
            ];
        }

        // 5. Sort descending
        usort($results, fn($a, $b) => $b['final_score'] <=> $a['final_score']);

        return $results;
    }
}
