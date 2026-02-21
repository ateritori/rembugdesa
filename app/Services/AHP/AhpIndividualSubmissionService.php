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
    /**
     * Menyimpan hasil perbandingan dan bobot (Menerima hasil hitung dari JS).
     */
    public function submit(DecisionSession $session, User $user, array $pairwiseData, $passedCr = null, $passedWeights = null)
    {
        return DB::transaction(function () use ($session, $user, $pairwiseData, $passedCr, $passedWeights) {

            // 1. Bersihkan data lama
            CriteriaPairwise::where('decision_session_id', $session->id)
                ->where('dm_id', $user->id)
                ->delete();

            // 2. Simpan perbandingan ke database
            foreach ($pairwiseData as $idI => $targets) {
                foreach ($targets as $idJ => $values) {
                    $valAIJ = isset($values['a_ij']) ? (float)$values['a_ij'] : 0;

                    // Skip invalid or zero values (safety guard)
                    if ($valAIJ == 0) {
                        continue;
                    }

                    // Tentukan arah berdasarkan nilai asli dari JS
                    $direction = ($valAIJ >= 1) ? 'left' : 'right';

                    // Simpan value sebagai angka positif 1–9 (tanpa menebak ulang reciprocal)
                    $finalValue = ($valAIJ >= 1)
                        ? $valAIJ
                        : (1 / $valAIJ);

                    CriteriaPairwise::create([
                        'decision_session_id' => $session->id,
                        'dm_id'               => $user->id,
                        'criteria_id_1'       => $idI,
                        'criteria_id_2'       => $idJ,
                        'value'               => $finalValue,
                        'direction'           => $direction,
                    ]);
                }
            }

            // 3. Tentukan Bobot & CR
            // Jika dilempar dari JS, pakai itu. Jika tidak (misal hitung manual), baru jalankan rumus PHP.
            $finalWeights = $passedWeights;
            $finalCr = $passedCr;

            if (is_null($finalWeights) || is_null($finalCr)) {
                // Fallback jika tidak dikirim dari JS (Misal testing/seeder)
                // Anda tetap butuh matriks jika mau hitung ulang di sini
                $analysis = $this->calculateAHP($this->buildMatrix($session, $pairwiseData), $session->criteria->count());
                $finalWeights = $analysis['weights'];
                $finalCr = $analysis['cr'];
            }

            // 4. Update Tabel Bobot
            CriteriaWeight::updateOrCreate(
                ['decision_session_id' => $session->id, 'dm_id' => $user->id],
                [
                    'weights'    => $finalWeights,
                    'cr'         => (float)$finalCr,
                    'updated_at' => now()
                ]
            );

            return ['weights' => $finalWeights, 'cr' => $finalCr];
        });
    }

    /**
     * Algoritma Perhitungan AHP (Metode Normalisasi Kolom)
     */
    private function calculateAHP($matrix, $n)
    {
        if ($n <= 0) return ['weights' => [], 'cr' => 0];

        // 1. Power Method (Eigenvector)
        $weights = array_fill(0, $n, 1 / $n);

        for ($iter = 0; $iter < 100; $iter++) {
            $nextWeights = array_fill(0, $n, 0);

            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $nextWeights[$i] += (float)$matrix[$i][$j] * (float)$weights[$j];
                }
            }

            $sumNext = array_sum($nextWeights);
            if ($sumNext > 0) {
                for ($i = 0; $i < $n; $i++) {
                    $nextWeights[$i] /= $sumNext;
                }
            }

            $weights = $nextWeights;
        }

        // 2. Hitung Lambda Max
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0;
            for ($j = 0; $j < $n; $j++) {
                $rowSum += (float)$matrix[$i][$j] * (float)$weights[$j];
            }
            if ($weights[$i] != 0) {
                $lambdaMax += $rowSum / $weights[$i];
            }
        }
        $lambdaMax /= $n;

        // 3. Hitung CI & CR
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0;

        $riTable = [
            1 => 0,
            2 => 0,
            3 => 0.58,
            4 => 0.9,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45,
            10 => 1.49
        ];

        $ri = $riTable[$n] ?? 1.49;
        $cr = ($ri > 0) ? ($ci / $ri) : 0;

        return [
            'weights' => $weights,
            'cr' => (float)max(0, $cr)
        ];
    }
}
