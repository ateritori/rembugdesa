<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AhpIndividualSubmissionService
{
    /**
     * Menyimpan hasil perbandingan berpasangan dan menghitung bobot AHP.
     */
    public function submit(DecisionSession $session, User $user, array $pairwiseData, array $rawFallback = [])
    {
        return DB::transaction(function () use ($session, $user, $pairwiseData) {

            // 1. Hapus data lama agar bersih (Mencegah Duplikat)
            CriteriaPairwise::where('decision_session_id', $session->id)
                ->where('dm_id', $user->id)
                ->delete();

            $criterias = $session->criteria()->where('is_active', true)->orderBy('id')->get();
            $n = $criterias->count();

            // Inisialisasi Matriks Identitas
            $matrix = [];
            $ids = $criterias->pluck('id')->toArray();
            foreach ($ids as $i) {
                foreach ($ids as $j) {
                    $matrix[$i][$j] = 1.0;
                }
            }

            // 2. Simpan ke Database sesuai Struktur Tabel & Isi Matriks
            // Format input dari Blade: $pairwiseData[id_i][id_j]['a_ij']
            foreach ($pairwiseData as $idI => $targets) {
                foreach ($targets as $idJ => $values) {
                    $valAIJ = (float)$values['a_ij'];

                    // Logic Konversi untuk Tabel: criteria_id_1, criteria_id_2, value, direction
                    // a_ij >= 1 artinya kriteria i (A) lebih dominan
                    $direction = ($valAIJ >= 1) ? 'left' : 'right';
                    $finalValue = ($valAIJ >= 1) ? $valAIJ : (1 / $valAIJ);

                    CriteriaPairwise::create([
                        'decision_session_id' => $session->id,
                        'dm_id'               => $user->id,
                        'criteria_id_1'       => $idI, // Menggunakan kolom sesuai tabel Anda
                        'criteria_id_2'       => $idJ, // Menggunakan kolom sesuai tabel Anda
                        'value'               => $finalValue,
                        'direction'           => $direction,
                    ]);

                    // Isi matriks untuk perhitungan AHP (menggunakan nilai asli a_ij)
                    $matrix[$idI][$idJ] = $valAIJ;
                    $matrix[$idJ][$idI] = 1 / $valAIJ;
                }
            }

            // 3. Jalankan Kalkulasi AHP
            $analysis = $this->calculateAHP($matrix, $n);

            // 4. Update Bobot Akhir (Ini opsional jika di controller sudah ada,
            // tapi bagus untuk redundancy agar data konsisten)
            CriteriaWeight::updateOrCreate(
                ['decision_session_id' => $session->id, 'dm_id' => $user->id],
                [
                    'weights'    => $analysis['weights'],
                    'cr'         => (float)$analysis['cr'],
                    'updated_at' => now()
                ]
            );

            return $analysis;
        });
    }

    /**
     * Algoritma Perhitungan AHP (Metode Normalisasi Kolom)
     */
    private function calculateAHP($matrix, $n)
    {
        if ($n <= 0) return ['weights' => [], 'cr' => 0];

        // 1. Hitung Jumlah Kolom
        $colSums = [];
        foreach ($matrix as $i => $rows) {
            foreach ($rows as $j => $val) {
                $colSums[$j] = (float)($colSums[$j] ?? 0) + (float)$val;
            }
        }

        // 2. Normalisasi Matriks & Hitung Eigenvector (Weights)
        $weights = [];
        foreach ($matrix as $i => $rows) {
            $rowSum = 0;
            foreach ($rows as $j => $val) {
                $rowSum += ($colSums[$j] > 0) ? ((float)$val / (float)$colSums[$j]) : 0;
            }
            $weights[$i] = (float)($rowSum / $n);
        }

        // 3. Kalkulasi Lambda Max
        $lambdaMax = 0;
        foreach ($colSums as $id => $sum) {
            $lambdaMax += ((float)$sum * (float)($weights[$id] ?? 0));
        }

        // 4. CI & CR
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0;
        $riTable = [1 => 0, 2 => 0, 3 => 0.58, 4 => 0.9, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49];
        $ri = $riTable[$n] ?? 1.49;
        $cr = ($ri > 0) ? ($ci / $ri) : 0;

        return [
            'weights' => $weights,
            'cr' => (float)max(0, $cr)
        ];
    }
}
