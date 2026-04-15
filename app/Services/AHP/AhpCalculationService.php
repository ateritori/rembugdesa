<?php

namespace App\Services\AHP;

class AhpCalculationService
{
    public function calculate(array $matrix): array
    {
        // Normalize matrix to 0-based indexed array
        $M = array_values(array_map(fn($row) => array_values($row), $matrix));
        $n = count($M);

        if ($n < 2) {
            return [
                'weights'       => [],
                'cr'            => 0.0,
                'is_consistent' => true,
            ];
        }

        // Step 1: Compute priority vector using geometric mean
        $W = [];

        for ($i = 0; $i < $n; $i++) {
            $product = 1.0;

            for ($j = 0; $j < $n; $j++) {
                $value = $M[$i][$j] > 0 ? $M[$i][$j] : 1e-9;
                $product *= $value;
            }

            $W[$i] = pow($product, 1 / $n);
        }

        // Normalize weights
        $sum = array_sum($W);
        if ($sum > 0) {
            foreach ($W as $i => $value) {
                $W[$i] = $value / $sum;
            }
        }

        // Step 2: Calculate lambda max
        $lambdaSum = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0.0;

            for ($j = 0; $j < $n; $j++) {
                $rowSum += $M[$i][$j] * $W[$j];
            }

            if ($W[$i] > 0) {
                $ratio = $rowSum / $W[$i];
                if (is_finite($ratio)) {
                    $lambdaSum += $ratio;
                }
            }
        }

        $lambdaMax = $lambdaSum / $n;

        // Step 3: Consistency Index (CI)
        $CI = ($lambdaMax - $n) / ($n - 1);

        // Step 4: Random Index (RI)
        $RI_TABLE = [
            1 => 0.00,
            2 => 0.00,
            3 => 0.58,
            4 => 0.90,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45,
            10 => 1.49,
        ];

        $RI = $RI_TABLE[$n] ?? 1.49;

        // Step 5: Consistency Ratio (CR)
        $CR = ($RI == 0 || $CI < 0) ? 0.0 : ($CI / $RI);

        return [
            'weights'       => $W,
            'cr'            => round($CR, 4),
            'is_consistent' => $CR <= 0.10,
        ];
    }
}
