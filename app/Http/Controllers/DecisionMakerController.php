<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\CriteriaPairwise;
use App\Models\AlternativeEvaluation;
use Illuminate\Http\Request;

class DecisionMakerController extends Controller
{
    /**
     * Workspace utama Decision Maker
     * HANYA ringkasan & navigasi
     */
    public function index(DecisionSession $decisionSession)
    {
        // Guard dasar (SAMA POLA DENGAN KRITERIA)
        abort_if($decisionSession->status === 'draft', 403);

        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403,
            'Anda tidak ditugaskan pada sesi ini.'
        );

        // Data ringkasan workspace
        $baseCriteria = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->first();

        $dmHasCompleted = ! is_null($criteriaWeights);

        // ===== Evaluasi Alternatif (hanya saat tab aktif) =====
        if (request('tab') === 'evaluasi-alternatif') {
            $alternatives = $decisionSession->alternatives()
                ->where('is_active', true)
                ->get();

            $criteria = $decisionSession->criteria()
                ->with(['scoringRule', 'scoringRule.parameters'])
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            $evaluations = AlternativeEvaluation::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', auth()->id())
                ->get()
                ->groupBy('alternative_id')
                ->map(fn($items) => $items->keyBy('criteria_id'));

            $hasCompletedEvaluation = $evaluations->isNotEmpty();
        } else {
            $alternatives = collect();
            $evaluations = collect();
            $hasCompletedEvaluation = false;
        }

        return view('dms.index', [
            'decisionSession' => $decisionSession,
            'criteria'        => (request('tab') === 'evaluasi-alternatif') ? $criteria : $baseCriteria,
            'criterias'       => (request('tab') === 'evaluasi-alternatif') ? $criteria : $baseCriteria, // jaga kompatibilitas view lama
            'criteriaWeights' => $criteriaWeights,
            'dmHasCompleted'  => $dmHasCompleted,
            'evaluations'            => $evaluations,
            'hasCompletedEvaluation' => $hasCompletedEvaluation,
            'alternatives'    => $alternatives,
            'tab'             => request('tab', 'workspace'),
        ]);
    }

    /**
     * Hasil bobot individu DM
     * READ ONLY
     */
    public function weights(DecisionSession $decisionSession)
    {
        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403
        );

        $criteria = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->first();

        $dmHasCompleted = ! is_null($criteriaWeights);

        $evaluations = collect();
        $hasCompletedEvaluation = false;

        $alternatives = collect();

        return view('dms.index', [
            'decisionSession' => $decisionSession,
            'criteria'        => $criteria,
            'criterias'       => $criteria,
            'criteriaWeights' => $criteriaWeights,
            'dmHasCompleted'  => $dmHasCompleted,
            'evaluations'            => $evaluations,
            'hasCompletedEvaluation' => $hasCompletedEvaluation,
            'alternatives'    => $alternatives,
            'tab'             => request('tab', 'workspace'),
        ]);
    }

    /**
     * Hasil bobot kelompok (agregasi)
     * READ ONLY
     */
    public function groupWeights(DecisionSession $decisionSession)
    {
        abort_if(
            ! $decisionSession->dms()
                ->where('users.id', auth()->id())
                ->exists(),
            403
        );

        $groupResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')
            ->first();

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->first();

        $dmHasCompleted = ! is_null($criteriaWeights);

        $evaluations = collect();
        $hasCompletedEvaluation = false;

        $alternatives = collect();

        return view('dms.index', [
            'decisionSession' => $decisionSession,
            'groupResult'     => $groupResult,
            'dmHasCompleted'  => $dmHasCompleted,
            'evaluations'            => $evaluations,
            'hasCompletedEvaluation' => $hasCompletedEvaluation,
            'alternatives'    => $alternatives,
            'tab'             => request('tab', 'workspace'),
        ]);
    }
}
