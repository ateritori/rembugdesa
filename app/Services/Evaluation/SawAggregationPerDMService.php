<?php

namespace App\Services\Evaluation;

use App\Models\EvaluationResult;
use App\Models\EvaluationAggregation;

class SawAggregationPerDMService
{
    /**
     * Aggregate SAW results per DM into final score per alternative
     */
    public function calculate($session): void
    {
        $data = EvaluationResult::where('decision_session_id', $session->id)
            ->where('method', 'saw')
            ->whereNotNull('user_id')
            ->get()
            ->groupBy(['user_id', 'alternative_id']);

        foreach ($data as $userId => $alternatives) {

            foreach ($alternatives as $altId => $rows) {

                $total = 0;

                foreach ($rows as $row) {
                    // SAW = SUM of weighted scores (not average)
                    $total += $row->weighted_score;
                }

                EvaluationAggregation::updateOrCreate(
                    [
                        'decision_session_id' => $session->id,
                        'user_id' => $userId,
                        'alternative_id' => $altId,
                        'method' => 'saw',
                    ],
                    [
                        'score' => $total,
                    ]
                );
            }
        }
    }
}
