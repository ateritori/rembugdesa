<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AHP\AhpIndividualSubmissionService;

class AhpPairwiseController extends Controller
{
    /**
     * Menampilkan workspace penilaian perbandingan berpasangan (AHP).
     */
    public function index(Request $request, DecisionSession $decisionSession)
    {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403, 'Akses ditolak.');

        $isParticipant = $decisionSession->dms()->where('users.id', $user->id)->exists();
        abort_if(!$isParticipant, 403, 'Anda tidak terdaftar dalam sesi ini.');

        // Memuat data perbandingan yang sudah ada untuk state Alpine.js
        $rawPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get();

        $existingPairwise = [];
        foreach ($rawPairwise as $p) {
            $key = min($p->criteria_id_1, $p->criteria_id_2) . '-' . max($p->criteria_id_1, $p->criteria_id_2);

            // Konversi nilai database ke posisi slider (1-17)
            $pos = ($p->direction === 'left') ? (10 - $p->value) : (9 + $p->value);

            $existingPairwise[$key] = [
                'id_i' => (int) $p->criteria_id_1,
                'id_j' => (int) $p->criteria_id_2,
                'pos'  => $pos
            ];
        }

        $individualWeightExists = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->exists();

        return view('dms.index', [
            'decisionSession'   => $decisionSession,
            'criterias'         => $decisionSession->criteria()->where('is_active', true)->orderBy('order')->get(),
            'existingPairwise'  => $existingPairwise,
            'criteriaWeights'   => CriteriaWeight::where('decision_session_id', $decisionSession->id)->where('dm_id', $user->id)->first(),
            'groupResult'       => CriteriaWeight::where('decision_session_id', $decisionSession->id)->whereNull('dm_id')->first(),
            'tab'               => 'penilaian-kriteria',
            'isEditing'         => $request->get('edit') == 1 || !$individualWeightExists,
        ]);
    }

    /**
     * Menyimpan hasil penilaian perbandingan berpasangan melalui Submission Service.
     */
    public function store(Request $request, DecisionSession $decisionSession, AhpIndividualSubmissionService $service)
    {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        if (!in_array($decisionSession->status, ['configured', 'scoring'])) {
            return back()->with('error', 'Sesi penilaian sudah dikunci.');
        }

        $request->validate([
            'pairwise'       => 'required|array',
            'cr_value'       => 'required|numeric',
            'final_weights'  => 'required|json',
        ]);

        try {
            // Eksekusi penyimpanan dan kalkulasi melalui Service
            // Transaksi database dikelola di dalam service atau menggunakan koneksi model
            $service->submit(
                $decisionSession,
                $user,
                $request->input('pairwise')
            );

            return redirect()->route('dms.index', [
                'decisionSession' => $decisionSession->id,
                'tab'             => 'penilaian-kriteria',
            ])->with('success', 'Penilaian berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('AHP Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}
