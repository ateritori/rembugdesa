<?php

namespace App\Http\Controllers;

use App\Models\AlternativeEvaluation;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use App\Services\Scoring\UtilityTransformService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AlternativeEvaluationController extends Controller
{
    /**
     * Form penilaian alternatif (DM)
     */
    public function index(DecisionSession $decisionSession)
    {
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);

        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403
        );

        return redirect()->route('dms.index', [
            $decisionSession->id,
            'tab' => 'evaluasi-alternatif'
        ]);
    }

    /**
     * Simpan / update penilaian alternatif (DM)
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        UtilityTransformService $utilityService
    ): RedirectResponse {
        // Guard dasar
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);

        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403
        );

        // KONSISTEN DENGAN index()
        abort_if($decisionSession->status !== 'scoring', 403);

        $dmId = auth()->id();
        abort_if(! $dmId, 403);

        $validated = $request->validate(
            [
                'evaluations'       => ['required', 'array', 'min:1'],
                'evaluations.*'     => ['array', 'min:1'],
                'evaluations.*.*'   => ['required', 'numeric'],
            ],
            [
                'evaluations.required'      => 'Penilaian belum diisi.',
                'evaluations.min'           => 'Minimal satu alternatif harus dinilai.',
                'evaluations.*.*.required'  => 'Semua kriteria harus diberi penilaian.',
                'evaluations.*.*.numeric'   => 'Nilai penilaian tidak valid.',
            ]
        );

        foreach ($validated['evaluations'] as $alternativeId => $criteriaValues) {
            foreach ($criteriaValues as $criteriaId => $rawValue) {

                // Ambil scoring rule (global atau khusus sesi)
                $rule = CriteriaScoringRule::where('criteria_id', $criteriaId)
                    ->where(function ($q) use ($decisionSession) {
                        $q->whereNull('decision_session_id')
                            ->orWhere('decision_session_id', $decisionSession->id);
                    })
                    ->firstOrFail();

                // Transform ke utility 0–1
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

        return redirect()
            ->route('dms.index', $decisionSession->id)
            ->with('success', 'Penilaian alternatif berhasil disimpan.');
    }
}
