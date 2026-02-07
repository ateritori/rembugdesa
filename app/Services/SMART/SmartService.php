<?php

namespace App\Services\SMART;

class SmartService
{
    private function calculateUtility(float $val, array $criterionMeta): float
    {
        if (!empty($criterionMeta['custom_map'])) {
            return $criterionMeta['custom_map'][(int) $val] ?? 0.0;
        }

        $min = $criterionMeta['min'];
        $max = $criterionMeta['max'];

        if ($max == $min) {
            return 1.0;
        }

        if (($criterionMeta['type'] ?? 'benefit') === 'cost') {
            return ($max - $val) / ($max - $min);
        }

        return ($val - $min) / ($max - $min);
    }

    public function processScoring(array $alternatives, array $criteriaConfig): array
    {
        foreach ($alternatives as &$alt) {
            $totalScore = 0.0;

            foreach ($alt['values'] as $cId => $rawValue) {
                $utility = $this->calculateUtility(
                    (float) $rawValue,
                    $criteriaConfig[$cId]
                );

                $totalScore += $utility * $criteriaConfig[$cId]['weight'];
            }

            $alt['final_score'] = round($totalScore, 4);
        }

        usort(
            $alternatives,
            fn($a, $b) =>
            $b['final_score'] <=> $a['final_score']
        );

        return $alternatives;
    }
}
