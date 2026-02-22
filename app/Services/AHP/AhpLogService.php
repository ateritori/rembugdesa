<?php

namespace App\Services\AHP;

use App\Models\Criteria;
use App\Models\CriteriaPairwise;
use App\Models\User;

class AhpLogService
{
    protected $ahpService;

    public function __construct(AhpService $ahpService)
    {
        $this->ahpService = $ahpService;
    }

    /**
     * Generate log lengkap per DM dan bobot final kelompok
     */
    public function generateFullLog(int $decisionSessionId): array
    {
        $result = [];

        // 1. Ambil semua kriteria aktif
        $criteria = Criteria::where('decision_session_id', $decisionSessionId)
            ->where('is_active', 1)
            ->orderBy('order')
            ->get();
        $criteriaIds = $criteria->pluck('id')->toArray();
        $criteriaNames = $criteria->pluck('name')->toArray();
        $n = count($criteriaIds);

        if ($n <= 1) {
            return [
                'dm' => [],
                'gm_final' => [],
                'criteria_names' => $criteriaNames,
            ];
        }

        // 2. Ambil semua DM
        $dmIds = CriteriaPairwise::where('decision_session_id', $decisionSessionId)
            ->distinct()
            ->pluck('dm_id');

        $users = User::whereIn('id', $dmIds)->get()->keyBy('id');

        $weightsPerDM = [];

        // 3. Proses tiap DM
        foreach ($dmIds as $dmId) {
            $pairs = CriteriaPairwise::where('decision_session_id', $decisionSessionId)
                ->where('dm_id', $dmId)
                ->get();

            // Inisialisasi matriks n x n
            $matrix = array_fill(0, $n, array_fill(0, $n, 1));

            // Isi matriks dari pairwise
            foreach ($pairs as $pair) {
                $i = array_search($pair->criteria_id_1, $criteriaIds);
                $j = array_search($pair->criteria_id_2, $criteriaIds);
                if ($i === false || $j === false) continue;

                $value = $pair->value ?: 1;
                if ($pair->direction === 'left') {
                    $matrix[$i][$j] = $value;
                    $matrix[$j][$i] = 1 / $value;
                } else {
                    $matrix[$i][$j] = 1 / $value;
                    $matrix[$j][$i] = $value;
                }
            }

            // Normalisasi kolom matriks
            $colSums = array_fill(0, $n, 0);
            for ($j = 0; $j < $n; $j++) {
                for ($i = 0; $i < $n; $i++) {
                    $colSums[$j] += $matrix[$i][$j];
                }
            }
            $normalizedMatrix = [];
            for ($i = 0; $i < $n; $i++) {
                $row = [];
                for ($j = 0; $j < $n; $j++) {
                    $row[] = $matrix[$i][$j] / $colSums[$j];
                }
                $normalizedMatrix[] = $row;
            }

            // Hitung bobot DM dengan Power Method (Eigenvector)
            $weights = array_fill(0, $n, 1 / $n);

            for ($iter = 0; $iter < 100; $iter++) {
                $nextWeights = array_fill(0, $n, 0);

                for ($i = 0; $i < $n; $i++) {
                    for ($j = 0; $j < $n; $j++) {
                        $nextWeights[$i] += $matrix[$i][$j] * $weights[$j];
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

            // Hitung CR (Consistency Ratio)
            $lambdaMax = 0;
            for ($i = 0; $i < $n; $i++) {
                $sum = 0;
                for ($j = 0; $j < $n; $j++) {
                    $sum += $matrix[$i][$j] * $weights[$j];
                }
                if ($weights[$i] != 0) {
                    $lambdaMax += $sum / $weights[$i];
                }
            }
            $lambdaMax /= $n;
            $ci = ($lambdaMax - $n) / ($n - 1);
            // RI berdasarkan tabel Saaty resmi
            $riTable = [
                1 => 0,
                2 => 0,
                3 => 0.58,
                4 => 0.90,
                5 => 1.12,
                6 => 1.24,
                7 => 1.32,
                8 => 1.41,
                9 => 1.45,
                10 => 1.49,
            ];

            $ri = $riTable[$n] ?? 1.49;
            $cr = ($ri > 0) ? $ci / $ri : 0;

            $result['dm'][] = [
                'dm_id' => $dmId,
                'dm_name' => $users[$dmId]->name ?? 'DM ' . $dmId,
                'criteria_names' => $criteriaNames,
                'matrix' => $matrix,
                'weights' => array_map(fn($w) => round($w, 4), $weights),
                'cr' => round($cr, 4),
                'is_consistent' => $cr <= 0.1,
                'normalized_matrix' => $normalizedMatrix,
            ];

            $weightsPerDM[$dmId] = $weights;
        }

        // 4. Hitung GM final kelompok (dengan normalisasi)
        $gmRaw = [];
        foreach ($criteriaIds as $index => $criteriaId) {
            $prod = 1.0;
            $count = 0;
            foreach ($weightsPerDM as $w) {
                if (isset($w[$index])) {
                    $prod *= $w[$index];
                    $count++;
                }
            }
            $gmRaw[$criteriaNames[$index]] = ($count > 0)
                ? pow($prod, 1 / $count)
                : 0;
        }

        // Normalisasi agar total = 1
        $total = array_sum($gmRaw);
        $gmFinal = [];
        foreach ($gmRaw as $name => $val) {
            $gmFinal[$name] = $total > 0 ? round($val / $total, 4) : 0;
        }

        $result['gm_final'] = $gmFinal;

        $result['criteria_names'] = $criteriaNames;

        return $result;
    }
}
