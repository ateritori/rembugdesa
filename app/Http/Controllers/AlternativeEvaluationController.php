<?php

namespace App\Http\Controllers;

use App\Models\AlternativeEvaluation;
use App\Models\Criteria;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use App\Services\Scoring\UtilityTransformService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlternativeEvaluationController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);
        abort_if($decisionSession->status !== 'alternatives', 403);

        $dmId = auth()->id();

        $alternatives = $decisionSession->alternatives()
            ->where('is_active', true)
            ->get();

        $criteria = $decisionSession->criteria()
            ->with(['scoringRule', 'scoringRule.parameters'])
            ->get();

        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $dmId)
            ->get()
            ->groupBy('alternative_id')
            ->map(fn($items) => $items->keyBy('criteria_id'));

        return view('alternative-evaluations.index', [
            'decisionSession' => $decisionSession,
            'alternatives'    => $alternatives,
            'criteria'        => $criteria,
            'evaluations'     => $evaluations,
        ]);
    }

    public function store(
        Request $request,
        DecisionSession $decisionSession,
        UtilityTransformService $utilityService
    ): RedirectResponse {
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);
        abort_if($decisionSession->status !== 'alternatives', 403);

        $dmId = auth()->id();
        abort_if(! $dmId, 403);

        $validated = $request->validate(
            [
                'evaluations' => ['required', 'array', 'min:1'],
                'evaluations.*' => ['array', 'min:1'],
                'evaluations.*.*' => ['required', 'numeric'],
            ],
            [
                'evaluations.required' => 'Penilaian belum diisi.',
                'evaluations.min'      => 'Minimal satu alternatif harus dinilai.',
                'evaluations.*.*.required' => 'Semua kriteria harus diberi penilaian.',
                'evaluations.*.*.numeric'  => 'Nilai penilaian tidak valid.',
            ]
        );

        foreach ($validated['evaluations'] as $alternativeId => $criteriaValues) {
            foreach ($criteriaValues as $criteriaId => $rawValue) {

                $rule = CriteriaScoringRule::where('criteria_id', $criteriaId)
                    ->where(function ($q) use ($decisionSession) {
                        $q->whereNull('decision_session_id')
                            ->orWhere('decision_session_id', $decisionSession->id);
                    })
                    ->firstOrFail();

                $utilityValue = $utilityService->transform($rule, $rawValue);

                AlternativeEvaluation::updateOrCreate(
                    [
                        'decision_session_id' => $decisionSession->id,
                        'dm_id'               => $dmId,
                        'alternative_id'      => $alternativeId,
                        'criteria_id'         => $criteriaId,
                    ],
                    [
                        'raw_value'     => $rawValue,
                        'utility_value' => $utilityValue,
                    ]
                );
            }
        }

        if (empty($validated['evaluations'])) {
            return back()->with('warning', 'Tidak ada penilaian yang disimpan.');
        }

        return back()->with('success', 'Semua penilaian alternatif berhasil disimpan.');
    }
}
