<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AhpPairwiseController extends Controller
{
    /**
     * Menampilkan workspace penilaian kriteria (AHP Pairwise).
     */
    public function index(DecisionSession $decisionSession)
    {
        $user = Auth::user();

        abort_if(!$user || !$user->hasRole('dm'), 403, 'Akses ditolak.');

        $isParticipant = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->exists();

        abort_if(!$isParticipant, 403, 'Anda tidak terdaftar dalam sesi ini.');

        $criterias = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $existingPairwise = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->mapWithKeys(function ($item) {
                // Key format i-j (ID terkecil - ID terbesar)
                $key = min($item->criteria_a_id, $item->criteria_b_id)
                    . '-' .
                    max($item->criteria_a_id, $item->criteria_b_id);

                return [$key => $item];
            });

        // Hitung kebutuhan perbandingan: n(n-1)/2
        $n = $criterias->count();
        $requiredPairs = ($n > 1) ? ($n * ($n - 1)) / 2 : 0;

        $hasCompletedPairwise = ($requiredPairs > 0) && ($existingPairwise->count() >= $requiredPairs);

        // Status read-only jika sesi sudah melewati tahap konfigurasi
        $pairwiseReadOnly = ($decisionSession->status !== 'configured');

        return view('dms.index', [
            'decisionSession'      => $decisionSession,
            'criterias'            => $criterias,
            'existingPairwise'     => $existingPairwise,
            'hasCompletedPairwise' => $hasCompletedPairwise,
            'pairwiseReadOnly'     => $pairwiseReadOnly,
            'activeTab'            => 'penilaian-kriteria',
            'n_criteria'           => $n,
            'required_pairs'       => $requiredPairs
        ]);
    }
}
