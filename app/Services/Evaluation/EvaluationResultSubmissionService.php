<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;
use Illuminate\Support\Facades\DB;

class EvaluationResultSubmissionService
{
    protected WeightedScoringService $weightedService;

    public function __construct(WeightedScoringService $weightedService)
    {
        $this->weightedService = $weightedService;
    }

    /**
     * Calculate and store evaluation results
     */
    public function calculateAndStore(DecisionSession $session, string $method = 'smart'): void
    {
        if ($method === 'weighted') {
            // weighted should not be stored in evaluation_results
            return;
        }

        DB::transaction(function () use ($session, $method) {

            // 1. Calculate weighted scores
            $results = $this->weightedService->calculate($session);

            if (empty($results)) {
                throw new \Exception('No evaluation results generated.');
            }

            // 2. Delete previous results for this method
            $session->evaluationResults()
                ->where('method', $method)
                ->delete();

            // 3. Prepare insert
            $rows = [];
            $rank = 1;
            $now = now();

            foreach ($results as $row) {
                $rows[] = [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $row['alternative_id'],
                    'method'              => $method,
                    'evaluation_score'    => $row['final_score'],
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
            }

            // 4. Insert
            EvaluationResult::insert($rows);
        });
    }
}
