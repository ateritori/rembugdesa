<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;
use App\Services\Borda\BordaRankingService;

class DecisionResultController extends Controller
{
    /**
     * Display final decision result.
     */
    public function show(
        DecisionSession $decisionSession,
        SmartRankingService $smartService,
        BordaRankingService $bordaService
    ) {
        // Only final sessions can show results
        abort_if($decisionSession->status !== 'final', 403);

        // Calculate SMART scores
        $smartScores = $smartService->calculate($decisionSession->id);

        // Calculate Borda scores and final ranking
        $bordaScores = $bordaService->calculate($smartScores);
        $finalRanking = $bordaService->ranking($bordaScores);

        // Load alternatives
        $alternatives = $decisionSession->alternatives()
            ->whereIn('id', array_keys($finalRanking))
            ->get()
            ->keyBy('id');

        // Prepare rows for view
        $rows = [];
        foreach ($finalRanking as $altId => $rank) {
            $rows[] = [
                'rank'        => $rank,
                'alternative' => $alternatives[$altId]->name ?? '-',
                'smart'       => round($smartScores[$altId], 6),
                'borda'       => $bordaScores[$altId],
            ];
        }

        $view = auth()->check() && auth()->user()->hasRole('dm')
            ? 'dms.summary.index'
            : 'decision-sessions.result';

        return view($view, [
            'decisionSession' => $decisionSession,
            'rows'            => $rows,
        ]);
    }
}
