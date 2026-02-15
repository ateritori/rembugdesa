<?php

namespace App\Services\SAW;

use InvalidArgumentException;

/**
 * Service perangkingan SAW.
 * Digunakan sebagai benchmark pembanding,
 * tidak menghasilkan keputusan final dan tidak dipersist.
 */
class SawRankingService
{
    /**
     * Proses pembobotan dan perangkingan SAW (benchmark).
     *
     * @param array $alternatives
     *   [
     *     [
     *       'alternative_id' => int,
     *       'values' => [criteria_id => float]
     *     ]
     *   ]
     *
     * @param array $criteriaConfig
     *   [
     *     criteria_id => [
     *       'weight' => float,
     *       'type'   => 'benefit'|'cost'
     *     ]
     *   ]
     *
     * @return array
     *   [
     *     alternative_id => [
     *       'score' => float,
     *       'rank'  => int
     *     ]
     *   ]
     */
    public function calculate(
        array $alternatives,
        array $criteriaConfig
    ): array {
        if (empty($alternatives) || empty($criteriaConfig)) {
            throw new InvalidArgumentException('Data alternatif atau konfigurasi kriteria kosong.');
        }

        $normalized = [];
        $scores = [];

        /* =========================
         * TAHAP 1: Normalisasi
         * ========================= */
        foreach ($criteriaConfig as $cId => $criterion) {
            $columnValues = [];

            foreach ($alternatives as $alt) {
                if (! isset($alt['values'][$cId])) {
                    continue;
                }
                $columnValues[] = $alt['values'][$cId];
            }

            if (empty($columnValues)) {
                continue;
            }

            $max = max($columnValues);
            $min = min($columnValues);

            foreach ($alternatives as $alt) {
                $altId = $alt['alternative_id'];
                $val   = $alt['values'][$cId] ?? 0;

                if ($criterion['type'] === 'cost') {
                    $normalized[$altId][$cId] =
                        ($val > 0 && $min > 0) ? ($min / $val) : 0;
                } else {
                    $normalized[$altId][$cId] =
                        ($max > 0) ? ($val / $max) : 0;
                }
            }
        }

        /* =========================
         * TAHAP 2: Agregasi Bobot
         * ========================= */
        foreach ($normalized as $altId => $values) {
            $total = 0;
            foreach ($values as $cId => $nVal) {
                if (! isset($criteriaConfig[$cId]['weight'])) {
                    continue;
                }
                $total += $nVal * (float) $criteriaConfig[$cId]['weight'];
            }
            $scores[$altId] = round($total, 6);
        }

        /* =========================
         * TAHAP 3: Ranking
         * ========================= */
        arsort($scores);

        $ranked = [];
        $rank = 1;
        foreach ($scores as $altId => $score) {
            $ranked[$altId] = [
                'score' => $score,
                'rank'  => $rank++,
            ];
        }

        return $ranked;
    }

    /**
     * Hitung skor SAW langsung dari matrix raw_value.
     * Digunakan untuk ANALISIS (benchmark), tidak dipersist.
     *
     * @param array $matrix
     *   [
     *     alternative_id => [
     *       criteria_id => raw_value
     *     ]
     *   ]
     *
     * @param array $weights
     *   [
     *     criteria_id => bobot_ahp
     *   ]
     *
     * @param array $types
     *   [
     *     criteria_id => 'benefit'|'cost'
     *   ]
     *
     * @return array
     *   [
     *     alternative_id => saw_score
     *   ]
     */
    public function calculateFromMatrix(
        array $matrix,
        array $weights,
        array $types = []
    ): array {
        if (empty($matrix) || empty($weights)) {
            throw new InvalidArgumentException('Matrix atau bobot kosong.');
        }

        $normalized = [];
        $scores = [];

        // =========================
        // TAHAP 1: Normalisasi SAW
        // =========================
        foreach ($weights as $cId => $weight) {
            $column = [];

            foreach ($matrix as $altId => $values) {
                if (isset($values[$cId])) {
                    $column[] = $values[$cId];
                }
            }

            if (empty($column)) {
                continue;
            }

            $max = max($column);
            $min = min($column);

            foreach ($matrix as $altId => $values) {
                $val = $values[$cId] ?? 0;
                $type = $types[$cId] ?? 'benefit';

                if ($type === 'cost') {
                    $normalized[$altId][$cId] =
                        ($val > 0 && $min > 0) ? ($min / $val) : 0;
                } else {
                    $normalized[$altId][$cId] =
                        ($max > 0) ? ($val / $max) : 0;
                }
            }
        }

        // =========================
        // TAHAP 2: Agregasi Bobot
        // =========================
        foreach ($normalized as $altId => $values) {
            $total = 0.0;

            foreach ($values as $cId => $nVal) {
                if (! isset($weights[$cId])) {
                    continue;
                }
                $total += $nVal * (float) $weights[$cId];
            }

            $scores[$altId] = round($total, 6);
        }

        return $scores;
    }
}
