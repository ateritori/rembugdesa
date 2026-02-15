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

        // 1. Otorisasi: Pastikan user login, memiliki role DM, dan terdaftar di sesi ini
        abort_if(!$user || !$user->hasRole('dm'), 403, 'Akses ditolak.');

        $isParticipant = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->exists();

        abort_if(!$isParticipant, 403, 'Anda tidak terdaftar dalam sesi ini.');

        // 2. Ambil kriteria aktif (Urutkan berdasarkan 'order' untuk konsistensi matriks)
        $criterias = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        /**
         * 3. Ambil hasil penilaian DM (jika sudah ada)
         * Gunakan mapWithKeys untuk memudahkan pencarian di View/Blade
         */
        $existingPairwise = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->mapWithKeys(function ($item) {
                // Pastikan key konsisten dengan format i-j (ID kecil - ID besar)
                $key = min($item->criteria_a_id, $item->criteria_b_id)
                    . '-' .
                    max($item->criteria_a_id, $item->criteria_b_id);

                return [$key => $item];
            });

        // 4. Hitung kebutuhan perbandingan berpasangan
        // Rumus AHP: n(n-1)/2
        $n = $criterias->count();
        $requiredPairs = ($n > 1) ? ($n * ($n - 1)) / 2 : 0;

        /**
         * 5. Logika Status & Mode Read-Only
         * Pairwise dianggap lengkap jika jumlah record di DB sesuai dengan requiredPairs.
         */
        $hasCompletedPairwise = ($requiredPairs > 0) && ($existingPairwise->count() >= $requiredPairs);

        // Penilaian dikunci (Read-Only) jika status sesi sudah lewat dari 'configured'
        // Namun, jika status masih 'configured' tapi user ingin mengedit, jangan kunci secara permanen.
        // Di bawah ini saya sesuaikan agar user tetap bisa mengedit selama status sesi masih 'configured'.
        $pairwiseReadOnly = ($decisionSession->status !== 'configured');

        return view('dms.index', [
            'decisionSession'      => $decisionSession,
            'criterias'            => $criterias,
            'existingPairwise'     => $existingPairwise,
            'hasCompletedPairwise' => $hasCompletedPairwise,
            'pairwiseReadOnly'     => $pairwiseReadOnly,
            'activeTab'            => 'penilaian-kriteria', // Sesuai dengan route redirect di controller sebelumnya
            'n_criteria'           => $n,
            'required_pairs'       => $requiredPairs
        ]);
    }
}
