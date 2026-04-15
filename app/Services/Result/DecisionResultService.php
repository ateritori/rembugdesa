<?php

namespace App\Services\Result;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\DmScore;
use App\Models\DecisionResult;
use App\Models\AlternativeEvaluation;
use App\Models\CriteriaWeight;
use App\Services\SAW\SawRankingService;
use Illuminate\Support\Collection;

class DecisionResultService
{
    /**
     * Mengambil hasil final kelompok yang sudah dihitung via Borda (Persisted)
     */
    public function borda(DecisionSession $session, string $pipeline = 'SMART+FINAL'): Collection
    {
        return DecisionResult::where('decision_session_id', $session->id)
            ->where('pipeline', $pipeline)
            ->with('alternative')
            ->orderBy('rank')
            ->get();
    }

    /**
     * Semua hasil SMART per Decision Maker (Untuk Dashboard Admin)
     */
    public function smartByDm(DecisionSession $session): Collection
    {
        return $session->dmScores()
            ->where('method', DmScore::METHOD_SMART)
            ->with(['alternative', 'dm'])
            ->get()
            ->groupBy('dm_id')
            ->map(function ($rows) {
                return $rows->sortByDesc('score')->values();
            });
    }

    /**
     * Hasil SMART untuk satu DM spesifik (Untuk Workspace DM)
     */
    public function smartForDm(DecisionSession $session, User $dm): Collection
    {
        return $session->dmScores()
            ->where('method', DmScore::METHOD_SMART)
            ->where('dm_id', $dm->id)
            ->with('alternative')
            ->orderByDesc('score')
            ->get();
    }

    /**
     * Benchmark SAW + Borda (ON THE FLY, TIDAK DIPERSIST)
     * Digunakan untuk analisis perbandingan metode SMART vs SAW dalam agregasi Borda
     */
    public function sawBordaBenchmark(
        DecisionSession $session,
        SawRankingService $sawService
    ): Collection {
        // 1. Eager Load relasi untuk menghindari N+1 query dan null errors
        $session->loadMissing(['alternatives', 'dms']);

        // 2. Ambil bobot kelompok (AHP Global)
        $groupWeight = CriteriaWeight::where('decision_session_id', $session->id)
            ->whereNull('dm_id')
            ->first();

        if (!$groupWeight || $session->dms->isEmpty()) {
            return collect();
        }

        $weights = $groupWeight->weights;

        // 3. Ambil semua evaluasi mentah per DM
        $evaluations = AlternativeEvaluation::where('decision_session_id', $session->id)
            ->get()
            ->groupBy('dm_id');

        if ($evaluations->isEmpty()) {
            return collect();
        }

        $bordaScores = [];
        $n = $session->alternatives->count();

        // 4. Kalkulasi SAW + Akumulasi Borda per DM
        foreach ($session->dms as $dm) {
            if (!isset($evaluations[$dm->id])) {
                continue;
            }

            // Bangun matriks keputusan untuk DM saat ini
            $matrix = [];
            foreach ($evaluations[$dm->id] as $e) {
                $matrix[$e->alternative_id][$e->criteria_id] = (float) $e->raw_value;
            }

            // Hitung skor SAW menggunakan service eksternal
            $scores = $sawService->calculateFromMatrix($matrix, $weights);

            // Urutkan skor SAW (Descending) untuk mendapatkan rank
            arsort($scores);

            $rank = 1;
            foreach ($scores as $altId => $scoreValue) {
                /**
                 * Konsistensi Metodologi Borda:
                 * Jika ada 5 alternatif: Rank 1 = 5 poin, Rank 2 = 4 poin, dst.
                 * Rumus: n - (rank - 1)
                 */
                $bordaScores[$altId] = ($bordaScores[$altId] ?? 0) + ($n - ($rank - 1));
                $rank++;
            }
        }

        // 5. Urutkan hasil akhir akumulasi Borda
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
     * Membandingkan kontribusi/preferensi DM tertentu terhadap hasil akhir kelompok
     */
    public function dmContribution(DecisionSession $session, User $dm, string $pipeline = 'SMART+FINAL'): Collection
    {
        $smartRows = $this->smartForDm($session, $dm)->values();

        // Build rank map for SMART (dynamic rank from score)
        $smartRankMap = [];
        foreach ($smartRows as $index => $row) {
            $smartRankMap[$row->alternative_id] = [
                'score' => $row->score,
                'rank'  => $index + 1,
            ];
        }

        $borda = $this->borda($session, $pipeline);

        return $borda->map(function ($row) use ($smartRankMap) {
            $altId = $row->alternative_id;

            return (object) [
                'alternative' => $row->alternative,
                'smart_score' => $smartRankMap[$altId]['score'] ?? 0,
                'smart_rank'  => $smartRankMap[$altId]['rank'] ?? '-',
                'borda_score' => $row->score,
                'final_rank'  => $row->rank,
            ];
        });
    }
}
