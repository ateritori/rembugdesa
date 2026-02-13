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
        // Guard dasar (SAMA POLA DENGAN KRITERIA)
        abort_if(! auth()->check() || ! auth()->user()->hasRole('dm'), 403);

        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403,
            'Anda tidak ditugaskan pada sesi ini.'
        );

        // Penilaian alternatif hanya boleh saat fase SCORING
        abort_if($decisionSession->status !== 'scoring', 403);

        $dmId = auth()->id();

        $alternatives = $decisionSession->alternatives()
            ->where('is_active', true)
            ->get();

        $criteria = $decisionSession->criteria()
            ->with(['scoringRule', 'scoringRule.parameters'])
            ->get();

        // Ambil penilaian DM (jika sudah pernah mengisi)
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $dmId)
            ->get()
            ->groupBy('alternative_id')
            ->map(fn($items) => $items->keyBy('criteria_id'));

        return view('dms.alternative-evaluations.index', [
            'decisionSession' => $decisionSession,
            'alternatives'    => $alternatives,
            'criteria'        => $criteria,
            'evaluations'     => $evaluations,
            'activeTab'       => 'evaluasi-alternatif',
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
            ->route('decision-sessions.summary', $decisionSession->id)
            ->with('success', 'Penilaian alternatif berhasil disimpan.');
    }
}
