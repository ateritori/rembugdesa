<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AhpIndividualSubmissionService
{
    protected $calculator;

    public function __construct(AhpCalculationService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function authorizeSubmission(DecisionSession $session, User $user): void
    {
        $assignment = $session->assignments()
            ->where('user_id', $user->id)
            ->first();

        abort_if(!$assignment, 403, 'User is not assigned to this session.');

        abort_if(
            !$assignment->can_pairwise || $session->status !== 'configured',
            403,
            'User is not allowed to submit pairwise data.'
        );
    }

    public function submit(DecisionSession $session, User $user, array $pairwiseData)
    {
        return DB::transaction(function () use ($session, $user, $pairwiseData) {

            // Remove existing pairwise data for current user
            $this->deleteExistingPairwise($session, $user);

            // Persist new pairwise data
            $this->storePairwise($session, $user, $pairwiseData);

            // Load active criteria once
            $criteria = $this->getActiveCriteria($session);

            // Build comparison matrix
            $matrix = $this->buildMatrix($criteria, $pairwiseData);

            // Calculate AHP result using dedicated service
            $analysis = $this->calculator->calculate($matrix);

            // Map weights to criteria IDs
            $mappedWeights = $this->mapWeights($criteria, $analysis['weights']);

            // Store result
            $this->storeWeights($session, $user, $mappedWeights, $analysis['cr']);

            return [
                'weights' => $mappedWeights,
                'cr'      => $analysis['cr'],
            ];
        });
    }

    private function deleteExistingPairwise($session, $user)
    {
        $session->criteriaPairwise()
            ->where('dm_id', $user->id)
            ->delete();
    }

    private function storePairwise($session, $user, $pairwiseData)
    {
        $rows = [];
        $now = now();

        foreach ($pairwiseData as $idI => $targets) {
            foreach ($targets as $idJ => $values) {

                $val = (float) ($values['a_ij'] ?? 0);
                if ($val == 0) continue;

                $direction = $val >= 1 ? 'left' : 'right';
                $value = $val >= 1 ? $val : (1 / $val);

                $rows[] = [
                    'decision_session_id' => $session->id,
                    'dm_id'               => $user->id,
                    'criteria_id_1'       => $idI,
                    'criteria_id_2'       => $idJ,
                    'value'               => $value,
                    'direction'           => $direction,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
            }
        }

        if (!empty($rows)) {
            CriteriaPairwise::insert($rows);
        }
    }

    private function getActiveCriteria($session)
    {
        return $session->criteria()
            ->where('is_active', true)
            ->where('level', 1)
            ->orderBy('order')
            ->get()
            ->values();
    }

    private function buildMatrix($criteria, $pairwiseData)
    {
        $n = $criteria->count();
        $matrix = array_fill(0, $n, array_fill(0, $n, 1));

        // Create fast lookup map
        $indexMap = $criteria->pluck('id')->flip();

        foreach ($pairwiseData as $idI => $targets) {
            foreach ($targets as $idJ => $values) {

                $val = (float) ($values['a_ij'] ?? 0);
                if ($val <= 0) continue;

                if (!isset($indexMap[$idI]) || !isset($indexMap[$idJ])) continue;

                $i = $indexMap[$idI];
                $j = $indexMap[$idJ];

                $matrix[$i][$j] = $val;
                $matrix[$j][$i] = 1 / $val;
            }
        }

        return $matrix;
    }

    private function mapWeights($criteria, $weights)
    {
        $mapped = [];

        foreach ($weights as $i => $value) {
            if (isset($criteria[$i])) {
                $mapped[$criteria[$i]->id] = $value;
            }
        }

        return $mapped;
    }

    private function storeWeights($session, $user, $weights, $cr)
    {
        $session->criteriaWeights()->updateOrCreate(
            [
                'decision_session_id' => $session->id,
                'dm_id' => $user->id
            ],
            [
                'weights' => $weights,
                'cr'      => (float) $cr,
                'updated_at' => now()
            ]
        );
    }
}
