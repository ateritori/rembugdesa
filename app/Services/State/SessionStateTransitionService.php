<?php

namespace App\Services\State;

use App\Models\DecisionSession;
use App\Services\AHP\AhpGroupSubmissionService;
use App\Services\Borda\BordaPipelineService;
use App\Services\Evaluation\SystemSmartService;
use App\Services\Evaluation\FinalSmartAggregationService;
use App\Services\Evaluation\SystemEvaluationService;

class SessionStateTransitionService
{
    public function activate(
        DecisionSession $decisionSession,
        AhpGroupSubmissionService $groupService
    ) {
        $validStatuses = ['draft', 'configured'];
        abort_unless(in_array($decisionSession->status, $validStatuses), 403);

        $groupService->calculateAndStore($decisionSession);

        $nextStatus = ($decisionSession->status === 'draft') ? 'configured' : 'scoring';
        $decisionSession->update(['status' => $nextStatus]);

        if ($nextStatus === 'scoring') {
            $sessionFresh = $decisionSession->fresh();
            app(SystemEvaluationService::class)->generate($sessionFresh);
        }
    }

    public function close(
        DecisionSession $decisionSession,
        BordaPipelineService $bordaPipelineService
    ) {
        abort_if($decisionSession->status !== 'scoring', 403);

        $decisionSession->getConnection()->transaction(function () use ($decisionSession, $bordaPipelineService) {

            // 1. Hitung Utility SMART (Eceran)
            app(SystemSmartService::class)->calculate($decisionSession);

            // 2. Agregasi Final & Bobot Sektor AHP
            app(FinalSmartAggregationService::class)->calculate($decisionSession);

            $decisionSession->update(['status' => 'closed']);
        });
    }
}
