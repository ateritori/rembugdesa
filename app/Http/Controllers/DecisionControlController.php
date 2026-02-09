<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;

class DecisionControlController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        $activeCriteriaCount = $decisionSession->criteria()
            ->where('is_active', true)
            ->count();

        $activeAlternativesCount = $decisionSession->alternatives()
            ->where('is_active', true)
            ->count();

        $assignedDmCount = $decisionSession->dms()->count();

        $assignedDms = $decisionSession->dms()->get();

        return view('control.index', compact(
            'decisionSession',
            'activeCriteriaCount',
            'activeAlternativesCount',
            'assignedDmCount',
            'assignedDms'
        ));
    }
}
