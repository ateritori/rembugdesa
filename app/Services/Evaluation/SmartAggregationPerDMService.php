<?php

namespace App\Services\Evaluation;

use App\Models\EvaluationAggregation;
use App\Models\EvaluationResult;

class SmartAggregationPerDMService
{
    public function calculate($session)
    {
        $data = EvaluationResult::where('decision_session_id', $session->id)
            ->where('method', 'smart')
            ->whereNotNull('user_id')
            ->get()
            ->groupBy(['user_id', 'alternative_id']);

        foreach ($data as $userId => $alternatives) {

            foreach ($alternatives as $altId => $rows) {

                $total = 0;
                $count = 0;

                foreach ($rows as $row) {
                    $total += $row->weighted_score;
                    $count++;
                }

                if ($count === 0) continue;

                $finalScore = $total / $count;

                // 🔥 simpan ke tabel baru
                EvaluationAggregation::updateOrCreate(
                    [
                        'decision_session_id' => $session->id,
                        'user_id' => $userId,
                        'alternative_id' => $altId,
                        'method' => 'smart',
                    ],
                    [
                        'score' => $finalScore,
                    ]
                );
            }
        }
    }
}
