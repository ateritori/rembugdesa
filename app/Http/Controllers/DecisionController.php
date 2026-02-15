<?php

namespace App\Http\Controllers;

use App\Services\AHP\AhpService;
use App\Services\SMART\SmartRankingService; // Penyesuaian Nama
use App\Services\BORDA\BordaRankingService; // Penyesuaian Nama
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DecisionController extends Controller
{
    protected AhpService $ahp;
    protected SmartRankingService $smart;
    protected BordaRankingService $borda;

    public function __construct(
        AhpService $ahp,
        SmartRankingService $smart,
        BordaRankingService $borda
    ) {
        // Menggunakan middleware di constructor
        $this->middleware(['auth', 'role:dm']);

        $this->ahp   = $ahp;
        $this->smart = $smart;
        $this->borda = $borda;
    }

    /**
     * Simulasi end-to-end proses keputusan menggunakan
     * integrasi AHP, SMART Ranking, dan Borda Ranking.
     */
    public function simulate(): JsonResponse
    {
        try {
            /** -----------------------------
             * TAHAP 1: AHP (Weighting)
             * ----------------------------*/
            $criteriaMatrix = [
                [1, 3, 5],
                [1 / 3, 1, 3],
                [1 / 5, 1 / 3, 1],
            ];

            $ahpResult = $this->ahp->calculatePriority($criteriaMatrix);
            $weights   = [
                'C1' => (float) $ahpResult['weights'][0],
                'C2' => (float) $ahpResult['weights'][1],
                'C3' => (float) $ahpResult['weights'][2],
            ];

            /** -----------------------------
             * TAHAP 2: SMART (Individual Ranking)
             * ----------------------------*/
            $criteriaConfig = [
                'C1' => ['weight' => $weights['C1'], 'custom_map' => [1 => 0.2, 2 => 0.4, 3 => 0.6, 4 => 0.8, 5 => 1.0]],
                'C2' => ['weight' => $weights['C2'], 'min' => 0, 'max' => 100, 'type' => 'benefit'],
                'C3' => ['weight' => $weights['C3'], 'min' => 0, 'max' => 10,  'type' => 'cost'],
            ];

            $dmRatings = [
                // DM 1
                [
                    ['alternative_id' => 'A1', 'values' => ['C1' => 5, 'C2' => 80, 'C3' => 3]],
                    ['alternative_id' => 'A2', 'values' => ['C1' => 4, 'C2' => 70, 'C3' => 5]],
                    ['alternative_id' => 'A3', 'values' => ['C1' => 3, 'C2' => 60, 'C3' => 2]],
                ],
                // DM 2
                [
                    ['alternative_id' => 'A2', 'values' => ['C1' => 5, 'C2' => 90, 'C3' => 4]],
                    ['alternative_id' => 'A1', 'values' => ['C1' => 4, 'C2' => 75, 'C3' => 3]],
                    ['alternative_id' => 'A3', 'values' => ['C1' => 3, 'C2' => 65, 'C3' => 2]],
                ],
                // DM 3
                [
                    ['alternative_id' => 'A1', 'values' => ['C1' => 5, 'C2' => 85, 'C3' => 2]],
                    ['alternative_id' => 'A3', 'values' => ['C1' => 4, 'C2' => 70, 'C3' => 3]],
                    ['alternative_id' => 'A2', 'values' => ['C1' => 3, 'C2' => 60, 'C3' => 4]],
                ],
            ];

            $dmRankings = [];
            foreach ($dmRatings as $ratings) {
                // Memanggil SmartRankingService
                $scored = $this->smart->processScoring($ratings, $criteriaConfig);
                $dmRankings[] = array_column($scored, 'alternative_id');
            }

            /** -----------------------------
             * TAHAP 3: BORDA (Group Aggregation)
             * ----------------------------*/
            // Memanggil BordaRankingService
            $finalConsensus = $this->borda->aggregateVotes($dmRankings, count($dmRankings));

            return response()->json([
                'status' => 'success',
                'data' => [
                    'ahp_weights'      => $weights,
                    'individual_ranks' => $dmRankings,
                    'borda_result'     => $finalConsensus,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Simulation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menjalankan simulasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
