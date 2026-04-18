<?php

namespace App\Services\State;

use App\Models\DecisionSession;
use App\Services\AHP\AhpGroupSubmissionService;
use App\Services\Borda\BordaPipelineService;

class SessionStateTransitionService
{
    public function activate(
        DecisionSession $decisionSession,
        AhpGroupSubmissionService $groupService
    ) {
        $validStatuses = ['draft', 'configured'];

        abort_unless(in_array($decisionSession->status, $validStatuses), 403);

        // HITUNG BOBOT AHP (GROUP)
        $groupService->calculateAndStore($decisionSession);

        $nextStatus = ($decisionSession->status === 'draft')
            ? 'configured'
            : 'scoring';

        $decisionSession->update(['status' => $nextStatus]);

        /**
         * 🔵 SYSTEM INPUT TRIGGER (CLEAN VERSION)
         * hanya generate evaluation_scores (system)
         */
        if ($nextStatus === 'scoring') {

            $sessionFresh = $decisionSession->fresh();

            app(\App\Services\Evaluation\SystemEvaluationService::class)
                ->generate($sessionFresh);
        }
    }

    public function close(
        DecisionSession $decisionSession,
        BordaPipelineService $bordaPipelineService
    ) {
        abort_if($decisionSession->status !== 'scoring', 403);

        $decisionSession->getConnection()->transaction(function () use ($decisionSession, $bordaPipelineService) {

            /**
             * 🔴 PROCESS LAYER (WAJIB URUT)
             */

            // 1. SMART
            app(\App\Services\Evaluation\SmartScoringService::class)
                ->calculate($decisionSession);

            // 2. SAW
            app(\App\Services\Evaluation\SawScoringService::class)
                ->calculate($decisionSession);

            // 3. WEIGHTED (AHP + SMART + SAW)
            app(\App\Services\Evaluation\AHPWeightedScoringService::class)
                ->calculate($decisionSession);

            // 4. FINAL AGGREGATION
            app(\App\Services\Evaluation\EvaluationAggregationService::class)
                ->calculate($decisionSession);

            /**
             * 🔵 BORDA LAYER (OPTIONAL DECISION METHOD)
             */
            $bordaPipelineService->run($decisionSession, 'smart');
            $bordaPipelineService->run($decisionSession, 'saw');

            $decisionSession->update(['status' => 'closed']);
        });
    }
}
