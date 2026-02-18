<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Services\Result\DecisionResultService;

class DecisionSessionResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['showPublic']);
    }

    /**
     * Tampilan hasil akhir (admin)
     */
    public function index(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'closed', 403);

        $resultService = app(DecisionResultService::class);
        $results = $resultService->borda($decisionSession);

        return view('decision-sessions.result', compact(
            'decisionSession',
            'results'
        ));
    }

    /**
     * Tampilan publik hasil akhir
     */
    public function showPublic(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'closed', 403);

        $resultService = app(DecisionResultService::class);
        $results = $resultService->borda($decisionSession);

        return view('decision-sessions.result', compact(
            'decisionSession',
            'results'
        ));
    }
}
