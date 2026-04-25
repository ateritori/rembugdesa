<?php

namespace App\Services\Analysis;

use App\Models\DecisionSession;
use App\Models\EvaluationScore;
use Illuminate\Support\Facades\DB;

class SawTraceService
{
    public function build(DecisionSession $session, ?int $userId, array $finalResults)
    {
        return $this->buildUserFullTrace($session, $userId);
    }

    public function buildUserFullTrace(DecisionSession $session, ?int $userId)
    {
        if ($userId === 0) {
            throw new \Exception('User ID tidak valid');
        }

        // Alternatives
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // 🔥 ambil kriteria sesuai data aktual (sama seperti SMART)
        $assignedCriteriaIds = EvaluationScore::where('decision_session_id', $session->id)
            ->when(
                $userId === null,
                fn($q) => $q->whereNull('user_id'),
                fn($q) => $q->where('user_id', $userId)
            )
            ->pluck('criteria_id')
            ->filter()
            ->unique()
            ->toArray();

        $criteria = $session->criteria()
            ->where('is_active', true)
            ->where('level', 2)
            ->whereIn('id', $assignedCriteriaIds)
            ->get()
            ->keyBy('id');

        // 🔹 Sector weights (SAMA seperti SMART)
        $weightRecord = DB::table('criteria_group_weights')
            ->where('decision_session_id', $session->id)
            ->latest('id')
            ->first();

        $weights = [];

        if ($weightRecord) {
            $raw = $weightRecord->weights;

            if (is_string($raw)) {
                $weights = json_decode($raw, true) ?: [];
            } elseif (is_array($raw)) {
                $weights = $raw;
            }
        }

        $weights = collect($weights)
            ->mapWithKeys(function ($v, $k) {
                return [(int)$k => (float)$v];
            })
            ->all();

        // 🔹 RAW input
        $rawScores = EvaluationScore::where('decision_session_id', $session->id)
            ->when(
                $userId === null,
                fn($q) => $q->whereNull('user_id'),
                fn($q) => $q->where('user_id', $userId)
            )
            ->get()
            ->groupBy('alternative_id');

        // 🔥 SAW: MIN-MAX dari DATA AKTUAL (bukan skala)
        $globalMinMax = [];

        foreach ($criteria as $criteriaId => $c) {

            $values = EvaluationScore::where('decision_session_id', $session->id)
                ->where('criteria_id', $criteriaId)
                ->pluck('value')
                ->filter()
                ->toArray();

            if (empty($values)) {
                continue;
            }

            $globalMinMax[$criteriaId] = [
                'min' => min($values),
                'max' => max($values),
            ];
        }

        $trace = [];

        foreach ($alternatives as $altId => $alt) {

            $steps = [];
            $total = 0;

            $altRaw = $rawScores[$altId] ?? collect();

            foreach ($criteria as $criteriaId => $c) {

                $rawRow = $altRaw->first(
                    fn($r) => (int)$r->criteria_id === (int)$criteriaId
                );

                if (!$rawRow) {
                    continue;
                }

                $rawValue = $rawRow->value;

                $min = $globalMinMax[$criteriaId]['min'] ?? null;
                $max = $globalMinMax[$criteriaId]['max'] ?? null;

                if ($min === null || $max === null || $max == $min) {
                    throw new \Exception("Invalid min/max for criteria_id={$criteriaId}");
                }

                // 🔥 NORMALISASI SAW MURNI
                if ($c->type === 'cost') {
                    if ($rawValue == 0) {
                        $normalized = 0;
                    } else {
                        $normalized = $min / $rawValue;
                    }
                } else {
                    if ($max == 0) {
                        $normalized = 0;
                    } else {
                        $normalized = $rawValue / $max;
                    }
                }

                $total += $normalized;

                $steps[] = [
                    'criteria_id'   => $criteriaId,
                    'criteria_name' => $c->name ?? null,
                    'domain_id'     => $c->domain_id,
                    'type'          => $c->type,

                    'raw_value' => $rawValue,
                    'source'    => $rawRow->source ?? null,

                    'min' => $min,
                    'max' => $max,
                    'normalized' => $normalized,

                    // SAW: tidak menggunakan utility function
                    'utility' => $normalized,
                    'contribution' => $normalized,
                    'utility_function' => 'linear',
                ];
            }

            $count = count($steps);

            // 🔹 SAW SCORE (rata-rata, bukan penjumlahan)
            if ($count > 0) {
                $sawScore = $total / $count;
            } else {
                $sawScore = 0;
            }

            // 🔹 mapping weight (SAMA seperti SMART)
            $sectorId = (int) $alt->criteria_id;

            if (!isset($weights[$sectorId])) {
                throw new \Exception("Sector weight not found for sector_id={$sectorId}");
            }

            $weight = (float) $weights[$sectorId];

            $finalScore = $sawScore * $weight;

            $trace[] = [
                'alternative_id' => $altId,
                'code' => $alt->code ?? null,
                'name' => $alt->name ?? null,

                'saw_score'   => $sawScore,
                'sector_weight' => $weight,
                'final_score' => $finalScore,

                'steps' => $steps,
            ];
        }

        return $trace;
    }
}
