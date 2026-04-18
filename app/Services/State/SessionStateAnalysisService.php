<?php

namespace App\Services\State;

use App\Models\DecisionSession;
use App\Services\Analysis\FinalRankingAnalysisService;

class SessionStateAnalysisService
{
    public function __construct(
        protected FinalRankingAnalysisService $analysisService
    ) {}

    public function analyze(DecisionSession $session): array
    {
        $data = $this->analysisService->build($session);

        $comparisonMatrix = collect($data['comparison']);

        return [
            'smartResults' => collect($data['smart']),
            'sawResults' => collect($data['saw']),
            'comparisonMatrix' => $comparisonMatrix,
            'rhoPercentage' => $data['rhoPercentage'] ?? null,
            'rhoInterpretation' => $data['rhoInterpretation'] ?? null,
            'summary' => [
                'total_match' => $comparisonMatrix->where('status', 'MATCH')->count(),
                'total_shift' => $comparisonMatrix->where('status', 'SHIFT')->count(),
                'total_invalid' => $comparisonMatrix->where('status', 'INVALID')->count(),
            ],
        ];
    }
}
