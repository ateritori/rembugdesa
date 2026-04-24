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

        // 🔥 MIN-MAX mengikuti SmartTraceService (rules-aware)
        $rules = DB::table('criteria_scoring_rules')
            ->where('decision_session_id', $session->id)
            ->get()
            ->mapWithKeys(fn($r) => [(int)$r->criteria_id => $r]);

        $globalMinMax = [];

        foreach ($criteria as $criteriaId => $c) {

            $criteriaId = (int) $criteriaId;
            $rule = $rules[$criteriaId] ?? null;

            if (!$rule) {
                throw new \Exception("Missing scoring rule for criteria_id={$criteriaId}");
            }

            // 👉 jika skala → pakai rule (bukan data)
            if ($rule->input_type === 'scale') {
                $globalMinMax[$criteriaId] = [
                    'min' => (float) $rule->scale_min,
                    'max' => (float) $rule->scale_max,
                ];
                continue;
            }

            // 👉 numeric → ambil dari data aktual (GLOBAL, bukan per user)
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

                // 🔥 NORMALISASI SAW
                if ($c->type === 'cost') {
                    $normalized = $min / $rawValue;
                } else {
                    $normalized = $rawValue / $max;
                }

                // clamp biar aman
                $normalized = max(0, min(1, $normalized));

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

                    // ❗ beda dari SMART
                    'utility' => null,
                    'contribution' => $normalized,
                    'utility_function' => null,
                ];
            }

            $count = count($steps);

            // 🔹 SAW SCORE (rata-rata atau langsung sum juga bisa)
            $sawScore = $count > 0
                ? $total / $count
                : 0;

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

                'smart_score' => null, // biar kompatibel
                'saw_score'   => $sawScore,
                'sector_weight' => $weight,
                'final_score' => $finalScore,

                'steps' => $steps,
            ];
        }

        return $trace;
    }
}
