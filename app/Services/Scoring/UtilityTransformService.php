<?php

namespace App\Services\Scoring;

use App\Models\CriteriaScoringRule;
use InvalidArgumentException;

class UtilityTransformService
{
    /**
     * Transform raw value menjadi utility (0–1)
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
     * ORDINAL
     * =======================================================
     */

    protected function transformOrdinal(
        CriteriaScoringRule $rule,
        int $value
    ): float {
        $min = (int) $rule->getParameter('scale_min');
        $max = (int) $rule->getParameter('scale_max');

        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException('Ordinal value out of range');
        }

        // Normalisasi ordinal → 0–1
        $normalized = ($value - $min) / max(($max - $min), 1);

        return $this->applyPreference(
            $normalized,
            $rule->preference_type,
            $rule->curve_param
        );
    }

    /* =======================================================
     * NUMERIC
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
