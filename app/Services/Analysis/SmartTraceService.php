<?php

namespace App\Services\Analysis;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;
use App\Models\EvaluationScore;
use Illuminate\Support\Facades\DB;

class SmartTraceService
{
    public function build(DecisionSession $session, ?int $userId, array $finalResults)
    {
        // Delegate to full trace to ensure detailed steps (raw, normalized, utility)
        // userId = null means SYSTEM
        return $this->buildUserFullTrace($session, $userId);
    }

    /**
     * Build raw per-user trace (no aggregation, no normalization)
     * For auditing DM input values per alternative & criteria
     */
    public function buildPerUserTrace(DecisionSession $session, int $userId)
    {
        if (!$userId) {
            throw new \Exception('User ID wajib dikirim ke buildPerUserTrace');
        }

        $scores = EvaluationScore::where('decision_session_id', $session->id)
            ->where('user_id', $userId)
            ->get()
            ->groupBy('alternative_id');

        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $criteria = $session->criteria()
            ->where('is_active', true)
            ->where('level', 2)
            ->get()
            ->keyBy('id');

        $trace = [];

        foreach ($alternatives as $altId => $alt) {

            $rows = $scores[$altId] ?? collect();

            $steps = [];

            foreach ($criteria as $criteriaId => $c) {

                $row = $rows->first(fn($r) => (int)$r->criteria_id === (int)$criteriaId);

                $steps[] = [
                    'criteria_id'   => $criteriaId,
                    'criteria_name' => $c->name ?? null,
                    'value'         => $row->value ?? null,
                    'source'        => $row->source ?? null,
                ];
            }

            $trace[] = [
                'alternative_id'   => $altId,
                'alternative_name' => $alt->name ?? null,
                'steps'            => $steps,
            ];
        }

        return $trace;
    }

    /**
     * Build full trace from USER INPUT → UTILITY → SMART → FINAL
     * This recomputes scores per user using EvaluationResult (utility reference)
     */
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

        // 🔥 gunakan kriteria berdasarkan data aktual (match Excel pivot)
        if ($userId === null) {
            $assignedCriteriaIds = EvaluationScore::where('decision_session_id', $session->id)
                ->whereNull('user_id')
                ->pluck('criteria_id')
                ->filter()
                ->unique()
                ->toArray();
        } else {
            $assignedCriteriaIds = EvaluationScore::where('decision_session_id', $session->id)
                ->where('user_id', $userId)
                ->pluck('criteria_id')
                ->filter()
                ->unique()
                ->toArray();
        }

        $criteria = $session->criteria()
            ->where('is_active', true)
            ->where('level', 2)
            ->whereIn('id', $assignedCriteriaIds)
            ->get()
            ->keyBy('id');

        // Sector weights
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
            ->mapWithKeys(fn($v, $k) => [(int)$k => (float)$v])
            ->all();

        // RAW USER INPUT
        $rawQuery = EvaluationScore::where('decision_session_id', $session->id);
        if ($userId === null) {
            $rawQuery->whereNull('user_id');
        } else {
            $rawQuery->where('user_id', $userId);
        }
        $rawScores = $rawQuery->get()->groupBy('alternative_id');

        // SYSTEM UTILITY (REFERENCE)
        $results = EvaluationResult::where('decision_session_id', $session->id)
            ->where('method', 'smart')
            ->get()
            ->groupBy('alternative_id');

        // 🔥 GLOBAL min-max per criteria (Excel-compatible + rules)
        $rules = DB::table('criteria_scoring_rules')
            ->where('decision_session_id', $session->id)
            ->get()
            ->keyBy('criteria_id');

        $globalMinMax = [];
        foreach ($criteria as $criteriaId => $c) {
            $rule = $rules[$criteriaId] ?? null;
            // 👉 jika skala → pakai rule (bukan data)
            if ($rule && $rule->input_type === 'scale') {
                $globalMinMax[$criteriaId] = [
                    'min' => (float) $rule->scale_min,
                    'max' => (float) $rule->scale_max,
                ];
                continue;
            }
            // 👉 numeric → ambil dari data aktual
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
            $totalUtility = 0;

            $altRaw = $rawScores[$altId] ?? collect();
            $altResults = $results[$altId] ?? collect();

            foreach ($criteria as $criteriaId => $c) {

                $rawRow = $altRaw->first(fn($r) => (int)$r->criteria_id === (int)$criteriaId);
                // $resultRow = $altResults->first(fn($r) => (int)$r->criteria_id === (int)$criteriaId);

                $rawValue = $rawRow->value ?? null;

                // ❗ skip jika tidak dinilai oleh DM
                if (!$rawRow) {
                    continue;
                }

                $min = $globalMinMax[$criteriaId]['min'] ?? null;
                $max = $globalMinMax[$criteriaId]['max'] ?? null;

                $normalized = null;

                if ($rawValue !== null && $min !== null && $max !== null && $max != $min) {
                    if ($c->type === 'cost') {
                        $normalized = ($max - $rawValue) / ($max - $min);
                    } else {
                        $normalized = ($rawValue - $min) / ($max - $min);
                    }

                    $normalized = max(0, min(1, $normalized));
                }

                // 🔥 utility with curve (Excel-compatible)
                $rule = $rules[$criteriaId] ?? null;
                $utility = $this->transformUtility($normalized, $rule);

                $totalUtility += $utility;

                $steps[] = [
                    'criteria_id'   => $criteriaId,
                    'criteria_name' => $c->name ?? null,
                    'type'          => $c->type,

                    'raw_value' => $rawValue,
                    'source'    => $rawRow->source ?? null,

                    'min' => $min,
                    'max' => $max,
                    'normalized' => $normalized,

                    'utility' => $utility,
                    'contribution' => $utility,
                    'utility_function' => $rule->utility_function ?? 'linear',
                ];
            }

            $count = count($steps);

            $smartScore = $count > 0
                ? $totalUtility / $count
                : 0;

            $sectorId = (int) ($alt->criteria_id ?? 0);
            $weight   = (float) ($weights[$sectorId] ?? 0);

            $finalScore = $smartScore * $weight;

            $trace[] = [
                'alternative_id' => $altId,
                'code' => $alt->code ?? null,
                'name' => $alt->name ?? null,

                'smart_score' => round($smartScore, 6),
                'sector_weight' => $weight,
                'final_score' => round($finalScore, 6),

                'steps' => $steps,
            ];
        }

        return $trace;
    }

    /**
     * Transform normalized value using utility function rule.
     */
    private function transformUtility($normalized, $rule)
    {
        if ($normalized === null) {
            return 0;
        }

        if (!$rule) {
            return $normalized;
        }

        if ($rule->utility_function === 'concave') {
            return pow($normalized, $rule->curve_degree ?? 0.2);
        }

        if ($rule->utility_function === 'convex') {
            return pow($normalized, $rule->curve_degree ?? 4);
        }

        return $normalized;
    }
}
