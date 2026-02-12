<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use Illuminate\Http\RedirectResponse;
use App\Services\AHP\AhpGroupWeightService;

class CriteriaAggregationController extends Controller
{
    /**
     * Aggregate criteria weights and lock criteria phase.
     */
    public function lock(
        DecisionSession $decisionSession,
        AhpGroupWeightService $service
    ): RedirectResponse {
        abort_if(! auth()->user()->hasRole('admin'), 403);

        // Aggregasi kriteria hanya boleh saat scoring
        abort_if($decisionSession->status !== 'scoring', 403);

        // Run group aggregation
        $service->aggregate($decisionSession->id);

        // Pindah ke fase agregasi
        $decisionSession->update([
            'status' => 'aggregated',
        ]);

        return redirect()
            ->route(
                'control.index',
                $decisionSession->id
            )
            ->with(
                'success',
                'Penilaian kriteria dikunci dan bobot kelompok berhasil dibentuk.'
            );
    }
}
