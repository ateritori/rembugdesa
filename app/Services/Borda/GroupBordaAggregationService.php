<?php

namespace App\Services\Borda;

use App\Models\BordaAggregation;
use App\Models\EvaluationAggregation;
use App\Models\UserProfile;

class GroupBordaAggregationService
{
    protected $bordaService;

    public function __construct(BordaService $bordaService)
    {
        $this->bordaService = $bordaService;
    }

    public function calculate($session, $method, $kategori)
    {
        $users = UserProfile::where('kategori_dm', $kategori)
            ->pluck('user_id');

        $data = EvaluationAggregation::where('decision_session_id', $session->id)
            ->where('method', $method)
            ->whereIn('user_id', $users)
            ->get()
            ->groupBy('user_id');

        $groupScores = [];

        foreach ($data as $userId => $rows) {

            $items = [];

            foreach ($rows as $row) {
                $items[] = [
                    'alternative_id' => $row->alternative_id,
                    'score' => $row->score,
                ];
            }

            $borda = $this->bordaService->calculate($items);

            foreach ($borda as $res) {

                $altId = $res['alternative_id'];

                if (!isset($groupScores[$altId])) {
                    $groupScores[$altId] = 0;
                }

                $groupScores[$altId] += $res['borda_score'];
            }
        }

        // final ranking kelompok (preserve aggregated scores)
        $finalItems = collect($groupScores)
            ->map(function ($score, $altId) {
                return [
                    'alternative_id' => $altId,
                    'score' => $score,
                ];
            })
            ->sortByDesc('score')
            ->values();

        $rank = 1;

        foreach ($finalItems as $item) {

            BordaAggregation::updateOrCreate(
                [
                    'decision_session_id' => $session->id,
                    'method' => strtoupper($method),
                    'level' => 'group',
                    'source' => $kategori,
                    'alternative_id' => $item['alternative_id'],
                ],
                [
                    'borda_score' => $item['score'],
                    'rank' => $rank,
                ]
            );
            $rank++;
        }
    }
}
