<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AHP\AhpIndividualSubmissionService;

class AhpPairwiseController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('dm'), 403);

        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403
        );

        return redirect()->route('dms.index', [
            $decisionSession->id,
            'tab' => 'penilaian-kriteria'
        ]);
    }

    public function store(Request $request, DecisionSession $decisionSession, AhpIndividualSubmissionService $service)
    {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        if ($decisionSession->status !== 'configured') {
            return back()->with('error', 'Akses ditolak. Sesi penilaian saat ini sedang dikunci atau sudah diproses.');
        }

        $frontendPairs = json_decode($request->input('debug_frontend'), true);

        if (empty($frontendPairs) || !is_array($frontendPairs)) {
            return back()->withInput()->with('error', 'Data perbandingan tidak ditemukan. Silakan isi form kembali.');
        }

        $cleanPairs = [];
        foreach ($frontendPairs as $key => $values) {
            $cleanPairs[$key] = [
                'a_ij' => (float) ($values['a_ij'] ?? 1.0),
                'a_ji' => (float) ($values['a_ji'] ?? 1.0),
            ];
        }

        try {
            // 1. Jalankan service untuk menyimpan detail pairwise (tetap perlu untuk integritas data)
            $result = $service->submit(
                $decisionSession,
                $user,
                $cleanPairs,
                $request->input('pairwise', [])
            );

            /**
             * PERBAIKAN: Timpa hasil hitungan Service dengan hasil hitungan REAL-TIME JS
             * Kita ambil cr_value dan final_weights yang dikirim dari input hidden Blade
             */
            $crFromJs = $request->input('cr_value');
            $weightsFromJs = json_decode($request->input('final_weights'), true);

            if ($crFromJs !== null && !empty($weightsFromJs)) {
                // Update tabel criteria_weights dengan angka pasti dari Frontend
                CriteriaWeight::updateOrCreate(
                    [
                        'decision_session_id' => $decisionSession->id,
                        'dm_id' => $user->id,
                    ],
                    [
                        'weights' => $weightsFromJs,
                        'cr' => (float) $crFromJs,
                    ]
                );
                $cr = (float) $crFromJs;
            } else {
                // Fallback jika input hidden gagal terkirim (cadangan)
                $cr = (float) $result['cr'];
            }

            $statusLabel = $cr <= 0.1 ? "KONSISTEN" : "TIDAK KONSISTEN";
            $msg = "Penilaian berhasil disimpan! Status: {$statusLabel} (CR: " . number_format($cr, 4) . ")";

            return redirect()
                ->route('dms.weights.index', $decisionSession->id)
                ->with('success', $msg);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }
}
