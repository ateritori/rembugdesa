<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Services\AHP\AhpCalculationService;

class AhpProvenanceService
{
    public function build(DecisionSession $session): array
    {
        return [
            'individual' => $this->buildIndividual($session),
            'group' => $this->buildGroup($session),
        ];
    }

    private function buildIndividual(DecisionSession $session): array
    {
        $pairwise = CriteriaPairwise::where('decision_session_id', $session->id)
            ->get()
            ->groupBy('dm_id');

        $calculator = new AhpCalculationService();

        $result = [];

        foreach ($pairwise as $dmId => $rows) {

            // build criteria list
            $criteriaIds = $rows->pluck('criteria_id_1')
                ->merge($rows->pluck('criteria_id_2'))
                ->unique()
                ->values();

            $n = $criteriaIds->count();

            // build matrix
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

                    if ($pair) {
                        $val = $pair->value;

                        if ($pair->criteria_id_1 == $c1) {
                            $matrix[$i][$j] = $pair->direction === 'right' ? $val : 1 / $val;
                        } else {
                            $matrix[$i][$j] = $pair->direction === 'right' ? 1 / $val : $val;
                        }
                    } else {
                        $matrix[$i][$j] = 1;
                    }
                }
            }

            // calculate AHP
            $analysis = $calculator->calculate($matrix);

            $result[$dmId] = [
                'criteria_ids' => $criteriaIds->toArray(),
                'matrix' => $matrix,
                'weights' => $analysis['weights'],
                'cr' => $analysis['cr'],
                'is_consistent' => $analysis['is_consistent'],
            ];
        }

        return $result;
    }

    private function buildGroup(DecisionSession $session): array
    {
        $groupWeight = $session->groupWeight;

        if (!$groupWeight) {
            return [];
        }

        $weights = $groupWeight->weights;

        if (is_string($weights)) {
            $weights = json_decode($weights, true) ?: [];
        }

        return [
            'weights' => $weights,
            'cr' => $groupWeight->cr,
        ];
    }
}
