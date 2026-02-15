<?php

namespace App\Http\Controllers;

use App\Models\AlternativeEvaluation;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use App\Services\Scoring\UtilityTransformService;
use App\Services\SMART\SmartRankingService; // Panggil Service Kebenaran
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlternativeEvaluationController extends Controller
{
    /**
     * Form penilaian alternatif (DM)
     */
    public function index(DecisionSession $decisionSession)
    {
        $user = auth()->user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        $isParticipant = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->exists();

        abort_if(!$isParticipant, 403, 'Anda tidak memiliki akses ke sesi ini.');

        return redirect()->route('dms.index', [
            $decisionSession->id,
            'tab' => 'evaluasi-alternatif'
        ]);
    }

    /**
     * Simpan penilaian & Panggil Service SMART
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        UtilityTransformService $utilityService,
        SmartRankingService $smartService // Inject Service
    ): RedirectResponse {
        $user = auth()->user();
        $dmId = auth()->id();

        abort_if(!$user || !$user->hasRole('dm'), 403);

        $isParticipant = $decisionSession->dms()->where('users.id', $dmId)->exists();
        abort_if(!$isParticipant, 403);

        if ($decisionSession->status !== 'scoring') {
            return back()->with('error', 'Sesi penilaian tidak aktif atau sudah ditutup.');
        }

        $validated = $request->validate([
            'evaluations'       => ['required', 'array', 'min:1'],
            'evaluations.*'     => ['array', 'min:1'],
            'evaluations.*.*'   => ['required', 'numeric'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $decisionSession, $user, $utilityService, $smartService) {

                $criteriaIds = collect($validated['evaluations'])->flatMap(fn($item) => array_keys($item))->unique();

                $scoringRules = CriteriaScoringRule::whereIn('criteria_id', $criteriaIds)
                    ->where(function ($q) use ($decisionSession) {
                        $q->whereNull('decision_session_id')
                            ->orWhere('decision_session_id', $decisionSession->id);
                    })
                    ->get()
                    ->keyBy('criteria_id');

                // 1. Simpan Data Mentah ke AlternativeEvaluation
                foreach ($validated['evaluations'] as $alternativeId => $criteriaValues) {
                    foreach ($criteriaValues as $criteriaId => $rawValue) {

                        $rule = $scoringRules->get($criteriaId);
                        if (!$rule) continue;

                        $utilityValue = $utilityService->transform($rule, $rawValue);

                        AlternativeEvaluation::updateOrCreate(
                            [
                                'decision_session_id' => $decisionSession->id,
                                'dm_id'               => $user->id,
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

                // 2. PANGGIL SERVICE SUMBER KEBENARAN (smart_score & rank_dm)
                // Persist = true agar langsung simpan ke smart_resul_dm
                $smartService->calculate($decisionSession, $user, true);

                return redirect()
                    ->route('dms.index', [$decisionSession->id, 'tab' => 'evaluasi-alternatif'])
                    ->with('success', 'Penilaian disimpan dan skor SMART berhasil diupdate oleh Service.');
            });
        } catch (\Exception $e) {
            Log::error('Evaluation Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
