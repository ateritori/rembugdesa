<?php

namespace App\Services\Analysis;

use App\Models\DecisionSession;
use App\Models\EvaluationResult;
use App\Models\EvaluationScore;
use Illuminate\Support\Facades\DB;

class SmartTraceService
{
    public function build(DecisionSession $session, int $userId, array $finalResults)
    {
        if (!$userId) {
            throw new \Exception('User ID wajib dikirim ke SmartTraceService');
        }

        // =====================
        // ALTERNATIVES
        // =====================
        $alternatives = $session->alternatives()
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // =====================
        // CRITERIA
        // =====================
        $assignedCriteriaIds = $session->assignments()
            ->where('user_id', $userId)
            ->where('can_evaluate', true)
            ->pluck('criteria_id')
            ->filter()
            ->toArray();

        if (empty($assignedCriteriaIds)) {
            $assignedCriteriaIds = $session->criteria()
                ->where('is_active', true)
                ->where('level', 2)
                ->pluck('id')
                ->toArray();
        }

        $criteria = $session->criteria()
            ->where('is_active', true)
            ->where('level', 2)
            ->whereIn('id', $assignedCriteriaIds)
            ->get()
            ->keyBy('id');

        // =====================
        // 🔥 BOBOT SEKTOR (FINAL)
        // =====================
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

        // =====================
        // RESULT SMART (PURE)
        // =====================
        $results = EvaluationResult::where('decision_session_id', $session->id)
            ->where('method', 'smart')
            ->whereNotNull('criteria_id')
            ->whereNotNull('evaluation_score')
            ->get()
            ->groupBy('alternative_id');

        // =====================
        // RAW SCORE (SYSTEM + HUMAN)
        // =====================
        $rawScores = EvaluationScore::where('decision_session_id', $session->id)
            ->get()
            ->groupBy('alternative_id');

        $trace = [];

        foreach ($alternatives as $altId => $alt) {

            $steps = [];
            $totalSmart = 0;

            foreach ($criteria as $criteriaId => $c) {

                $altResults = $results[$altId] ?? collect();
                $row = $altResults->first(fn($r) => (int)$r->criteria_id === (int)$criteriaId);

                $altRaw = $rawScores[$altId] ?? collect();
                $rawRow = $altRaw->first(fn($r) => (int)$r->criteria_id === (int)$criteriaId);

                $rawValue = $rawRow->value ?? null;
                $source   = $rawRow->source ?? null;

                $utility = $row->evaluation_score ?? 0;

                // 🔥 PURE SMART (NO WEIGHT)
                $totalSmart += $utility;

                $steps[] = [
                    'criteria_id'   => $criteriaId,
                    'criteria_name' => $c->name ?? null,
                    'type'          => $c->type,

                    'raw_value' => $rawValue,
                    'source'    => $source,

                    'normalization' => null,
                    'utility'       => $utility,

                    // 🔥 TIDAK ADA BOBOT DI LEVEL INI
                    'weight'       => 1,
                    'contribution' => $utility,
                ];
            }

            // =====================
            // AVG SMART (GLOBAL)
            // =====================
            $totalCriteria = $criteria->count();

            $smartScore = $totalCriteria > 0
                ? $totalSmart / $totalCriteria
                : 0;

            // =====================
            // 🔥 FINAL (KALI BOBOT SEKTOR)
            // =====================
            $sectorId = (int) ($alt->criteria_id ?? 0);
            $weight   = (float) ($weights[$sectorId] ?? 0);

            $reconstructedFinal = $smartScore * $weight;

            $finalScore = $finalResults[$altId] ?? null;

            $trace[] = [
                'alternative_id' => $altId,
                'name' => $alt->name ?? null,

                'sector_id' => $sectorId,
                'sector_weight' => $weight,

                'smart_score' => round($smartScore, 6),
                'final_score' => $finalScore,

                'reconstructed_score' => round($reconstructedFinal, 6),

                'delta' => is_null($finalScore)
                    ? null
                    : abs($finalScore - round($reconstructedFinal, 6)),

                'steps' => $steps,
            ];
        }

        return $trace;
    }
}
