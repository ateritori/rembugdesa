<?php

namespace App\Services\AHP;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use App\Models\User;
use App\Services\AHP\AhpService;
use DomainException;

class AhpIndividualSubmissionService
{
    public function submit(
        DecisionSession $decisionSession,
        User $dm,
        array $frontendPairs,
        array $pairwiseInput
    ): array {
        // Ambil kriteria aktif
        $criterias = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        if ($criterias->count() < 2) {
            throw new DomainException('Kriteria tidak mencukupi.');
        }

        // Mapping ID → index
        $ids = $criterias->pluck('id')->toArray();
        $idToIndex = array_flip($ids);
        $n = count($ids);

        // Bangun matriks numerik
        $matrix = array_fill(0, $n, array_fill(0, $n, 1.0));

        foreach ($frontendPairs as $key => $pair) {
            [$id1, $id2] = array_map('intval', explode('-', $key));

            if (! isset($idToIndex[$id1], $idToIndex[$id2])) {
                continue;
            }

            $i = $idToIndex[$id1];
            $j = $idToIndex[$id2];

            $matrix[$i][$j] = (float) $pair['a_ij'];
            $matrix[$j][$i] = (float) $pair['a_ji'];
        }

        // Hitung AHP
        $ahp = app(AhpService::class)->calculate($matrix);

        if (! isset($ahp['cr']) || $ahp['cr'] >= 0.10) {
            throw new DomainException(
                'Consistency Ratio (CR) = ' .
                    round($ahp['cr'] ?? 0, 4) .
                    '. Data hanya dapat disimpan jika CR < 0.10.'
            );
        }

        // Reset data lama
        CriteriaPairwise::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $dm->id)
            ->delete();

        CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $dm->id)
            ->delete();

        // Simpan pairwise
        $processed = [];

        foreach ($pairwiseInput as $c1 => $rows) {
            foreach ($rows as $c2 => $data) {
                $id1 = (int) $c1;
                $id2 = (int) $c2;

                if ($id1 === $id2) {
                    continue;
                }

                $key = min($id1, $id2) . '-' . max($id1, $id2);
                if (isset($processed[$key])) {
                    continue;
                }
                $processed[$key] = true;

                $valIJ = (float) $data['a_ij'];
                $direction = ($valIJ >= 1) ? 'left' : 'right';
                $saveValue = ($valIJ >= 1) ? $valIJ : (1 / $valIJ);

                CriteriaPairwise::create([
                    'decision_session_id' => $decisionSession->id,
                    'dm_id'               => $dm->id,
                    'criteria_id_1'       => min($id1, $id2),
                    'criteria_id_2'       => max($id1, $id2),
                    'value'               => $saveValue,
                    'direction'           => $direction,
                ]);
            }
        }

        // Mapping bobot ke ID kriteria
        $weights = [];
        foreach ($ids as $index => $id) {
            $weights[$id] = $ahp['weights'][$index];
        }

        CriteriaWeight::create([
            'decision_session_id' => $decisionSession->id,
            'dm_id'               => $dm->id,
            'weights'             => $weights,
            'cr'                  => $ahp['cr'],
        ]);

        return [
            'cr' => $ahp['cr'],
            'weights' => $weights,
        ];
    }
}
