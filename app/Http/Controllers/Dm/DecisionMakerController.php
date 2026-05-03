<?php

namespace App\Http\Controllers\Dm;

use App\Http\Controllers\Controller;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\CriteriaPairwise;
use App\Models\AlternativeEvaluation;
use App\Models\SmartResultDm;
use App\Models\CriteriaGroupWeight;
use App\Models\EvaluationScore;
use App\Services\SMART\SmartRankingService;
use App\Services\Analysis\SmartTraceService;
use App\Services\Result\DecisionResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class DecisionMakerController extends Controller
{
    public function index(DecisionSession $decisionSession, SmartRankingService $smartRankingService)
    {
        return $this->renderWorkspace($decisionSession, $smartRankingService, app(SmartTraceService::class));
    }

    public function weights(DecisionSession $decisionSession)
    {
        return $this->renderWorkspace($decisionSession, null, app(SmartTraceService::class));
    }

    public function groupWeights(DecisionSession $decisionSession)
    {
        return $this->renderWorkspace($decisionSession, null, app(SmartTraceService::class));
    }

    /**
     * Manajemen workspace DM untuk penanganan revisi dan sinkronisasi view.
     */
    private function renderWorkspace(DecisionSession $decisionSession, ?SmartRankingService $smartRankingService = null, ?SmartTraceService $smartTraceService = null)
    {
        abort_if($decisionSession->status === 'draft', 403, 'Sesi masih dalam tahap draft.');

        $user = Auth::user();

        $assignments = $decisionSession->assignments()
            ->where('user_id', $user->id)
            ->get();

        $hasAssignment = $assignments->isNotEmpty();
        abort_if(!$hasAssignment, 403, 'Anda tidak ditugaskan dalam sesi ini.');

        // ROLE (berdasarkan assignment)
        $hasPairwiseAccess = $assignments->where('can_pairwise', true)->isNotEmpty();
        $hasEvaluateAccess = $assignments->where('can_evaluate', true)->isNotEmpty();

        $assignedCriteriaIds = $assignments
            ->where('can_evaluate', true)
            ->pluck('criteria_id')
            ->filter()
            ->unique();

        // PHASE (berdasarkan status sesi)
        $isPairwisePhase = $decisionSession->status === 'configured';
        $isEvaluationPhase = $decisionSession->status === 'scoring';

        // FINAL ACCESS (ROLE + PHASE)
        $canAccessPairwise = $hasPairwiseAccess && $isPairwisePhase;
        $canAccessEvaluate = $hasEvaluateAccess && $isEvaluationPhase;

        $currentTab = request('tab', 'workspace');
        $isEditing = request('edit') == 1;

        $criteriaQuery = $decisionSession->criteria()
            ->where('is_active', true);

        if ($canAccessPairwise) {
            // Pairwise hanya pakai kriteria level 1
            $criteriaQuery->where('level', 1);
        }

        if ($canAccessEvaluate && $assignedCriteriaIds->isNotEmpty()) {
            // Evaluasi hanya kriteria yang di-assign ke DM
            $criteriaQuery->whereIn('id', $assignedCriteriaIds);
        }

        $criteria = $criteriaQuery
            ->orderBy('order')
            ->get();

        $alternatives = $decisionSession->alternatives()
            ->where('is_active', true)
            ->get();

        // Data bobot kelompok dan individu
        $groupWeightRow = CriteriaGroupWeight::where('decision_session_id', $decisionSession->id)
            ->first();

        $groupResult = null;

        if ($groupWeightRow && $groupWeightRow->weights) {
            $weightsData = $groupWeightRow->weights;

            // Handle jika sudah di-cast jadi array oleh model
            if (is_array($weightsData)) {
                $decoded = $weightsData;
            } else {
                $decoded = json_decode($weightsData, true);
            }

            $groupResult = (object)[
                'weights' => $decoded,
                'source'  => 'group_pairwise_json'
            ];
        }

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

        // Data evaluasi alternatif (pakai evaluation_scores - Eloquent)
        $evaluations = EvaluationScore::where('decision_session_id', $decisionSession->id)
            ->when($assignedCriteriaIds->isNotEmpty(), function ($q) use ($assignedCriteriaIds) {
                $q->whereIn('criteria_id', $assignedCriteriaIds);
            })
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

        $smartTrace = collect();

        $debugTraceRaw = null;

        if ($smartTraceService && $hasCompletedEvaluation) {
            try {
                $debugTraceRaw = $smartTraceService->buildUserFullTrace($decisionSession, $user->id);

                $smartTrace = collect($debugTraceRaw);
            } catch (Exception $e) {
                Log::error("SMART Trace Error: " . $e->getMessage());
            }
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
            'dmHasCompleted'         => !is_null($individualWeight),
            'hasCompletedEvaluation' => $hasCompletedEvaluation,
            'tab'                    => $currentTab,
            'isEditing'              => $isEditing,
            'smartContext'           => ['dm_name' => $user->name],
            'hasPairwiseAccess'      => $hasPairwiseAccess,
            'hasEvaluateAccess'      => $hasEvaluateAccess,
            'isPairwisePhase'        => $isPairwisePhase,
            'isEvaluationPhase'      => $isEvaluationPhase,
            'canAccessPairwise'      => $canAccessPairwise,
            'canAccessEvaluate'      => $canAccessEvaluate,
            'smartTrace'             => $smartTrace,
        ]);
    }
}
