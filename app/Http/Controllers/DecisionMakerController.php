<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\CriteriaPairwise; // Tambahkan ini
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
     * PUSAT LOGIKA - Menjamin fitur revisi dan view sinkron.
     */
    private function renderWorkspace(DecisionSession $decisionSession, ?SmartRankingService $smartRankingService = null)
    {
        // 1. Otorisasi & Guard
        abort_if($decisionSession->status === 'draft', 403, 'Sesi masih dalam tahap draft.');

        $user = Auth::user();
        $isDm = $decisionSession->dms()->where('users.id', $user->id)->exists();
        abort_if(!$isDm, 403, 'Anda tidak ditugaskan dalam sesi ini.');

        $currentTab = request('tab', 'workspace');
        $isEditing = request('edit') == 1;

        // 2. Data Dasar
        $criteria = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $alternatives = $decisionSession->alternatives()
            ->where('is_active', true)
            ->get();

        // 3. Pengelolaan Bobot (Weights)
        $groupResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')
            ->first();

        $individualWeight = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->first();

        /**
         * PERBAIKAN KRUSIAL: Ambil data mentah perbandingan (Pairwise)
         * Ini yang akan menggerakkan slider saat mode EDIT aktif.
         */
        $existingPairwise = [];
        if ($individualWeight || $isEditing) {
            $rawPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', $user->id)
                ->get();

            foreach ($rawPairwise as $p) {
                // Key format: "id_kecil-id_besar" agar cocok dengan JS
                $key = min($p->criteria_a_id, $p->criteria_b_id) . '-' . max($p->criteria_a_id, $p->criteria_b_id);

                $existingPairwise[$key] = (object)[
                    'direction'  => $p->value_a_ij >= 1 ? 'left' : 'right',
                    'value'      => $p->value_a_ij >= 1 ? $p->value_a_ij : $p->value_b_ij,
                    'val_a_ij'   => $p->value_a_ij,
                    'val_a_ji'   => $p->value_a_ji,
                ];
            }
        }

        // Penentuan Bobot untuk View
        if ($isEditing) {
            $criteriaWeights = $individualWeight;
        } else {
            if ($decisionSession->status === 'configured') {
                $criteriaWeights = $individualWeight;
            } else {
                $criteriaWeights = $groupResult ?? $individualWeight;
            }
        }

        // 4. Evaluasi Alternatif
        $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->groupBy('alternative_id')
            ->map(fn($items) => $items->keyBy('criteria_id'));

        $hasCompletedEvaluation = $evaluations->isNotEmpty();

        // 5. Kalkulasi SMART (Hanya jika kriteria sudah ada bobotnya)
        $smartScores = collect();
        $hasSmartResult = false;

        if ($smartRankingService && ($groupResult || $individualWeight) && $hasCompletedEvaluation) {
            try {
                $scores = $smartRankingService->calculate($decisionSession, $user, false);
                $smartScores = collect($scores)->sortByDesc('score');
                $hasSmartResult = $smartScores->isNotEmpty();
            } catch (Exception $e) {
                Log::error("SMART Error: " . $e->getMessage());
            }
        }

        // 6. Kontribusi DM (Hanya Final/Closed)
        $resultContribution = null;
        if ($currentTab === 'hasil-akhir' && $decisionSession->status === 'closed') {
            $resultContribution = app(DecisionResultService::class)->dmContribution($decisionSession, $user);
        }

        // 7. Pengiriman ke View
        return view('dms.index', [
            'decisionSession'        => $decisionSession,
            'criteria'               => $criteria,
            'criterias'              => $criteria,
            'alternatives'           => $alternatives,
            'evaluations'            => $evaluations,
            'criteriaWeights'        => $criteriaWeights,
            'existingPairwise'       => $existingPairwise, // DATA BARU UNTUK SLIDER
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
