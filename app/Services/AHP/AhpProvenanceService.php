<?php

namespace App\Services\AHP;

use Illuminate\Support\Collection;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaGroupWeight;
use App\Models\DecisionSession;

class AhpProvenanceService
{
    public function build(DecisionSession $session): array
    {
        return [
            'individual' => $this->buildIndividual($session),
            'group'      => $this->buildGroup($session),
        ];
    }

    // =========================
    // INDIVIDUAL
    // =========================
    private function buildIndividual(DecisionSession $session): array
    {
        $pairwise = CriteriaPairwise::where('decision_session_id', $session->id)
            ->get()
            ->groupBy('dm_id');

        $result = [];

        foreach ($pairwise as $dmId => $rows) {

            if ($rows->isEmpty()) {
                continue;
            }

            $criteriaIds = $rows->pluck('criteria_id_1')
                ->merge($rows->pluck('criteria_id_2'))
                ->unique()
                ->values();

            if ($criteriaIds->count() < 2) {
                continue;
            }

            $matrix = $this->buildMatrix($rows, $criteriaIds);

            $analysis = $this->calculate($matrix);

            $result[$dmId] = [
                'criteria_ids' => $criteriaIds,
                'matrix'       => $matrix,
                'weights'      => $analysis['weights'],
                'cr'           => $analysis['cr'],
                'is_consistent' => $analysis['cr'] <= 0.1,
            ];
        }

        return $result;
    }

    // =========================
    // GROUP
    // =========================
    private function buildGroup(DecisionSession $session): array
    {
        $group = CriteriaGroupWeight::where('decision_session_id', $session->id)
            ->first();

        if (!$group) {
            return [];
        }

        return [
            'weights' => $group->weights ?? [],
            'cr'      => $group->cr ?? null,
        ];
    }

    // =========================
    // BUILD MATRIX
    // =========================
    private function buildMatrix(Collection $rows, Collection $criteriaIds): array
    {
        $n = $criteriaIds->count();
        $matrix = [];

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {

                if ($i === $j) {
                    $matrix[$i][$j] = 1;
                    continue;
                }

                $c1 = $criteriaIds[$i];
                $c2 = $criteriaIds[$j];

                $pair = $rows->first(function ($r) use ($c1, $c2) {
                    return ($r->criteria_id_1 == $c1 && $r->criteria_id_2 == $c2)
                        || ($r->criteria_id_1 == $c2 && $r->criteria_id_2 == $c1);
                });

                if (!$pair) {
                    $matrix[$i][$j] = 1;
                    continue;
                }

                $value = (float) $pair->value;

                if ($pair->criteria_id_1 == $c1) {
                    $matrix[$i][$j] = $pair->direction === 'right'
                        ? $value
                        : (1 / max($value, 1e-9));
                } else {
                    $matrix[$i][$j] = $pair->direction === 'right'
                        ? (1 / max($value, 1e-9))
                        : $value;
                }
            }
        }

        return $matrix;
    }

    // =========================
    // AHP CALCULATION (GM)
    // =========================
    private function calculate(array $M): array
    {
        $n = count($M);

        if ($n === 0) {
            return [
                'weights' => [],
                'cr' => null
            ];
        }

        // --- Geometric Mean
        $W = [];

        for ($i = 0; $i < $n; $i++) {
            $product = 1;

            for ($j = 0; $j < $n; $j++) {
                $val = $M[$i][$j] > 0 ? $M[$i][$j] : 1e-9;
                $product *= $val;
            }

            $W[$i] = pow($product, 1 / $n);
        }

        // --- Normalize
        $sum = array_sum($W);

        if ($sum == 0) {
            return [
                'weights' => array_fill(0, $n, 0),
                'cr' => null
            ];
        }

        foreach ($W as $i => $val) {
            $W[$i] = $val / $sum;
        }

        // --- Lambda max
        $lambda = 0;

        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0;

            for ($j = 0; $j < $n; $j++) {
                $rowSum += $M[$i][$j] * $W[$j];
            }

            $lambda += $rowSum / max($W[$i], 1e-9);
        }

        $lambda /= $n;

        // --- CI & CR
        $CI = ($n > 1) ? ($lambda - $n) / ($n - 1) : 0;

        $RI = [
            1 => 0.00,
            2 => 0.00,
            3 => 0.58,
            4 => 0.90,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45
        ][$n] ?? 1.49;

        $CR = ($RI == 0) ? 0 : $CI / $RI;

        return [
            'weights' => $W,
            'cr'      => round($CR, 4),
        ];
    }
}
