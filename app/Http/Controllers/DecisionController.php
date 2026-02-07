<?php

namespace App\Http\Controllers;

use App\Services\AHP\AhpService;
use App\Services\SMART\SmartService;
use App\Services\BORDA\BordaService;

class DecisionController extends Controller
{
    protected AhpService $ahp;
    protected SmartService $smart;
    protected BordaService $borda;

    public function __construct(
        AhpService $ahp,
        SmartService $smart,
        BordaService $borda
    ) {
        $this->middleware(['auth', 'role:dm']);

        $this->ahp   = $ahp;
        $this->smart = $smart;
        $this->borda = $borda;
    }

    /**
     * Simulasi end-to-end proses keputusan
     * (versi dummy, tanpa database)
     */
    public function simulate()
    {
        /** -----------------------------
         * TAHAP 1: AHP
         * ----------------------------*/
        $criteriaMatrix = [
            [1, 3, 5],
            [1 / 3, 1, 3],
            [1 / 5, 1 / 3, 1],
        ];

        $ahpResult = $this->ahp->calculatePriority($criteriaMatrix);
        $weights   = [
            'C1' => $ahpResult['weights'][0],
            'C2' => $ahpResult['weights'][1],
            'C3' => $ahpResult['weights'][2],
        ];

        /** -----------------------------
         * TAHAP 2: SMART (per DM)
         * ----------------------------*/
        $criteriaConfig = [
            'C1' => ['weight' => $weights['C1'], 'custom_map' => [1 => 0.2, 2 => 0.4, 3 => 0.6, 4 => 0.8, 5 => 1.0]],
            'C2' => ['weight' => $weights['C2'], 'min' => 0, 'max' => 100, 'type' => 'benefit'],
            'C3' => ['weight' => $weights['C3'], 'min' => 0, 'max' => 10,  'type' => 'cost'],
        ];

        // Dummy ranking dari 3 Decision Makers
        $dmRankings = [];

        $dmRatings = [
            // DM1
            [
                ['alternative_id' => 'A1', 'values' => ['C1' => 5, 'C2' => 80, 'C3' => 3]],
                ['alternative_id' => 'A2', 'values' => ['C1' => 4, 'C2' => 70, 'C3' => 5]],
                ['alternative_id' => 'A3', 'values' => ['C1' => 3, 'C2' => 60, 'C3' => 2]],
            ],
            // DM2
            [
                ['alternative_id' => 'A2', 'values' => ['C1' => 5, 'C2' => 90, 'C3' => 4]],
                ['alternative_id' => 'A1', 'values' => ['C1' => 4, 'C2' => 75, 'C3' => 3]],
                ['alternative_id' => 'A3', 'values' => ['C1' => 3, 'C2' => 65, 'C3' => 2]],
            ],
            // DM3
            [
                ['alternative_id' => 'A1', 'values' => ['C1' => 5, 'C2' => 85, 'C3' => 2]],
                ['alternative_id' => 'A3', 'values' => ['C1' => 4, 'C2' => 70, 'C3' => 3]],
                ['alternative_id' => 'A2', 'values' => ['C1' => 3, 'C2' => 60, 'C3' => 4]],
            ],
        ];

        foreach ($dmRatings as $ratings) {
            $scored = $this->smart->processScoring($ratings, $criteriaConfig);
            $dmRankings[] = array_column($scored, 'alternative_id');
        }

        /** -----------------------------
         * TAHAP 3: BORDA
         * ----------------------------*/
        $finalConsensus = $this->borda->aggregateVotes($dmRankings, 3);

        return response()->json([
            'ahp'   => $ahpResult,
            'borda' => $finalConsensus,
        ]);
    }
}
