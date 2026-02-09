<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        $criteria = $decisionSession->criteria()
            ->orderBy('order')
            ->get();

        $scoringRules = CriteriaScoringRule::with('parameters')
            ->whereIn('criteria_id', $criteria->pluck('id'))
            ->where('decision_session_id', $decisionSession->id)
            ->get()
            ->keyBy('criteria_id');

        return view('criteria.index', compact(
            'decisionSession',
            'criteria',
            'scoringRules'
        ));
    }

    public function store(Request $request, DecisionSession $decisionSession)
    {
        if ($response = $this->authorizeDraft($decisionSession)) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:benefit,cost',
        ]);

        $decisionSession->criteria()->create([
            'name' => $request->name,
            'type' => $request->type,
            'order' => $decisionSession->criteria()->count() + 1,
        ]);

        return redirect()
            ->route('criteria.index', $decisionSession->id)
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, Criteria $criteria)
    {
        if ($response = $this->authorizeDraft($criteria->decisionSession)) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:benefit,cost',
        ]);

        $criteria->update($request->only('name', 'type'));

        $decisionSession = $criteria->decisionSession;

        return redirect()
            ->route('criteria.index', $decisionSession->id)
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function toggle(Criteria $criteria)
    {
        if ($response = $this->authorizeDraft($criteria->decisionSession)) {
            return $response;
        }

        $criteria->update([
            'is_active' => ! $criteria->is_active,
        ]);

        $decisionSession = $criteria->decisionSession;

        return redirect()
            ->route('criteria.index', $decisionSession->id)
            ->with('success', 'Status kriteria diperbarui.');
    }

    public function destroy(Criteria $criteria)
    {
        if ($response = $this->authorizeDraft($criteria->decisionSession)) {
            return $response;
        }

        $criteria->delete();

        $decisionSession = $criteria->decisionSession;

        return redirect()
            ->route('criteria.index', $decisionSession->id)
            ->with('success', 'Kriteria berhasil dihapus.');
    }

    /* ================= INTERNAL ================= */

    private function authorizeDraft(DecisionSession $decisionSession)
    {
        if ($decisionSession->status !== 'draft') {
            return redirect()
                ->route('criteria.index', $decisionSession->id)
                ->with('error', 'Sesi sudah aktif. Perubahan kriteria tidak diperbolehkan.');
        }

        return null;
    }
}
