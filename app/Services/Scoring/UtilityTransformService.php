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
            'ordinal' => $this->transformOrdinal($rule, (int) $rawValue),
            'numeric' => $this->transformNumeric($rule, (float) $rawValue),
            default   => throw new InvalidArgumentException('Unsupported input type'),
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
        // Ambil range skala
        $range = json_decode($rule->getParameter('scale_range'), true);
        $min   = (int) ($range['min'] ?? 0);
        $max   = (int) ($range['max'] ?? 0);

        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException('Ordinal value out of range');
        }

        // Ambil utility referensi per skala
        $utilities = json_decode($rule->getParameter('scale_utilities'), true);

        if (! is_array($utilities) || ! array_key_exists($value, $utilities)) {
            throw new InvalidArgumentException('Utility value for ordinal scale not defined');
        }

        return (float) $utilities[$value];
    }

    /* =======================================================
     * NUMERIC (LINEAR NORMALIZATION + PREFERENCE CURVE)
     * =======================================================
     */

    protected function transformNumeric(
        CriteriaScoringRule $rule,
        float $value
    ): float {
        $min = (float) $rule->getParameter('value_min');
        $max = (float) $rule->getParameter('value_max');

        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException('Numeric value out of range');
        }

        // Normalisasi numeric → 0–1
        $normalized = ($value - $min) / max(($max - $min), 0.00001);

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
            'linear'  => $value,
            'concave' => pow($value, $param ?? 0.5),
            'convex'  => pow($value, $param ?? 2.0),
            default   => $value,
        };
    }
}
