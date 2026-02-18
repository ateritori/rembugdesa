<?php

namespace App\Http\Controllers\Dm;

use App\Http\Controllers\Controller;

use App\Models\AlternativeEvaluation;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use App\Services\Scoring\UtilityTransformService;
use App\Services\SMART\SmartRankingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class AlternativeEvaluationController extends Controller
{
    /**
     * Mengarahkan Decision Maker (DM) ke workspace penilaian alternatif.
     */
    public function index(DecisionSession $decisionSession)
    {
        $user = auth()->user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        $isParticipant = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->exists();

        abort_if(!$isParticipant, 403, 'Akses ditolak.');

        return redirect()->route('dms.index', [
            $decisionSession->id,
            'tab' => 'evaluasi-alternatif'
        ]);
    }

    /**
     * Menyimpan penilaian, transformasi nilai utilitas, dan kalkulasi skor SMART.
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        UtilityTransformService $utilityService,
        SmartRankingService $smartService
    ): RedirectResponse {
        $user = auth()->user();
        $dmId = auth()->id();

        abort_if(!$user || !$user->hasRole('dm'), 403);

        $isParticipant = $decisionSession->dms()->where('users.id', $dmId)->exists();
        abort_if(!$isParticipant, 403);

        if ($decisionSession->status !== 'scoring') {
            return back()->with('error', 'Sesi penilaian tidak aktif.');
        }

        $validated = $request->validate([
            'evaluations'       => ['required', 'array', 'min:1'],
            'evaluations.*'     => ['array', 'min:1'],
            'evaluations.*.*'   => ['required', 'numeric'],
        ]);

        try {
            // Menggunakan transaksi database melalui koneksi model
            return $decisionSession->getConnection()->transaction(function () use ($validated, $decisionSession, $user, $utilityService, $smartService) {

                $criteriaIds = collect($validated['evaluations'])->flatMap(fn($item) => array_keys($item))->unique();

                $scoringRules = CriteriaScoringRule::whereIn('criteria_id', $criteriaIds)
                    ->where(function ($q) use ($decisionSession) {
                        $q->whereNull('decision_session_id')
                            ->orWhere('decision_session_id', $decisionSession->id);
                    })
                    ->get()
                    ->keyBy('criteria_id');

                // 1. Persist data mentah dan nilai utilitas hasil transformasi
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

                // 2. Kalkulasi dan simpan skor SMART (Persist mode aktif)
                $smartService->calculate($decisionSession, $user, true);

                return redirect()
                    ->route('dms.index', [$decisionSession->id, 'tab' => 'evaluasi-alternatif'])
                    ->with('success', 'Penilaian disimpan dan skor SMART berhasil diperbarui.');
            });
        } catch (\Exception $e) {
            Log::error('Evaluation Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
