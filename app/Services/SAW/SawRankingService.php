<?php

namespace App\Services\SAW;

use InvalidArgumentException;
use App\Models\DecisionSession;
use Illuminate\Support\Facades\Log;
use App\Models\AlternativeEvaluation;
use App\Models\CriteriaWeight;
use App\Models\User;
use App\Models\DmScore;

/**
 * Service perangkingan SAW.
 * Digunakan sebagai benchmark pembanding.
 */
class SawRankingService
{
    /**
     * Entry utama: menerima DecisionSession (konsisten dengan SMART)
     */
    public function calculate(DecisionSession $session, User|int $dm, bool $persist = false): array
    {
        if (is_int($dm)) {
            $dm = \App\Models\User::findOrFail($dm);
        }

        Log::info('SAW START', [
            'session_id' => $session->id,
            'dm_id' => $dm->id,
        ]);

        // 1) Ambil bobot sektor (seperti SMART)
        $groupWeight = CriteriaWeight::where('decision_session_id', $session->id)
            ->whereNull('dm_id')
            ->first();

        if (!$groupWeight || empty($groupWeight->weights)) {
            throw new InvalidArgumentException('Bobot kriteria kelompok belum tersedia.');
        }

        // Normalisasi bobot (key bisa string → cast ke int)
        $rawWeights = collect($groupWeight->weights)
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->toArray();

        $totalRawWeight = array_sum($rawWeights);
        if ($totalRawWeight <= 0) {
            throw new InvalidArgumentException('Total bobot nol.');
        }

        $normalizedWeights = [];
        foreach ($rawWeights as $sectorId => $val) {
            $normalizedWeights[$sectorId] = $val / $totalRawWeight;
        }

        Log::info('SAW WEIGHTS', [
            'weights' => $normalizedWeights,
        ]);

        // 2) Ambil evaluasi per DM (seperti SMART)
        $evaluations = AlternativeEvaluation::where('decision_session_id', $session->id)
            ->where('dm_id', $dm->id)
            ->get();

        Log::info('SAW EVALUATIONS COUNT', [
            'count' => $evaluations->count(),
            'dm_id' => $dm->id,
        ]);

        if ($evaluations->isEmpty()) return [];

        // 3) Ambil bobot kriteria per criteria_id
        $criteriaWeights = collect($rawWeights);

        // Ambil tipe kriteria (benefit / cost)
        $criteriaData = \App\Models\Criteria::whereIn('id', $evaluations->pluck('criteria_id')->unique())
            ->get(['id', 'type'])
            ->keyBy('id');

        $criteriaTypes = $criteriaData->map(fn($c) => $c->type === 'cost' ? 'cost' : 'benefit')->toArray();

        // 4) Hitung min-max per kriteria (berdasarkan criteria_id)
        $stats = [];
        foreach ($evaluations as $eval) {
            $cId = $eval->criteria_id;
            $val = (float) $eval->raw_value;

            if (!isset($stats[$cId])) {
                $stats[$cId] = ['min' => $val, 'max' => $val];
            } else {
                $stats[$cId]['min'] = min($stats[$cId]['min'], $val);
                $stats[$cId]['max'] = max($stats[$cId]['max'], $val);
            }
        }

        // 5) Hitung skor SAW per alternatif (assignment-aware)
        $scores = [];

        // Kelompokkan evaluasi per alternatif (sesuai parameter yang dinilai DM)
        $grouped = $evaluations->groupBy('alternative_id');

        foreach ($grouped as $altId => $items) {

            foreach ($items as $eval) {

                $paramId = $eval->criteria_id;
                $val = (float) $eval->raw_value;

                $max = $stats[$paramId]['max'] ?? 0;
                $min = $stats[$paramId]['min'] ?? 0;

                $type = $criteriaTypes[$paramId] ?? 'benefit';

                if ($type === 'cost') {
                    $normalized = ($val > 0) ? ($min / $val) : 0;
                } else {
                    $normalized = ($max > 0) ? ($val / $max) : 0;
                }

                Log::info('SAW DEBUG - NORMALIZATION SAW', [
                    'altId' => $altId,
                    'param_id' => $paramId,
                    'raw' => $val,
                    'min' => $min,
                    'max' => $max,
                    'normalized' => $normalized,
                    'note' => 'sector applied at alternative level',
                ]);

                // akumulasi normalized dulu (tanpa sektor di level kriteria)
                $scores[$altId] = ($scores[$altId] ?? 0)
                    + $normalized;
            }
        }

        Log::info('SAW SCORES RESULT', [
            'scores' => $scores,
        ]);

        if (empty($scores)) {
            Log::error('SAW FAILED - scores empty', [
                'session_id' => $session->id,
                'dm_id' => $dm->id,
            ]);
            return [];
        }

        // 6) Sorting deterministik (DESC score)
        $scores = collect($scores)
            ->sortByDesc(fn($score) => $score)
            ->all();

        // 7) Apply AHP weight based on alternative's main criteria_id
        $altCriteria = \App\Models\Alternative::whereIn('id', array_keys($scores))
            ->pluck('criteria_id', 'id')
            ->toArray();

        foreach ($scores as $altId => $val) {
            $criteriaId = $altCriteria[$altId] ?? null;
            $criteriaWeight = $normalizedWeights[$criteriaId] ?? 0;

            Log::info('SAW FINAL WEIGHTING', [
                'altId' => $altId,
                'criteria_id' => $criteriaId,
                'score_before' => $val,
                'weight' => $criteriaWeight,
                'score_after' => $val * $criteriaWeight,
            ]);

            $scores[$altId] = $val * $criteriaWeight;
        }

        // 8) Build ranked + persist (seperti SMART)

        $ranked = [];
        $upsertData = [];
        $rank = 1;

        foreach ($scores as $altId => $score) {
            //$sectorId = $altSectors[$altId] ?? null;
            //$sectorWeight = $normalizedWeights[$sectorId] ?? 0;
            //$finalScore = round($score * $sectorWeight, 6);
            $finalScore = round($score, 6);
            $ranked[$altId] = [
                'score' => $finalScore,
                'rank' => $rank,
            ];

            if ($persist) {
                $upsertData[] = [
                    'decision_session_id' => $session->id,
                    'dm_id' => $dm->id,
                    'alternative_id' => $altId,
                    'method' => 'saw',
                    'score' => $finalScore,
                    'updated_at' => now(),
                ];
            }
            $rank++;
        }

        if ($persist && !empty($upsertData)) {
            Log::info('SAW UPSERT EXECUTED');
            Log::info('SAW UPSERT PREP', [
                'count' => count($upsertData),
                'data' => $upsertData,
            ]);
            DmScore::upsert(
                $upsertData,
                ['decision_session_id', 'dm_id', 'alternative_id', 'method'],
                ['score', 'updated_at']
            );
        }

        return $ranked;
    }

    /**
     * Metode 1: Menghasilkan skor dan rank lengkap
     */
    public function calculateFromData(array $alternatives, array $criteriaConfig): array
    {
        if (empty($alternatives) || empty($criteriaConfig)) {
            return [];
        }

        // Transformasi ke format matrix untuk menggunakan fungsi internal yang seragam
        $matrix = [];
        $weights = [];
        $types = [];

        foreach ($criteriaConfig as $cId => $config) {
            $weights[$cId] = $config['weight'];
            $types[$cId] = $config['type'] ?? 'benefit';
        }

        foreach ($alternatives as $alt) {
            $matrix[$alt['alternative_id']] = $alt['values'];
        }

        Log::info('SAW MATRIX PREP', [
            'matrix' => $matrix,
            'weights' => $weights,
            'types' => $types,
        ]);

        $scores = $this->calculateFromMatrix($matrix, $weights, $types);

        // Deterministic sorting: SAW score DESC, alternative_id ASC
        $scores = collect($scores)
            ->sortByDesc(function ($score, $altId) {
                return [$score, -$altId]; // DESC score, ASC altId
            });

        $result = [];
        foreach ($scores as $altId => $score) {
            $result[$altId] = [
                'score' => $score,
            ];
        }

        return $result;
    }

    /**
     * Metode 2: Hitung skor langsung (Lebih efisien untuk loop Borda)
     */
    public function calculateFromMatrix(array $matrix, array $weights, array $types = []): array
    {
        if (empty($matrix) || empty($weights)) {
            return [];
        }

        $normalized = [];
        $scores = [];

        // TAHAP 1: Normalisasi per kolom (Kriteria)
        foreach ($weights as $cId => $weight) {
            $column = [];
            foreach ($matrix as $altId => $values) {
                if (isset($values[$cId])) {
                    $column[] = (float) $values[$cId];
                }
            }

            if (empty($column)) continue;

            $max = max($column);
            $min = min($column);
            $type = $types[$cId] ?? 'benefit';

            Log::info('SAW COLUMN STATS', [
                'criteria_id' => $cId,
                'column' => $column,
                'max' => $max,
                'min' => $min,
                'type' => $type,
            ]);

            foreach ($matrix as $altId => $values) {
                $val = (float) ($values[$cId] ?? 0);

                if ($type === 'cost') {
                    // Mencegah Division by Zero jika nilai mentah 0
                    $normalized[$altId][$cId] = ($val > 0) ? ($min / $val) : 0;
                } else {
                    $normalized[$altId][$cId] = ($max > 0) ? ($val / $max) : 0;
                }
            }
        }

        Log::info('SAW NORMALIZED', $normalized);

        // TAHAP 2: Perkalian Bobot & Penjumlahan
        foreach ($matrix as $altId => $values) {
            $total = 0.0;
            foreach ($weights as $cId => $weight) {
                $nVal = $normalized[$altId][$cId] ?? 0;
                $total += $nVal * (float) $weight;
            }
            $scores[$altId] = round($total, 6);
        }

        Log::info('SAW FINAL SCORES', $scores);

        return $scores;
    }
}
