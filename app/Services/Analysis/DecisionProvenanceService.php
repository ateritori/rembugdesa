<?php

namespace App\Services\Analysis;

use App\Services\Evaluation\FinalSmartAggregationService;

class DecisionProvenanceService
{
    public function build($decisionSession)
    {
        // =========================
        // FINAL COMPARISON (SAW, RHO, dll)
        // =========================
        $final = app(FinalRankingAnalysisService::class)
            ->build($decisionSession);

        // =========================
        // 🔥 AMBIL SMART FINAL LANGSUNG DARI SUMBER RESMI
        // =========================
        $smart = app(FinalSmartAggregationService::class)
            ->calculate($decisionSession);

        // =========================
        // SAW (sementara masih dari final)
        // =========================
        $saw = $final['saw'];

        return [
            'meta' => [
                'decision_session_id' => $decisionSession->id,
            ],

            // =========================
            // PIPELINE VIEW
            // =========================
            'pipeline' => [
                'smart_final' => $smart,
                'saw'   => $saw,
                'final' => [
                    'comparison' => $final['comparison'],
                    'evaluation' => [
                        'rho' => $final['rho'],
                        'rhoPercentage' => $final['rhoPercentage'],
                        'rhoInterpretation' => $final['rhoInterpretation'],
                    ]
                ]
            ],

            // =========================
            // TRACE PER ALTERNATIVE
            // =========================
            'trace' => $this->buildTrace(
                $smart,
                $saw,
                $final['comparison']
            ),
        ];
    }

    private function buildTrace($smart, $saw, $comparison)
    {
        $smartMap = collect($smart)->keyBy('alternative_id');
        $sawMap   = collect($saw)->keyBy('alternative_id');
        $compMap  = collect($comparison)->keyBy('alternative_id');

        $allIds = collect()
            ->merge($smartMap->keys())
            ->merge($sawMap->keys())
            ->unique()
            ->values();

        $trace = [];

        foreach ($allIds as $id) {
            $trace[] = [
                'alternative_id' => $id,

                'name' =>
                $smartMap[$id]['name']
                    ?? $sawMap[$id]['name']
                    ?? '-',

                'trace' => [
                    'smart_final' => $smartMap[$id] ?? null,
                    'saw'         => $sawMap[$id] ?? null,
                    'comparison'  => $compMap[$id] ?? null,
                ]
            ];
        }

        return $trace;
    }
}
