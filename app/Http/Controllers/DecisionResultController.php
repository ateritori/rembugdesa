<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;
use App\Services\BORDA\BordaRankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DecisionResultController extends Controller
{
    /**
     * Menampilkan hasil akhir keputusan.
     */
    public function show(
        DecisionSession $decisionSession,
        SmartRankingService $smartService,
        BordaRankingService $bordaService
    ) {
        // 1. Guard
        abort_if($decisionSession->status !== 'final', 403, 'Hasil belum tersedia.');

        /**
         * 2. Menangani Error P1006
         * Karena Service mewajibkan type App\Models\User, kita kirimkan Auth::user().
         * Pastikan middleware 'auth' sudah terpasang agar Auth::user() tidak null.
         */
        $user = Auth::user();
        $smartScores = $smartService->calculate($decisionSession, $user);

        // 3. Kalkulasi Borda & Ranking
        $bordaScores = $bordaService->calculate($smartScores);
        $finalRanking = $bordaService->ranking($bordaScores);

        // 4. Data Alternatif
        $alternatives = $decisionSession->alternatives()
            ->whereIn('id', array_keys($finalRanking))
            ->get()
            ->keyBy('id');

        // 5. Transformasi untuk View
        $rows = [];
        foreach ($finalRanking as $altId => $rank) {
            $rows[] = [
                'rank'        => $rank,
                'alternative' => $alternatives[$altId]->name ?? '-',
                'smart'       => isset($smartScores[$altId]) ? round($smartScores[$altId], 6) : 0,
                'borda'       => $bordaScores[$altId] ?? 0,
            ];
        }

        $view = $user->hasRole('dm') ? 'dms.summary.index' : 'decision-sessions.result';

        return view($view, [
            'decisionSession' => $decisionSession,
            'rows'            => $rows,
        ]);
    }
}
