<?php

namespace App\Services\SAW;

class SawService
{
    /**
     * Proses pembobotan dan perangkingan SAW
     */
    public function processBenchmark(array $alternatives, array $criteriaConfig): array
    {
        $normalized = [];
        $scores = [];

        /* =========================
         * TAHAP 1: Normalisasi
         * ========================= */
        foreach ($criteriaConfig as $cId => $criterion) {
            $columnValues = array_column(
                array_map(fn($a) => $a['values'], $alternatives),
                $cId
            );

            $max = max($columnValues);
            $min = min($columnValues);

            foreach ($alternatives as $alt) {
                $altId = $alt['alternative_id'];
                $val   = $alt['values'][$cId];

                if ($criterion['type'] === 'cost') {
                    $normalized[$altId][$cId] = ($val > 0) ? ($min / $val) : 0;
                } else {
                    $normalized[$altId][$cId] = ($max > 0) ? ($val / $max) : 0;
                }
            }
        }

        /* =========================
         * TAHAP 2: Agregasi Bobot
         * ========================= */
        foreach ($normalized as $altId => $values) {
            $total = 0;
            foreach ($values as $cId => $nVal) {
                $total += $nVal * $criteriaConfig[$cId]['weight'];
            }
            $scores[$altId] = $total;
        }

        arsort($scores);
        return $scores;
    }
}
