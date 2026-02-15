<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\AlternativeEvaluation;
use App\Models\SmartResultDm;
use App\Services\SMART\SmartRankingService;
use App\Services\Result\DecisionResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class DecisionMakerController extends Controller
{
    public function index(DecisionSession $decisionSession, SmartRankingService $smartRankingService)
    {
        return $this->renderWorkspace($decisionSession, $smartRankingService);
    }

    public function weights(DecisionSession $decisionSession)
    {
        return $this->renderWorkspace($decisionSession);
    }

    public function groupWeights(DecisionSession $decisionSession)
    {
        return $this->renderWorkspace($decisionSession);
    }

    /**
     * PUSAT LOGIKA - Menjamin tidak ada fitur yang hilang dan view tidak crash.
     */
    private function renderWorkspace(DecisionSession $decisionSession, ?SmartRankingService $smartRankingService = null)
    {
        // 1. Otorisasi & Guard
        abort_if($decisionSession->status === 'draft', 403);
        $user = Auth::user();
        abort_if(!$decisionSession->dms()->where('users.id', $user->id)->exists(), 403, 'Anda tidak ditugaskan.');

        $currentTab = request('tab', 'workspace');

        // 2. Data Dasar (Selalu Ada)
        $criteria = $decisionSession->criteria()->where('is_active', true)->orderBy('order')->get();
        $alternatives = $decisionSession->alternatives()->where('is_active', true)->get();

        $groupResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')->first();

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)->first();

        // 3. Evaluasi Alternatif
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)->get()
            ->groupBy('alternative_id')
            ->map(fn($items) => $items->keyBy('criteria_id'));

        $hasCompletedEvaluation = $evaluations->isNotEmpty();

        // 4. Kalkulasi SMART (Hanya dilakukan jika Service tersedia & tab sesuai/index)
        $smartScores = collect();
        $hasSmartResult = false;

        if ($smartRankingService && $groupResult && $hasCompletedEvaluation) {
            $lastEvaluationUpdate = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', $user->id)->max('updated_at');
            $lastSmartUpdate = SmartResultDm::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', $user->id)->max('updated_at');

            $shouldPersist = is_null($lastSmartUpdate) || ($lastEvaluationUpdate > $lastSmartUpdate);

            try {
                $scores = $smartRankingService->calculate($decisionSession, $user, $shouldPersist);
                $smartScores = collect($scores)->sortByDesc('score');
                $hasSmartResult = $smartScores->isNotEmpty();
            } catch (Exception $e) {
                $smartScores = collect();
            }
        }

        // 5. Kontribusi DM (Hasil Akhir)
        $resultContribution = null;
        if ($currentTab === 'hasil-akhir' && $decisionSession->status === 'closed') {
            $resultContribution = app(DecisionResultService::class)->dmContribution($decisionSession, $user);
        }

        // 6. Pengiriman ke View (Sesuai variabel yang Anda butuhkan)
        return view('dms.index', [
            'decisionSession'        => $decisionSession,
            'criteria'               => $criteria,
            'criterias'              => $criteria,
            'alternatives'           => $alternatives,
            'evaluations'            => $evaluations,
            'groupResult'            => $groupResult,
            'smartScores'            => $smartScores,
            'hasSmartResult'         => $hasSmartResult,
            'dmHasCompleted'         => !is_null($criteriaWeights),
            'hasCompletedEvaluation' => $hasCompletedEvaluation,
            'hasEvaluations'         => $hasCompletedEvaluation,
            'criteriaWeights'        => $criteriaWeights,
            'resultContribution'     => $resultContribution,
            'tab'                    => $currentTab,
            'smartContext'           => ['dm_name' => $user->name],
        ]);
    }
}
