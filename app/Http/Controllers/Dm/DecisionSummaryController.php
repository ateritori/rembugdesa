<?php

namespace App\Http\Controllers\Dm;

use App\Http\Controllers\Controller;

use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;
use Illuminate\Support\Facades\Auth;

class DecisionSummaryController extends Controller
{
    /**
     * Menampilkan ringkasan hasil penilaian SMART untuk individu Decision Maker.
     */
    public function show(
        DecisionSession $decisionSession,
        SmartRankingService $smartService
    ) {
        $user = Auth::user();

        abort_if(!$user || !$user->hasRole('dm'), 403, 'Akses ditolak.');

        // Memastikan DM terdaftar dalam sesi keputusan terkait
        $isAssigned = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->exists();

        abort_if(!$isAssigned, 403, 'Anda tidak ditugaskan pada sesi ini.');

        // Validasi akses berdasarkan fase status sesi
        $allowedStatuses = ['scoring', 'closed', 'final'];
        abort_unless(
            in_array($decisionSession->status, $allowedStatuses),
            403,
            'Hasil ringkasan belum tersedia pada tahap ini.'
        );

        // Kalkulasi skor utilitas SMART untuk individu DM
        $smartScores = $smartService->calculate($decisionSession, $user);
        $smartScores = is_array($smartScores) ? $smartScores : [];

        // Memuat data alternatif yang telah dinilai
        $alternatives = $decisionSession->alternatives()
            ->whereIn('id', array_keys($smartScores))
            ->get()
            ->keyBy('id');

        // Pemetaan data untuk presentasi tabel
        $rows = [];
        foreach ($smartScores as $altId => $score) {
            if (!isset($alternatives[$altId])) continue;

            $rows[] = [
                'alternative' => $alternatives[$altId]->name,
                'smart'       => round($score, 6),
            ];
        }

        // Pengurutan berdasarkan skor utilitas tertinggi
        usort($rows, fn($a, $b) => $b['smart'] <=> $a['smart']);

        return view('dms.summary.index', [
            'decisionSession' => $decisionSession,
            'rows'            => $rows,
            'activeTab'       => 'summary',
            'meta'            => [
                'total_alternatives' => count($rows),
                'dm_name'            => $user->name
            ]
        ]);
    }
}
