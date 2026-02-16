<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use Illuminate\Http\RedirectResponse;
use App\Services\AHP\AhpGroupWeightService;
use Illuminate\Support\Facades\Log;

class CriteriaAggregationController extends Controller
{
    /**
     * Melakukan agregasi bobot kriteria kriteria.
     */
    public function lock(
        DecisionSession $decisionSession,
        AhpGroupWeightService $service
    ): RedirectResponse {

        abort_unless(auth()->user()?->hasRole('admin'), 403, 'Akses ditolak.');

        if ($decisionSession->status !== 'scoring') {
            return back()->with('error', 'Status sesi tidak valid untuk agregasi.');
        }

        if (!$decisionSession->criteriaWeights()->exists()) {
            return back()->with('error', 'Data penilaian kriteria dari DM belum tersedia.');
        }

        try {
            // Menggunakan koneksi model untuk transaksi database
            return $decisionSession->getConnection()->transaction(function () use ($decisionSession, $service) {

                // Menjalankan agregasi bobot kelompok
                $service->aggregate($decisionSession->id);

                $decisionSession->update([
                    'status' => 'aggregated',
                ]);

                return redirect()
                    ->route('control.index', $decisionSession->id)
                    ->with('success', 'Bobot kelompok berhasil dihitung dan fase kriteria dikunci.');
            });
        } catch (\Exception $e) {
            Log::error('Aggregation failed: ' . $e->getMessage(), [
                'session_id' => $decisionSession->id
            ]);

            return back()->with('error', 'Gagal menghitung agregasi: ' . $e->getMessage());
        }
    }
}
