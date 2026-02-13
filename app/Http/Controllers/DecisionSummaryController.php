<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;

class DecisionSummaryController extends Controller
{
    /**
     * Ringkasan hasil akhir (DM)
     */
    public function show(
        DecisionSession $decisionSession,
        SmartRankingService $smartService
    ) {
        // Guard dasar (SAMA POLA DENGAN CONTROLLER LAIN)
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);

        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403,
            'Anda tidak ditugaskan pada sesi ini.'
        );

        // Ringkasan hanya boleh setelah agregasi
        abort_if(
            ! in_array($decisionSession->status, ['aggregated', 'final'], true),
            403
        );

        // Hitung skor SMART untuk DM ini
        $smartScores = $smartService->calculate(
            $decisionSession->id,
            auth()->id()
        );

        // Ambil alternatif yang relevan
        $alternatives = $decisionSession->alternatives()
            ->whereIn('id', array_keys($smartScores))
            ->get()
            ->keyBy('id');

        // Susun data tabel
        $rows = [];
        foreach ($smartScores as $altId => $score) {
            $rows[] = [
                'alternative' => $alternatives[$altId]->name ?? '-',
                'smart'       => round($score, 6),
            ];
        }

        // Urutkan dari skor tertinggi
        usort($rows, fn($a, $b) => $b['smart'] <=> $a['smart']);

        return view('dms.summary.index', [
            'decisionSession' => $decisionSession,
            'rows'            => $rows,
            'activeTab'       => 'summary',
        ]);
    }
}
