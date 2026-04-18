<?php

namespace App\Services\Evaluation;

use App\Models\DecisionSession;
use App\Models\Alternative;
use App\Models\EvaluationScore;
use Illuminate\Support\Facades\DB;

class SystemEvaluationService
{
    /**
     * Generate system evaluation scores from alternatives
     */
    public function generate(DecisionSession $session): void
    {
        DB::transaction(function () use ($session) {

            $alternatives = Alternative::where('decision_session_id', $session->id)
                ->where('is_active', true)
                ->get();

            $criteria = $session->criteria()
                ->where('is_active', true)
                ->get();

            $rows = [];
            $now = now();

            foreach ($alternatives as $alternative) {
                foreach ($criteria as $criterion) {

                    $value = $this->mapValue($alternative, $criterion);

                    if ($value === null) {
                        continue;
                    }

                    $rows[] = [
                        'decision_session_id' => $session->id,
                        'user_id'             => null,
                        'criteria_id'         => $criterion->id,
                        'alternative_id'      => $alternative->id,
                        'value'               => (float) $value,
                        'source'              => 'system',
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
            }

            EvaluationScore::insert($rows);
        });
    }

    /**
     * ONLY mapping real fields from alternatives
     */
    protected function mapValue($alternative, $criterion)
    {
        return match ($criterion->code ?? null) {
            'rab'            => $alternative->rab,
            'coverage'       => $alternative->coverage,
            'beneficiaries'  => $alternative->beneficiaries,
            default          => null,
        };
    }
}
