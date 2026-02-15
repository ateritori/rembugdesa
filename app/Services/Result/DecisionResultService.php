<?php

namespace App\Services\Result;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\SmartResultDm;
use App\Models\BordaResult;
use App\Models\AlternativeEvaluation;
use App\Models\CriteriaWeight;
use App\Services\SAW\SawRankingService;
use Illuminate\Support\Collection;

class DecisionResultService
{
    /**
     * Hasil final kelompok (Borda)
     */
    public function borda(DecisionSession $session): Collection
    {
        return BordaResult::where('decision_session_id', $session->id)
            ->with('alternative')
            ->orderBy('final_rank')
            ->get();
    }

    /**
     * Semua hasil SMART per DM (ADMIN)
     */
    public function smartByDm(DecisionSession $session): Collection
    {
        return SmartResultDm::where('decision_session_id', $session->id)
            ->with(['alternative', 'dm'])
            ->orderBy('dm_id')
            ->orderBy('rank_dm')
            ->get()
            ->groupBy('dm_id');
    }

    /**
     * Hasil SMART satu DM (DM Workspace)
     */
    public function smartForDm(
        DecisionSession $session,
        User $dm
    ): Collection {
        return SmartResultDm::where([
            'decision_session_id' => $session->id,
            'dm_id'               => $dm->id,
        ])
            ->with('alternative')
            ->orderBy('rank_dm')
            ->get();
    }


    /**
     * Benchmark SAW + Borda (ON THE FLY, TIDAK DIPERSIST)
     * Khusus ANALISIS
     */
    public function sawBordaBenchmark(
        DecisionSession $session,
        SawRankingService $sawService
    ): Collection {

        // 1. Bobot AHP kelompok
        $groupWeight = CriteriaWeight::where('decision_session_id', $session->id)
            ->whereNull('dm_id')
            ->firstOrFail();

        $weights = $groupWeight->weights;

        // 2. Ambil semua DM dalam sesi
        $dms = $session->dms;

        // 3. Ambil semua evaluasi alternatif (RAW VALUE)
        $evaluations = AlternativeEvaluation::where('decision_session_id', $session->id)
            ->get()
            ->groupBy('dm_id');

        if ($evaluations->isEmpty()) {
            return collect();
        }

        // 4. Inisialisasi akumulator Borda
        $bordaScores = [];
        $n = $session->alternatives()->count();

        // 5. SAW + Borda PER DM
        foreach ($dms as $dm) {
            if (! isset($evaluations[$dm->id])) {
                continue;
            }

            // Matrix SAW untuk DM ini
            $matrix = [];

            foreach ($evaluations[$dm->id] as $e) {
                $matrix[$e->alternative_id][$e->criteria_id] = (float) $e->raw_value;
            }

            // Hitung skor SAW DM ini
            $scores = $sawService->calculateFromMatrix($matrix, $weights);

            // Ranking SAW
            arsort($scores);

            $rank = 1;
            foreach ($scores as $altId => $score) {
                // Borda klasik: (n - rank)
                $bordaScores[$altId] ??= 0;
                $bordaScores[$altId] += ($n - $rank);
                $rank++;
            }
        }

        // 6. Ranking akhir hasil akumulasi Borda
        arsort($bordaScores);

        $results = collect();
        $finalRank = 1;

        foreach ($bordaScores as $altId => $score) {
            $results->push((object) [
                'alternative_id' => $altId,
                'alternative'    => $session->alternatives->firstWhere('id', $altId),
                'borda_score'    => $score,
                'final_rank'     => $finalRank++,
            ]);
        }

        return $results;
    }

    /**
     * Kontribusi DM terhadap hasil akhir (DM Workspace)
     */
    public function dmContribution(
        DecisionSession $session,
        User $dm
    ): array {
        $smart = $this->smartForDm($session, $dm)->keyBy('alternative_id');
        $borda = $this->borda($session)->keyBy('alternative_id');

        $result = [];

        foreach ($borda as $altId => $row) {
            $result[$altId] = [
                'alternative' => $row->alternative,
                'smart_score' => $smart[$altId]->smart_score ?? 0,
                'smart_rank'  => $smart[$altId]->rank_dm ?? null,
                'borda_score' => $row->borda_score,
                'final_rank'  => $row->final_rank,
            ];
        }

        return $result;
    }
}
