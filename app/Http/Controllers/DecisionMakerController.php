<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\CriteriaPairwise; // Pastikan ini diimport
use Illuminate\Http\Request;

class DecisionMakerController extends Controller
{
    /**
     * Halaman Workspace Utama DM
     */
    public function index(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status === 'draft', 403);
        abort_if(! $decisionSession->dms()->where('users.id', auth()->id())->exists(), 403, 'Anda tidak ditugaskan pada sesi ini.');

        $criteria = $decisionSession->criteria()->where('is_active', true)->orderBy('order')->get();

        $existingPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->get()
            ->keyBy(fn($p) => $p->criteria_id_1 . '-' . $p->criteria_id_2);

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->first();

        // Variabel penentu apakah DM sudah menyelesaikan AHP
        $dmHasCompleted = ! is_null($criteriaWeights);

        return view('dms.index', [
            'decisionSession' => $decisionSession,
            'criteria' => $criteria,
            'criterias' => $criteria,
            'existingPairwise' => $existingPairwise,
            'criteriaWeights' => $criteriaWeights,
            'dmHasCompleted' => $dmHasCompleted,
            'activeTab' => request()->query('tab', 'workspace')
        ]);
    }

    /**
     * Menampilkan Hasil Bobot Individu (Method yang sebelumnya hilang)
     */
    public function weights(DecisionSession $decisionSession)
    {
        abort_if(! $decisionSession->dms()->where('users.id', auth()->id())->exists(), 403);

        $criteria = $decisionSession->criteria()->where('is_active', true)->orderBy('order')->get();

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->first();

        $dmHasCompleted = ! is_null($criteriaWeights);

        return view('dms.index', [
            'decisionSession' => $decisionSession,
            'criteria' => $criteria,
            'criterias' => $criteria,
            'criteriaWeights' => $criteriaWeights,
            'dmHasCompleted' => $dmHasCompleted,
            'activeTab' => 'weights'
        ]);
    }

    /**
     * Menampilkan Hasil Bobot Kelompok (Agregasi)
     */
    public function groupWeights(DecisionSession $decisionSession)
    {
        abort_if(! $decisionSession->dms()->where('users.id', auth()->id())->exists(), 403);

        $groupResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->whereNull('dm_id')
            ->first();

        $criteriaWeights = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', auth()->id())
            ->first();

        $dmHasCompleted = ! is_null($criteriaWeights);

        return view('dms.index', [
            'decisionSession' => $decisionSession,
            'groupResult' => $groupResult,
            'dmHasCompleted' => $dmHasCompleted,
            'activeTab' => 'group-weights'
        ]);
    }
}
