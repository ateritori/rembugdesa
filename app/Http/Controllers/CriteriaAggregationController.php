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

        // Only allowed when session is active
        abort_if($decisionSession->status !== 'active', 403);

        // Run group aggregation
        $service->aggregate($decisionSession->id);

        // Move session to alternative evaluation phase
        $decisionSession->update([
            'status' => 'alternatives',
        ]);

        return redirect()
            ->to(
                route('decision-sessions.show', $decisionSession->id)
                    . '?tab=control'
            )
            ->with(
                'success',
                'Penilaian kriteria dikunci dan bobot kelompok berhasil dibentuk.'
            );
    }
}
