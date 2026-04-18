<?php

namespace App\Services\State;

use App\Models\DecisionSession;

class SessionStateDashboardService
{
    public function __construct(
        protected SessionStateProgressService $progressService,
        protected SessionStateAnalysisService $analysisService
    ) {}

    public function build(DecisionSession $decisionSession, string $tab): array
    {
        $decisionSession->load(['alternatives', 'criteria', 'assignments']);

        $assignments = $decisionSession->assignments;

        // Summary
        $assignedDmCount = $assignments->pluck('user_id')->unique()->count();
        $selectedActionsCount = $assignments->count();

        $activeCriteriaCount = $decisionSession->criteria->where('is_active', true)->count();
        $activeAlternativesCount = $decisionSession->alternatives->where('is_active', true)->count();

        $canActivate = $activeCriteriaCount >= 2
            && $activeAlternativesCount >= 2
            && $assignedDmCount >= 1;

        // Progress
        $progress = $this->progressService->calculate($decisionSession);

        // Default
        $analysis = [
            'smartResults' => collect(),
            'sawResults' => collect(),
            'comparisonMatrix' => [],
            'summary' => [
                'total_match' => 0,
                'total_shift' => 0,
                'total_invalid' => 0,
            ],
            'rhoPercentage' => null,
            'rhoInterpretation' => null,
        ];

        if ($decisionSession->status === 'closed' && $tab === 'analisis') {
            $analysis = $this->analysisService->analyze($decisionSession);
        }

        return array_merge([
            'decisionSession' => $decisionSession,
            'assignedDmCount' => $assignedDmCount,
            'selectedActionsCount' => $selectedActionsCount,
            'activeCriteriaCount' => $activeCriteriaCount,
            'activeAlternativesCount' => $activeAlternativesCount,
            'canActivate' => $canActivate,
            'dmEvaluationsDone' => $progress['dmAltDone'],
        ], $progress, $analysis);
    }
}
