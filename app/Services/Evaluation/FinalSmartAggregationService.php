<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use Illuminate\Support\Facades\DB;

class FinalSmartAggregationService
{
    public function calculate(DecisionSession $session, float $alpha = 0.6): array
    {
        $beta = 1 - $alpha;

        // 1. Ambil Bobot Sektor AHP (Level 1)
        $weightRecord = DB::table('criteria_group_weights')
            ->where('decision_session_id', $session->id)
            ->latest('id')
            ->first();

        $weights = [];
        if ($weightRecord) {
            $raw = $weightRecord->weights;
            $weights = is_string($raw) ? json_decode($raw, true) ?: [] : (is_array($raw) ? $raw : []);
        }
        $weights = collect($weights)->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])->all();

        // 2. Load Master Data
        $alternatives = DB::table('alternatives')->where('decision_session_id', $session->id)->get()->keyBy('id');
        $criteriaList = DB::table('criteria')->where('decision_session_id', $session->id)->get()->keyBy('id');

        // 3. Ambil Data Eceran dari Evaluation Results
        $allResults = DB::table('evaluation_results')
            ->where('decision_session_id', $session->id)
            ->where('method', 'smart')
            ->get()
            ->groupBy('alternative_id');

        $results = [];

        foreach ($allResults as $altId => $rows) {
            $alt = $alternatives[$altId] ?? null;
            if (!$alt) continue;

            // Rata-rata per kriteria dulu (hindari double counting antar DM)
            $systemRows = $rows->whereNull('user_id')->groupBy('criteria_id');
            $humanRows  = $rows->whereNotNull('user_id')->groupBy('criteria_id');

            $systemScore = $systemRows->count() > 0
                ? $systemRows->map(fn($r) => $r->avg('evaluation_score'))->avg()
                : 0;

            $humanScore = $humanRows->count() > 0
                ? $humanRows->map(fn($r) => $r->avg('evaluation_score'))->avg()
                : 0;

            // Gabungan SMART (Alpha & Beta)
            $smartScore = ($alpha * $systemScore) + ($beta * $humanScore);

            // 4. Ambil Bobot Sektor yang "dibawa" Alternatif
            $sectorId = $alt->criteria_id;
            $criteriaItem = $criteriaList[$sectorId] ?? null;

            // Jika level 2, cari Parent Level 1-nya
            if ($criteriaItem && $criteriaItem->level == 2) {
                $actualSectorId = DB::table('criteria')
                    ->where('decision_session_id', $session->id)
                    ->where('level', 1)
                    ->where('order', '<=', $criteriaItem->order)
                    ->orderByDesc('order')
                    ->value('id');
            } else {
                $actualSectorId = $sectorId;
            }

            $sectorWeight = $weights[$actualSectorId] ?? 0;
            // DEBUG: cek mapping sector weight
            if ($sectorWeight === 0) {
                dd([
                    'alternative_id'     => $altId,
                    'sector_id_raw'      => $sectorId,
                    'actual_sector_id'   => $actualSectorId,
                    'available_weights'  => $weights,
                    'criteria_item'      => $criteriaItem,
                    'note'               => 'Sector weight is zero - possible mapping issue'
                ]);
            }
            $finalScore = $smartScore * $sectorWeight;

            $results[] = [
                'alternative_id' => $altId,
                'system_score'   => round($systemScore, 6),
                'human_score'    => round($humanScore, 6),
                'sector_id'      => $actualSectorId,
                'weight'         => $sectorWeight,
                'smart_score'    => round($smartScore, 6),
                'final_score'    => round($finalScore, 6),
            ];
        }

        usort($results, fn($a, $b) => $b['final_score'] <=> $a['final_score']);

        // 5. Simpan ke Aggregations untuk Ranking & Provenance
        foreach ($results as $row) {
            DB::table('evaluation_aggregations')->updateOrInsert(
                [
                    'decision_session_id' => $session->id,
                    'alternative_id'      => $row['alternative_id'],
                    'method'              => 'smart_final',
                    'user_id'             => null,
                ],
                [
                    'score'      => $row['final_score'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return $results;
    }
}
