<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use Illuminate\Support\Facades\DB;

class SystemCriteriaEvaluationService
{
    public function generate(DecisionSession $session): void
    {
        $alternatives = $session->alternatives()->get();

        DB::transaction(function () use ($session, $alternatives) {

            foreach ($alternatives as $alt) {

                // RAB (criteria_id = 6)
                $this->upsert($session->id, $alt->id, 6, $alt->rab);

                // Beneficiaries (criteria_id = 7)
                $this->upsert($session->id, $alt->id, 7, $alt->beneficiaries);

                // Coverage (criteria_id = 8)
                $this->upsert($session->id, $alt->id, 8, $alt->coverage);
            }
        });
    }

    protected function upsert($sessionId, $altId, $criteriaId, $value)
    {
        DB::table('evaluation_scores')->updateOrInsert(
            [
                'decision_session_id' => $sessionId,
                'alternative_id'      => $altId,
                'criteria_id'         => $criteriaId,
                'source'              => 'system',
            ],
            [
                'user_id'    => null,
                'value'      => $value,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
