<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\EvaluationScore;
use Illuminate\Support\Facades\DB;

class SystemEvaluationService
{
    public function generate(DecisionSession $session): void
    {
        DB::transaction(function () use ($session) {

            // Ambil hanya kriteria system
            $criteria = $session->criteria()
                ->where('level', 2)
                ->where('evaluator_type', 'system')
                ->get();

            if ($criteria->isEmpty()) {
                return;
            }

            $alternatives = $session->alternatives()
                ->where('is_active', true)
                ->get();

            // Hapus data system lama
            $session->evaluationScores()
                ->where('source', 'system')
                ->delete();

            $rows = [];
            $now = now();

            foreach ($criteria as $c) {
                foreach ($alternatives as $a) {

                    $value = $this->calculate($c, $a);

                    $rows[] = [
                        'decision_session_id' => $session->id,
                        'user_id'             => null,
                        'criteria_id'         => $c->id,
                        'alternative_id'      => $a->id,
                        'value'               => $value,
                        'source'              => 'system',
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
            }

            if (!empty($rows)) {
                EvaluationScore::insert($rows);
            }
        });
    }

    protected function calculate($criteria, $alternative): float
    {
        // Mapping sederhana (bisa kamu kembangkan nanti)
        switch ($criteria->id) {

            case 6: // RAB
                return $alternative->rab ?? 0;

            case 7: // Penerima Manfaat
                return $alternative->beneficiaries ?? 0;

            case 8: // Cakupan Wilayah
                return $alternative->coverage ?? 0;

            default:
                return 0;
        }
    }
}
