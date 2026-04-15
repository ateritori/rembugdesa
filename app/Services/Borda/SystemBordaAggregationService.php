<?php

namespace App\Services\Borda;

use App\Models\BordaAggregation;
use App\Models\EvaluationAggregation;

class SystemBordaAggregationService
{
    protected $bordaService;

    public function __construct(BordaService $bordaService)
    {
        $this->bordaService = $bordaService;
    }

    public function calculate($session, $method)
    {
        $rows = EvaluationAggregation::where('decision_session_id', $session->id)
            ->where('method', $method)
            ->whereNull('user_id')
            ->get();

        $items = [];

        foreach ($rows as $row) {
            $items[] = [
                'alternative_id' => $row->alternative_id,
                'score' => $row->score,
            ];
        }

        // Convert ranking to Borda score (pure Borda)
        $borda = $this->bordaService->calculate($items);

        foreach ($borda as $res) {

            BordaAggregation::updateOrCreate(
                [
                    'decision_session_id' => $session->id,
                    'method' => strtoupper($method),
                    'level' => 'system',
                    'source' => 'system',
                    'alternative_id' => $res['alternative_id'],
                ],
                [
                    'borda_score' => $res['borda_score'],
                    'rank' => $res['rank'],
                ]
            );
        }
    }
}
