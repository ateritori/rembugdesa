<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;

class AhpWorkspaceService
{
    public function getWorkspace(DecisionSession $decisionSession, $user)
    {
        $assignment = $decisionSession->assignments()
            ->where('user_id', $user->id)
            ->first();

        abort_if(!$assignment, 403, 'User is not assigned to this session.');

        abort_if(
            !$assignment->can_pairwise || $decisionSession->status !== 'configured',
            403,
            'User is not allowed to access pairwise phase.'
        );

        $decisionSession->load([
            'criteria' => function ($q) {
                $q->where('is_active', true)
                    ->where('level', 1)
                    ->orderBy('order');
            },
            'criteriaPairwise' => function ($q) use ($user) {
                $q->where('dm_id', $user->id);
            },
            'criteriaWeights' => function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->where('dm_id', $user->id)
                        ->orWhereNull('dm_id');
                });
            }
        ]);

        $criterias = $decisionSession->criteria;
        $level1Ids = $criterias->pluck('id')->toArray();

        $existingPairwise = $this->transformPairwise(
            $decisionSession->criteriaPairwise,
            $level1Ids
        );

        $individualWeight = $decisionSession->criteriaWeights
            ->firstWhere('dm_id', $user->id);

        $groupResult = $decisionSession->criteriaWeights
            ->firstWhere('dm_id', null);

        return [
            'decisionSession'  => $decisionSession,
            'criterias'        => $criterias,
            'existingPairwise' => $existingPairwise,
            'criteriaWeights'  => $individualWeight,
            'groupResult'      => $groupResult,
            'tab'              => 'penilaian-kriteria',
            'isEditing'        => !$individualWeight,
        ];
    }

    private function transformPairwise($pairwiseCollection, $level1Ids)
    {
        return $pairwiseCollection
            ->filter(function ($p) use ($level1Ids) {
                return in_array($p->criteria_id_1, $level1Ids) &&
                    in_array($p->criteria_id_2, $level1Ids);
            })
            ->mapWithKeys(function ($p) {
                $key = min($p->criteria_id_1, $p->criteria_id_2)
                    . '-' .
                    max($p->criteria_id_1, $p->criteria_id_2);

                $pos = $p->direction === 'left'
                    ? 9 - ($p->value - 1)
                    : 9 + ($p->value - 1);

                return [
                    $key => [
                        'id_i' => (int) $p->criteria_id_1,
                        'id_j' => (int) $p->criteria_id_2,
                        'pos'  => $pos,
                    ]
                ];
            })
            ->toArray();
    }
}
