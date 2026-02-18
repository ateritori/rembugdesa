<?php

namespace App\Http\Controllers\Dm;

use App\Http\Controllers\Controller;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\CriteriaPairwise;
use App\Models\AlternativeEvaluation;
use App\Models\SmartResultDm;
use App\Services\SMART\SmartRankingService;
use App\Services\Result\DecisionResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * Manajemen workspace DM untuk penanganan revisi dan sinkronisasi view.
     */
    private function renderWorkspace(DecisionSession $decisionSession, ?SmartRankingService $smartRankingService = null)
    {
        abort_if($decisionSession->status === 'draft', 403, 'Sesi masih dalam tahap draft.');

        $user = Auth::user();
        $isDm = $decisionSession->dms()->where('users.id', $user->id)->exists();
        abort_if(!$isDm, 403, 'Anda tidak ditugaskan dalam sesi ini.');

        $currentTab = request('tab', 'workspace');
        $isEditing = request('edit') == 1;

        $criteria = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $alternatives = $decisionSession->alternatives()
            ->where('is_active', true)
            ->get();

        // Data bobot kelompok dan individu
        $groupResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')
            ->first();

        $individualWeight = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->first();

        // Load data mentah perbandingan (Pairwise) untuk slider mode edit
        $existingPairwise = [];
        if ($individualWeight || $isEditing) {
            $rawPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', $user->id)
                ->get();

            foreach ($rawPairwise as $p) {
                $key = min($p->criteria_a_id, $p->criteria_b_id) . '-' . max($p->criteria_a_id, $p->criteria_b_id);

                $existingPairwise[$key] = (object)[
                    'direction'  => $p->value_a_ij >= 1 ? 'left' : 'right',
                    'value'      => $p->value_a_ij >= 1 ? $p->value_a_ij : $p->value_b_ij,
                    'val_a_ij'   => $p->value_a_ij,
                    'val_a_ji'   => $p->value_a_ji,
                ];
            }
        }

        // Penentuan jenis bobot yang ditampilkan di view
        if ($isEditing) {
            $criteriaWeights = $individualWeight;
        } else {
            $criteriaWeights = ($decisionSession->status === 'configured')
                ? $individualWeight
                : ($groupResult ?? $individualWeight);
        }

        // Data evaluasi alternatif
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->groupBy('alternative_id')
            ->map(fn($items) => $items->keyBy('criteria_id'));

        $hasCompletedEvaluation = $evaluations->isNotEmpty();

        // Kalkulasi skor SMART
        $smartScores = collect();
        $hasSmartResult = false;

        if ($smartRankingService && ($groupResult || $individualWeight) && $hasCompletedEvaluation) {
            try {
                $scores = $smartRankingService->calculate($decisionSession, $user, false);
                $smartScores = collect($scores)->sortByDesc('score');
                $hasSmartResult = $smartScores->isNotEmpty();
            } catch (Exception $e) {
                Log::error("SMART Calculation Error: " . $e->getMessage());
            }
        }

        // Analisis kontribusi DM pada hasil akhir
        $resultContribution = null;
        if ($currentTab === 'hasil-akhir' && $decisionSession->status === 'closed') {
            $resultContribution = app(DecisionResultService::class)->dmContribution($decisionSession, $user);
        }

        return view('dms.index', [
            'decisionSession'        => $decisionSession,
            'criteria'               => $criteria,
            'criterias'              => $criteria,
            'alternatives'           => $alternatives,
            'evaluations'            => $evaluations,
            'criteriaWeights'        => $criteriaWeights,
            'existingPairwise'       => $existingPairwise,
            'groupResult'            => $groupResult,
            'smartScores'            => $smartScores,
            'hasSmartResult'         => $hasSmartResult,
            'resultContribution'     => $resultContribution,
            'dmHasCompleted'         => !is_null($individualWeight),
            'hasCompletedEvaluation' => $hasCompletedEvaluation,
            'tab'                    => $currentTab,
            'isEditing'              => $isEditing,
            'smartContext'           => ['dm_name' => $user->name],
        ]);
    }
}
