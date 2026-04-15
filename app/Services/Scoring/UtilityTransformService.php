<?php

namespace App\Services\Scoring;

use App\Models\CriteriaScoringRule;
use InvalidArgumentException;

class UtilityTransformService
{
    /**
     * Transform raw value menjadi utility (0–1)
     * Menggunakan definisi utility yang disimpan di criteria_scoring_parameters (JSON)
     */
    public function transform(
        CriteriaScoringRule $rule,
        float|int $rawValue
    ): float {
        return match ($rule->input_type) {
            'scale', 'ordinal' => $this->transformOrdinal($rule, (int) $rawValue),
            'numeric'          => $this->transformNumeric($rule, (float) $rawValue),
            default            => throw new InvalidArgumentException(
                'Unsupported input type: ' . $rule->input_type
            ),
        };
    }

    /* =======================================================
     * ORDINAL (SCALE-BASED, VIA JSON PARAMETER)
     * =======================================================
     */

    protected function transformOrdinal(
        CriteriaScoringRule $rule,
        int $value
    ): float {
        // 1) Ambil range skala (opsional, hanya untuk guard ringan)
        $rangeParam = $rule->getParameter('scale_range');
        $range = is_string($rangeParam)
            ? json_decode($rangeParam, true)
            : (is_array($rangeParam) ? $rangeParam : []);

        if (isset($range['min'], $range['max'])) {
            if ($value < (int) $range['min'] || $value > (int) $range['max']) {
                throw new InvalidArgumentException('Ordinal value out of range');
            }
        }

        // 2) Ambil utilities (WAJIB ADA)
        $utilitiesParam = $rule->getParameter('scale_utilities');
        $utilitiesRaw = is_string($utilitiesParam)
            ? json_decode($utilitiesParam, true)
            : (is_array($utilitiesParam) ? $utilitiesParam : null);

        if (! is_array($utilitiesRaw) || empty($utilitiesRaw)) {
            // Fail-safe: utilities belum didefinisikan → default utility minimum
            return 0.0;
        }

        // 3) Normalisasi key & value utilities
        //    - key -> string
        //    - value -> float
        $utilities = [];
        foreach ($utilitiesRaw as $k => $v) {
            $utilities[(string) $k] = (float) $v;
        }

        // 4) Ambil utility berdasarkan ordinal
        $key = (string) $value;

        // Fallback: jika key tidak ada, tapi hanya satu utility (misalnya skala 1)
        if (! array_key_exists($key, $utilities)) {
            if (count($utilities) === 1) {
                return (float) array_values($utilities)[0];
            }

            return 0.0;
        }

        return $utilities[$key];
    }

    /* =======================================================
     * NUMERIC (LINEAR NORMALIZATION + PREFERENCE CURVE)
     * =======================================================
     */

    protected function transformNumeric(
        CriteriaScoringRule $rule,
        float $value
    ): float {
        // Ambil parameter min & max (boleh null untuk numeric bebas)
        $minParam = $rule->getParameter('value_min');
        $maxParam = $rule->getParameter('value_max');

        // Jika min dan max TIDAK didefinisikan → numeric bebas (placeholder)
        // Utility BELUM dihitung di sini
        // Simpan raw_value sebagai utility sementara
        if ($minParam === null || $maxParam === null) {
            return $value;
        }

        $min = (float) $minParam;
        $max = (float) $maxParam;

        if ($max <= $min) {
            throw new InvalidArgumentException('Invalid numeric range definition');
        }

        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException('Numeric value out of range');
        }

        // Tentukan tipe (benefit / cost) dari relasi kriteria
        $type = $rule->criteria->type ?? 'benefit';

        // Normalisasi numeric → 0–1 (sesuai benefit / cost)
        if ($type === 'cost') {
            $normalized = ($max - $value) / ($max - $min);
        } else {
            $normalized = ($value - $min) / ($max - $min);
        }

        return $this->applyPreference(
            $normalized,
            $rule->preference_type,
            $rule->curve_param
        );
    }

    /* =======================================================
     * PREFERENCE / UTILITY CURVE
     * =======================================================
     */

    protected function applyPreference(
        float $value,
        string $type,
        ?float $param
    ): float {
        $value = max(0, min(1, $value)); // safety clamp

        return match ($type) {
            'linear'  => $value * 100,
            'concave' => pow($value, $param ?? 0.5) * 100,
            'convex'  => pow($value, $param ?? 2.0) * 100,
            default   => $value * 100,
        };
    }
}
