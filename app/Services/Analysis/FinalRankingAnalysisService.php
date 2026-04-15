<?php

namespace App\Services\Analysis;

use App\Models\BordaAggregation;

class FinalRankingAnalysisService
{
    public function build($decisionSession)
    {
        // SMART final
        $smart = BordaAggregation::where('decision_session_id', $decisionSession->id)
            ->where('level', 'final')
            ->where('method', 'SMART')
            ->orderBy('rank')
            ->get();

        // SAW final
        $saw = BordaAggregation::where('decision_session_id', $decisionSession->id)
            ->where('level', 'final')
            ->where('method', 'SAW')
            ->orderBy('rank')
            ->get();

        $smartMap = $smart->keyBy('alternative_id');
        $sawMap   = $saw->keyBy('alternative_id');

        $allIds = collect($smartMap->keys())
            ->merge($sawMap->keys())
            ->unique();

        $comparison = [];

        foreach ($allIds as $id) {
            $s = $smartMap->get($id);
            $w = $sawMap->get($id);

            $rankSmart = $s->rank ?? null;
            $rankSaw   = $w->rank ?? null;

            $diff = (is_null($rankSmart) || is_null($rankSaw))
                ? null
                : abs($rankSmart - $rankSaw);

            $status = match (true) {
                is_null($diff) => 'INVALID',
                $diff === 0    => 'MATCH',
                default        => 'SHIFT',
            };

            $comparison[] = [
                'alternative_id' => $id,
                'name' => $s->alternative->name ?? $w->alternative->name ?? '-',
                'rank_smart' => $rankSmart,
                'rank_saw'   => $rankSaw,
                'diff' => $diff,
                'status' => $status,
            ];
        }

        $smart = $smart->map(function ($row) {
            return [
                'alternative_id' => $row->alternative_id,
                'name' => $row->alternative->name ?? '-',
                'rank' => $row->rank,
                'score' => $row->borda_score ?? null,
            ];
        })->values();

        $saw = $saw->map(function ($row) {
            return [
                'alternative_id' => $row->alternative_id,
                'name' => $row->alternative->name ?? '-',
                'rank' => $row->rank,
                'score' => $row->borda_score ?? null,
            ];
        })->values();

        // === SPEARMAN CORRELATION ===
        // Align ranks by alternative_id to ensure valid comparison
        $smartMapRank = collect($smart)->keyBy('alternative_id');
        $sawMapRank   = collect($saw)->keyBy('alternative_id');

        $commonIds = collect($smartMapRank->keys())
            ->intersect($sawMapRank->keys())
            ->values();

        $rankSmart = [];
        $rankSaw   = [];

        foreach ($commonIds as $id) {
            $rankSmart[] = $smartMapRank[$id]['rank'];
            $rankSaw[]   = $sawMapRank[$id]['rank'];
        }

        $rho = null;

        if (!empty($rankSmart) && count($rankSmart) === count($rankSaw)) {
            $n = count($rankSmart);
            $sumD = 0;

            for ($i = 0; $i < $n; $i++) {
                $d = $rankSmart[$i] - $rankSaw[$i];
                $sumD += pow($d, 2);
            }

            $rho = 1 - ((6 * $sumD) / ($n * ($n * $n - 1)));
        }

        $rhoPercentage = !is_null($rho) ? $rho * 100 : null;

        $rhoInterpretation = match (true) {
            is_null($rho) => 'Tidak valid',
            $rho >= 0.8 => 'Sangat Kuat',
            $rho >= 0.6 => 'Kuat',
            $rho >= 0.4 => 'Cukup',
            $rho >= 0.2 => 'Lemah',
            default => 'Sangat Lemah',
        };

        return [
            'smart' => $smart,
            'saw' => $saw,
            'comparison' => $comparison,
            'rho' => $rho,
            'rhoPercentage' => $rhoPercentage,
            'rhoInterpretation' => $rhoInterpretation,
        ];
    }
}
