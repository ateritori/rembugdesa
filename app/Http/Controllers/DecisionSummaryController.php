<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;
use Illuminate\Support\Facades\Auth;

class DecisionSummaryController extends Controller
{
    /**
     * Menampilkan ringkasan hasil penilaian SMART untuk individu DM.
     */
    public function show(
        DecisionSession $decisionSession,
        SmartRankingService $smartService
    ) {
        $user = Auth::user();

        // 1. Guard Dasar: Pastikan user adalah DM
        abort_if(!$user || !$user->hasRole('dm'), 403, 'Akses ditolak.');

        // 2. Guard Penugasan: Pastikan DM terdaftar di sesi ini
        $isAssigned = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->exists();

        abort_if(!$isAssigned, 403, 'Anda tidak ditugaskan pada sesi ini.');

        // 3. Guard Status Sesi
        // Ringkasan biasanya tersedia saat sesi mulai masuk tahap penilaian (scoring) hingga selesai (closed/final)
        $allowedStatuses = ['scoring', 'closed', 'final'];
        abort_unless(
            in_array($decisionSession->status, $allowedStatuses),
            403,
            'Hasil ringkasan belum tersedia pada tahap ini.'
        );

        /**
         * 4. Hitung skor SMART untuk DM ini
         * Mematuhi kontrak Service: calculate(DecisionSession $session, User $user)
         */
        $smartScores = $smartService->calculate($decisionSession, $user);

        // Pastikan $smartScores adalah array agar tidak error saat dihitung
        $smartScores = is_array($smartScores) ? $smartScores : [];

        // 5. Ambil data alternatif yang relevan
        $alternatives = $decisionSession->alternatives()
            ->whereIn('id', array_keys($smartScores))
            ->get()
            ->keyBy('id');

        // 6. Susun data tabel (Data Mapping)
        $rows = [];
        foreach ($smartScores as $altId => $score) {
            if (!isset($alternatives[$altId])) continue;

            $rows[] = [
                'alternative' => $alternatives[$altId]->name,
                'smart'       => round($score, 6),
            ];
        }

        // 7. Sorting: Urutkan dari skor utilitas tertinggi
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
