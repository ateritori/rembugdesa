<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use Illuminate\Http\RedirectResponse;
use App\Services\AHP\AhpGroupWeightService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CriteriaAggregationController extends Controller
{
    /**
     * Melakukan agregasi bobot kriteria dan mengunci fase penilaian kriteria.
     * Fase ini krusial karena menggabungkan opini dari seluruh Decision Maker (DM).
     */
    public function lock(
        DecisionSession $decisionSession,
        AhpGroupWeightService $service
    ): RedirectResponse {
        // 1. Otorisasi: Hanya admin yang bisa mengunci fase
        abort_if(!auth()->user() || !auth()->user()->hasRole('admin'), 403, 'Hanya Admin yang dapat mengunci fase ini.');

        // 2. Guard: Agregasi hanya boleh dilakukan saat status masih 'scoring'
        if ($decisionSession->status !== 'scoring') {
            return back()->with('error', 'Status sesi saat ini tidak memungkinkan untuk agregasi kriteria.');
        }

        /**
         * 3. Validasi Kesiapan Data (Penting)
         * Memastikan bahwa setidaknya sudah ada DM yang melakukan penilaian
         * sebelum admin melakukan agregasi.
         */
        $hasWeights = $decisionSession->criteriaWeights()->exists();
        if (!$hasWeights) {
            return back()->with('error', 'Belum ada data penilaian dari Decision Maker (DM) untuk diagregasi.');
        }

        try {
            // 4. Gunakan Transaksi Database
            // Memastikan jika agregasi gagal, status sesi tidak berubah menjadi 'aggregated'
            return DB::transaction(function () use ($decisionSession, $service) {

                // Jalankan service agregasi kelompok (biasanya menggunakan Geometric Mean)
                $service->aggregate($decisionSession->id);

                // Update status sesi menjadi 'aggregated'
                $decisionSession->update([
                    'status' => 'aggregated',
                ]);

                return redirect()
                    ->route('control.index', $decisionSession->id)
                    ->with(
                        'success',
                        'Penilaian kriteria berhasil dikunci dan bobot kelompok (Group Weights) telah dihitung.'
                    );
            });
        } catch (\Exception $e) {
            // Log error untuk mempermudah audit jika perhitungan matematis gagal
            Log::error('Gagal melakukan agregasi kriteria: ' . $e->getMessage(), [
                'session_id' => $decisionSession->id,
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghitung agregasi: ' . $e->getMessage());
        }
    }
}
