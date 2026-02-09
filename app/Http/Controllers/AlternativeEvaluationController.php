<?php

namespace App\Http\Controllers;

use App\Models\AlternativeEvaluation;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use App\Services\Scoring\UtilityTransformService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlternativeEvaluationController extends Controller
{
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        UtilityTransformService $utilityService
    ): RedirectResponse {
        // Guard role & authentication
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);

        // Guard status
        abort_if($decisionSession->status !== 'alternatives', 403);

        $dmId = auth()->id();
        abort_if(! $dmId, 403);

        $validated = $request->validate([
            'alternative_id' => ['required', 'exists:alternatives,id'],
            'criteria_id'    => ['required', 'exists:criteria,id'],
            'raw_value'      => ['required', 'numeric'],
        ]);

        // Ambil aturan penilaian
        $rule = CriteriaScoringRule::where('criteria_id', $validated['criteria_id'])
            ->where(function ($q) use ($decisionSession) {
                $q->whereNull('decision_session_id')
                    ->orWhere('decision_session_id', $decisionSession->id);
            })
            ->firstOrFail();

        // Hitung utilitas
        $utilityValue = $utilityService->transform(
            $rule,
            $validated['raw_value']
        );

        // Simpan (update atau create)
        AlternativeEvaluation::updateOrCreate(
            [
                'decision_session_id' => $decisionSession->id,
                'dm_id'               => $dmId,
                'alternative_id'      => $validated['alternative_id'],
                'criteria_id'         => $validated['criteria_id'],
            ],
            [
                'raw_value'     => $validated['raw_value'],
                'utility_value' => $utilityValue,
            ]
        );

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }
}
