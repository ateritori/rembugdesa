<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use Illuminate\Support\Facades\DB;

class AhpGroupSubmissionService
{
    /**
     * @var AhpGroupWeightService
     */
    protected AhpGroupWeightService $groupWeightService;

    public function __construct(AhpGroupWeightService $groupWeightService)
    {
        $this->groupWeightService = $groupWeightService;
    }

    public function calculateAndStore(DecisionSession $session): array
    {
        return DB::transaction(function () use ($session) {

            // Get distinct DM ids with pairwise data
            $dmIds = $session->criteriaPairwise()
                ->distinct()
                ->pluck('dm_id');

            if ($dmIds->isEmpty()) {
                return [
                    'weights' => [],
                    'message' => 'No pairwise data found.'
                ];
            }

            // Load active criteria
            $criteria = $session->criteria()
                ->where('is_active', true)
                ->where('level', 1)
                ->orderBy('order')
                ->get()
                ->values();

            $indexMap = $criteria->pluck('id')->flip();
            $n = $criteria->count();

            // Load all pairwise once and group by DM
            $pairwiseAll = $session->criteriaPairwise()
                ->whereIn('dm_id', $dmIds)
                ->get()
                ->groupBy('dm_id');

            // Build matrix per DM
            $matrices = [];

            foreach ($dmIds as $dmId) {
                $matrix = array_fill(0, $n, array_fill(0, $n, 1));

                $pairwise = $pairwiseAll[$dmId] ?? collect();

                foreach ($pairwise as $p) {
                    if (!isset($indexMap[$p->criteria_id_1]) || !isset($indexMap[$p->criteria_id_2])) {
                        continue;
                    }

                    $i = $indexMap[$p->criteria_id_1];
                    $j = $indexMap[$p->criteria_id_2];

                    $value = (float) $p->value;

                    if ($p->direction === 'left') {
                        $matrix[$i][$j] = $value;
                        $matrix[$j][$i] = 1 / $value;
                    } else {
                        $matrix[$i][$j] = 1 / $value;
                        $matrix[$j][$i] = $value;
                    }
                }

                $matrices[$dmId] = $matrix;
            }

            // Aggregate matrices (group AHP)
            $result = $this->groupWeightService->aggregate($matrices);

            // Map weights to criteria IDs
            $mappedWeights = [];

            foreach ($result['weights'] as $i => $value) {
                if (isset($criteria[$i])) {
                    $mappedWeights[$criteria[$i]->id] = $value;
                }
            }

            // Build provenance (non-intrusive, does not affect main logic)
            $provenance = [
                'dm_ids' => $dmIds->values(),
                'criteria_ids' => $criteria->pluck('id')->values(),
                'matrices_per_dm' => $matrices,
                'aggregation_method' => 'geometric_mean',
                'result' => $result,
            ];

            // Store group result (including CR)
            $session->groupWeight()->updateOrCreate(
                [
                    'decision_session_id' => $session->id,
                ],
                [
                    'weights' => $mappedWeights,
                    'cr'      => $result['cr'] ?? null,
                    'provenance' => $provenance,
                    'updated_at' => now()
                ]
            );

            return [
                'weights' => $mappedWeights,
                'provenance' => $provenance,
            ];
        });
    }
}
