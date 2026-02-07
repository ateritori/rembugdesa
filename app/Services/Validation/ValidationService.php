<?php

namespace App\Services\Validation;

class ValidationService
{
    /**
     * Menghitung koefisien korelasi peringkat Spearman (ρ)
     *
     * @param array $rankSmart [altId => posisi]
     * @param array $rankSaw   [altId => posisi]
     * @return float
     */
    public function calculateSpearmanRho(array $rankSmart, array $rankSaw): float
    {
        $n = count($rankSmart);
        $dSquaredSum = 0.0;

        // TAHAP 1: Hitung jumlah selisih kuadrat (Σ d²)
        foreach ($rankSmart as $altId => $posSmart) {
            if (!isset($rankSaw[$altId])) {
                continue; // abaikan jika alternatif tidak ada
            }

            $posSaw = $rankSaw[$altId];
            $d = $posSmart - $posSaw;
            $dSquaredSum += ($d ** 2);
        }

        // TAHAP 2: Hitung koefisien Spearman
        $denominator = $n * (($n ** 2) - 1);

        if ($denominator == 0) {
            return 0.0;
        }

        $rho = 1 - ((6 * $dSquaredSum) / $denominator);

        return round($rho, 4);
    }
}
