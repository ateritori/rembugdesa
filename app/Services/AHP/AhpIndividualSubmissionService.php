<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AhpIndividualSubmissionService
{
    public function submit(DecisionSession $session, User $user, array $cleanPairs, array $rawFallback)
    {
        return DB::transaction(function () use ($session, $user, $cleanPairs) {
            // Hapus data lama
            CriteriaPairwise::where('decision_session_id', $session->id)
                ->where('dm_id', $user->id)
                ->delete();

            // Simpan pairwise baru
            foreach ($cleanPairs as $key => $val) {
                $ids = explode('-', $key);
                CriteriaPairwise::create([
                    'decision_session_id' => $session->id,
                    'dm_id' => $user->id,
                    'criteria_id_1' => (int)$ids[0],
                    'criteria_id_2' => (int)$ids[1],
                    'value' => (float)$val['a_ij'],
                    'direction' => $val['a_ij'] >= 1 ? 'left' : 'right',
                ]);
            }

            $criterias = $session->criteria()->where('is_active', true)->orderBy('id')->get();
            $n = $criterias->count();

            // Bangun Matriks
            $matrix = [];
            $ids = $criterias->pluck('id')->toArray();
            foreach ($ids as $i) {
                foreach ($ids as $j) {
                    $matrix[$i][$j] = 1.0;
                }
            }

            foreach ($cleanPairs as $key => $val) {
                $exploded = explode('-', $key);
                $id1 = (int)$exploded[0];
                $id2 = (int)$exploded[1];
                $matrix[$id1][$id2] = (float)$val['a_ij'];
                $matrix[$id2][$id1] = (float)$val['a_ji'];
            }

            // Jalankan Kalkulasi AHP
            $analysis = $this->calculateAHP($matrix, $n);

            CriteriaWeight::updateOrCreate(
                ['decision_session_id' => $session->id, 'dm_id' => $user->id],
                ['weights' => $analysis['weights'], 'cr' => (float)$analysis['cr']]
            );

            return $analysis;
        });
    }

    private function calculateAHP($matrix, $n)
    {
        // 1. Normalisasi & Eigenvector
        $colSums = [];
        foreach ($matrix as $i => $rows) {
            foreach ($rows as $j => $val) {
                $colSums[$j] = (float)($colSums[$j] ?? 0) + (float)$val;
            }
        }

        $weights = [];
        foreach ($matrix as $i => $rows) {
            $rowSum = 0;
            foreach ($rows as $j => $val) {
                $rowSum += ((float)$val / (float)$colSums[$j]);
            }
            $weights[$i] = (float)($rowSum / $n);
        }

        // 2. Lambda Max
        $lambdaMax = 0;
        foreach ($colSums as $id => $sum) {
            $lambdaMax += ((float)$sum * (float)$weights[$id]);
        }

        // 3. Consistency Ratio
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0;
        $riTable = [1 => 0, 2 => 0, 3 => 0.58, 4 => 0.9, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49];
        $ri = $riTable[$n] ?? 1.49;
        $cr = $ri > 0 ? $ci / $ri : 0;

        return ['weights' => $weights, 'cr' => (float)$cr];
    }
}
