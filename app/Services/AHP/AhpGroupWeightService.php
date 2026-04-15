<?php

namespace App\Services\AHP;

use InvalidArgumentException;

class AhpGroupWeightService
{
    /**
     * Aggregate multiple pairwise matrices (AIJ method) and compute group weights.
     */
    public function aggregate(array $matrices): array
    {
        if (empty($matrices)) {
            throw new InvalidArgumentException('Matrices cannot be empty.');
        }

        $this->validateMatrices($matrices);

        $k = count($matrices);
        $n = count($matrices[0]);

        // Step 1: Aggregate matrices using geometric mean (AIJ)
        $aggregated = $this->aggregateMatrices($matrices, $n, $k);

        // Step 2: Compute weights from aggregated matrix
        $weights = $this->calculateWeights($aggregated, $n);

        // Step 3: Compute consistency ratio (CR) from aggregated matrix
        $cr = $this->calculateConsistency($aggregated, $weights, $n);

        return [
            'matrix'        => $aggregated,
            'weights'       => $weights,
            'cr'            => round($cr, 4),
            'is_consistent' => $cr <= 0.1,
        ];
    }

    /**
     * Ensure all matrices are valid n x n.
     */
    private function validateMatrices(array $matrices): void
    {
        $n = count($matrices[0]);

        foreach ($matrices as $matrix) {
            if (count($matrix) !== $n) {
                throw new InvalidArgumentException('Inconsistent matrix size.');
            }

            foreach ($matrix as $row) {
                if (count($row) !== $n) {
                    throw new InvalidArgumentException('Matrix must be square.');
                }
            }
        }
    }

    /**
     * Aggregate matrices using geometric mean (AIJ).
     */
    private function aggregateMatrices(array $matrices, int $n, int $k): array
    {
        $result = [];

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {

                $product = 1.0;

                foreach ($matrices as $matrix) {
                    $value = (float) $matrix[$i][$j];

                    if ($value <= 0) {
                        throw new InvalidArgumentException("Invalid value at [$i][$j].");
                    }

                    $product *= $value;
                }

                $result[$i][$j] = pow($product, 1 / $k);
            }
        }

        return $result;
    }

    /**
     * Compute priority vector using geometric mean.
     */
    private function calculateWeights(array $matrix, int $n): array
    {
        $weights = [];

        for ($i = 0; $i < $n; $i++) {
            $rowProduct = 1.0;

            for ($j = 0; $j < $n; $j++) {
                $rowProduct *= $matrix[$i][$j];
            }

            $weights[$i] = pow($rowProduct, 1 / $n);
        }

        $sum = array_sum($weights);

        if ($sum <= 0) {
            throw new InvalidArgumentException('Invalid weight sum.');
        }

        foreach ($weights as $i => $value) {
            $weights[$i] = $value / $sum;
        }

        return $weights;
    }
    /**
     * Compute Consistency Ratio (CR) for aggregated matrix.
     */
    private function calculateConsistency(array $matrix, array $weights, int $n): float
    {
        $lambdaSum = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0.0;

            for ($j = 0; $j < $n; $j++) {
                $rowSum += $matrix[$i][$j] * $weights[$j];
            }

            if ($weights[$i] > 0) {
                $ratio = $rowSum / $weights[$i];
                if (is_finite($ratio)) {
                    $lambdaSum += $ratio;
                }
            }
        }

        $lambdaMax = $lambdaSum / $n;

        $CI = ($lambdaMax - $n) / ($n - 1);

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

        if ($RI == 0 || $CI < 0) {
            return 0.0;
        }

        return $CI / $RI;
    }
}
