<?php

namespace App\Services\SAW;

use InvalidArgumentException;

/**
 * Service perangkingan SAW.
 * Digunakan sebagai benchmark pembanding.
 */
class SawRankingService
{
    /**
     * Metode 1: Menghasilkan skor dan rank lengkap
     */
    public function calculate(array $alternatives, array $criteriaConfig): array
    {
        if (empty($alternatives) || empty($criteriaConfig)) {
            throw new InvalidArgumentException('Data alternatif atau konfigurasi kriteria kosong.');
        }

        // Transformasi ke format matrix untuk menggunakan fungsi internal yang seragam
        $matrix = [];
        $weights = [];
        $types = [];

        foreach ($criteriaConfig as $cId => $config) {
            $weights[$cId] = $config['weight'];
            $types[$cId] = $config['type'] ?? 'benefit';
        }

        foreach ($alternatives as $alt) {
            $matrix[$alt['alternative_id']] = $alt['values'];
        }

        $scores = $this->calculateFromMatrix($matrix, $weights, $types);

        // Deterministic sorting: SAW score DESC, alternative_id ASC
        $scores = collect($scores)
            ->sortBy([
                fn($score, $altId) => -$score,
                fn($score, $altId) => $altId,
            ])
            ->values()
            ->all();

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
     * Metode 2: Hitung skor langsung (Lebih efisien untuk loop Borda)
     */
    public function calculateFromMatrix(array $matrix, array $weights, array $types = []): array
    {
        if (empty($matrix) || empty($weights)) {
            throw new InvalidArgumentException('Matrix atau bobot kosong.');
        }

        $normalized = [];
        $scores = [];

        // TAHAP 1: Normalisasi per kolom (Kriteria)
        foreach ($weights as $cId => $weight) {
            $column = [];
            foreach ($matrix as $altId => $values) {
                if (isset($values[$cId])) {
                    $column[] = (float) $values[$cId];
                }
            }

            if (empty($column)) continue;

            $max = max($column);
            $min = min($column);
            $type = $types[$cId] ?? 'benefit';

            foreach ($matrix as $altId => $values) {
                $val = (float) ($values[$cId] ?? 0);

                if ($type === 'cost') {
                    // Mencegah Division by Zero jika nilai mentah 0
                    $normalized[$altId][$cId] = ($val > 0) ? ($min / $val) : 0;
                } else {
                    $normalized[$altId][$cId] = ($max > 0) ? ($val / $max) : 0;
                }
            }
        }

        // TAHAP 2: Perkalian Bobot & Penjumlahan
        foreach ($matrix as $altId => $values) {
            $total = 0.0;
            foreach ($weights as $cId => $weight) {
                $nVal = $normalized[$altId][$cId] ?? 0;
                $total += $nVal * (float) $weight;
            }
            $scores[$altId] = round($total, 6);
        }

        return $scores;
    }
}
