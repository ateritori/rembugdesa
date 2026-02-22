<?php

namespace App\Services\Borda;

use App\Models\DecisionSession;
use App\Models\BordaResult;
use App\Models\Alternative;

class BordaLogService
{
    public function generate(DecisionSession $session): array
    {
        // Ambil ranking SMART per DM (sumber kebenaran)
        $smartResults = \App\Models\SmartResultDm::where('decision_session_id', $session->id)
            ->with(['alternative'])
            ->orderBy('dm_id')
            ->orderBy('rank_dm')
            ->get()
            ->groupBy('dm_id');

        // Ambil hasil Borda tersimpan
        $bordaResults = BordaResult::where('decision_session_id', $session->id)
            ->with('alternative')
            ->orderBy('final_rank')
            ->get()
            ->keyBy('alternative_id');

        $matrix = [];

        foreach ($bordaResults as $altId => $borda) {

            $row = [
                'alternative_id'   => $altId,
                'alternative_name' => $borda->alternative->name ?? 'Alt #' . $altId,
                'ranks'            => [],
                'borda_score'      => $borda->borda_score,
                'final_rank'       => $borda->final_rank,
            ];

            foreach ($smartResults as $dmId => $results) {

                $rankRow = $results->firstWhere('alternative_id', $altId);

                $row['ranks'][$dmId] = $rankRow->rank_dm ?? '-';
            }

            $matrix[] = $row;
        }

        return [
            'matrix' => $matrix,
        ];
    }
}
