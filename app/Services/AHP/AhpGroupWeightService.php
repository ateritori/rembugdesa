<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AhpGroupWeightService
{
    /**
     * Agregasi bobot kriteria kelompok menggunakan Geometric Mean
     */
    public function aggregate(DecisionSession $decisionSession): array
    {
        $individualWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNotNull('dm_id')
            ->get();

        if ($individualWeights->isEmpty()) {
            throw new InvalidArgumentException('Belum ada bobot individu untuk diagregasi.');
        }

        $criteriaIds = collect();

        foreach ($individualWeights as $row) {
            $criteriaIds = $criteriaIds->merge(array_keys($row->weights));
        }

        $criteriaIds = $criteriaIds->unique()->values();

        $groupWeights = [];

        foreach ($criteriaIds as $criteriaId) {
            $product = 1;
            $count = 0;

            foreach ($individualWeights as $row) {
                if (!isset($row->weights[$criteriaId])) {
                    continue;
                }

                $value = (float) $row->weights[$criteriaId];

                if ($value <= 0) {
                    throw new InvalidArgumentException(
                        "Bobot tidak valid pada kriteria ID {$criteriaId}"
                    );
                }

                $product *= $value;
                $count++;
            }

            if ($count === 0) {
                continue;
            }

            $groupWeights[$criteriaId] = pow($product, 1 / $count);
        }

        $total = array_sum($groupWeights);

        if ($total <= 0) {
            throw new InvalidArgumentException('Total bobot hasil agregasi tidak valid.');
        }

        foreach ($groupWeights as $criteriaId => $value) {
            $groupWeights[$criteriaId] = $value / $total;
        }

        DB::transaction(function () use ($decisionSession, $groupWeights) {
            CriteriaWeight::updateOrCreate(
                [
                    'decision_session_id' => $decisionSession->id,
                    'dm_id' => null,
                ],
                [
                    'weights' => $groupWeights,
                    'cr' => null,
                ]
            );
        });

        return $groupWeights;
    }
}
