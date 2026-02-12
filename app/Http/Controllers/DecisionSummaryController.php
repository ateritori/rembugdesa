<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;

class DecisionSummaryController extends Controller
{
    public function show(
        DecisionSession $decisionSession,
        SmartRankingService $smartService
    ) {
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);

        abort_if(! in_array($decisionSession->status, ['aggregated', 'final'], true), 403);

        $smartScores = $smartService->calculate(
            $decisionSession->id,
            auth()->id()
        );


        $alternatives = $decisionSession->alternatives()
            ->whereIn('id', array_keys($smartScores))
            ->get()
            ->keyBy('id');

        $rows = [];
        foreach ($smartScores as $altId => $score) {
            $rows[] = [
                'alternative' => $alternatives[$altId]->name ?? '-',
                'smart'       => round($score, 6),
            ];
        }

        usort($rows, fn($a, $b) => $b['smart'] <=> $a['smart']);

        return view('dms.summary.index', [
            'decisionSession' => $decisionSession,
            'rows'            => $rows,
        ]);
    }
}
