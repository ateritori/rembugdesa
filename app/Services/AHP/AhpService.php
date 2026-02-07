<?php

namespace App\Services\AHP;

class AhpService
{
    public function calculate(array $matrix): array
    {
        // ===============================
        // 0. Normalisasi indeks (0-based)
        // ===============================
        $M = array_values(
            array_map(fn($row) => array_values($row), $matrix)
        );

        $n = count($M);

        if ($n < 2) {
            return [
                'weights'       => [],
                'cr'            => 0.0,
                'is_consistent' => true,
            ];
        }

        // =================================================
        // 1. Priority Vector (EIGENVECTOR / POWER ITERATION)
        //    SAMA PERSIS DENGAN FRONTEND
        // =================================================
        $W = array_fill(0, $n, 1 / $n);
        $maxIter = 100;
        $eps = 1e-8;

        for ($iter = 0; $iter < $maxIter; $iter++) {
            $Wnext = array_fill(0, $n, 0.0);

            // Wnext = M * W
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $Wnext[$i] += $M[$i][$j] * $W[$j];
                }
            }

            // normalisasi
            $sum = array_sum($Wnext);
            if ($sum == 0 || !is_finite($sum)) {
                break;
            }

            for ($i = 0; $i < $n; $i++) {
                $Wnext[$i] /= $sum;
            }

            // cek konvergensi
            $diff = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $diff += abs($Wnext[$i] - $W[$i]);
            }

            $W = $Wnext;

            if ($diff < $eps) {
                break;
            }
        }

        // ===============================
        // 2. Lambda max (Saaty)
        // ===============================
        $lambdaSum = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $rowSum += $M[$i][$j] * $W[$j];
            }

            if ($W[$i] > 0 && is_finite($rowSum / $W[$i])) {
                $lambdaSum += ($rowSum / $W[$i]);
            }
        }

        $lambdaMax = $lambdaSum / $n;

        // ===============================
        // 3. CI (Consistency Index)
        // ===============================
        $CI = ($lambdaMax - $n) / ($n - 1);

        // ===============================
        // 4. RI (Random Index – Saaty)
        // ===============================
        $RI_TABLE = [
            1  => 0.00,
            2  => 0.00,
            3  => 0.58,
            4  => 0.90,
            5  => 1.12,
            6  => 1.24,
            7  => 1.32,
            8  => 1.41,
            9  => 1.45,
            10 => 1.49,
        ];

        $RI = $RI_TABLE[$n] ?? 1.49;

        // ===============================
        // 5. CR (Consistency Ratio)
        // ===============================
        $CR = ($RI == 0 || !is_finite($CI / $RI) || $CI < 0)
            ? 0.0
            : ($CI / $RI);

        return [
            'weights'       => $W,
            'cr'            => round($CR, 4),
            'is_consistent' => $CR <= 0.10,
        ];
    }
}
