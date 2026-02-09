<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use Illuminate\Http\Request;

class DecisionControlController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        // 1. Ambil data dasar via relasi
        $activeCriteriaCount = $decisionSession->criteria()->where('is_active', true)->count();
        $activeAlternativesCount = $decisionSession->alternatives()->where('is_active', true)->count();

        // 2. Hitung jumlah pasangan (pairs) yang WAJIB diisi
        $requiredCriteriaPairs = $activeCriteriaCount > 1
            ? ($activeCriteriaCount * ($activeCriteriaCount - 1)) / 2
            : 0;

        // 3. Ambil DM dan batasi hitungan relasi pairwise hanya untuk SESSION_ID ini
        $assignedDms = $decisionSession->dms()->withCount([
            'criteriaPairwise' => function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id);
            }
            // Tambahkan alternativesPairwise jika modelnya sudah siap
        ])->get();

        $assignedDmCount = $assignedDms->count();

        // 4. Hitung berapa banyak DM yang jumlah pengisiannya >= syarat wajib
        $dmPairwiseDone = $assignedDms->filter(function ($dm) use ($requiredCriteriaPairs) {
            return $requiredCriteriaPairs > 0 && $dm->criteria_pairwise_count >= $requiredCriteriaPairs;
        })->count();

        return view('control.index', compact(
            'decisionSession',
            'activeCriteriaCount',
            'activeAlternativesCount',
            'assignedDmCount',
            'assignedDms',
            'dmPairwiseDone',
            'requiredCriteriaPairs'
        ));
    }
}
