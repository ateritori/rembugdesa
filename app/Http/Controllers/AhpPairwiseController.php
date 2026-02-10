<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AHP\AhpIndividualSubmissionService;
use App\Models\CriteriaWeight;

class AhpPairwiseController extends Controller
{
    /**
     * Show pairwise comparison workspace.
     */
    public function index(DecisionSession $decisionSession)
    {
        $user = Auth::user();

        abort_if(! $user || ! $user->hasRole('dm'), 403);
        abort_if(
            ! in_array($decisionSession->status, ['active', 'criteria', 'alternatives', 'closed'], true),
            403
        );

        $existingResult = CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->first();

        return view('dms.weights.index', [
            'decisionSession' => $decisionSession,
            'existingResult'  => $existingResult,
        ]);
    }

    /**
     * Store pairwise comparison submitted by Decision Maker.
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        AhpIndividualSubmissionService $service
    ) {
        $user = Auth::user();

        abort_if(! $user || ! $user->hasRole('dm'), 403);
        abort_if($decisionSession->status !== 'active', 403);

        $frontendPairs = json_decode($request->input('debug_frontend'), true);

        if (! is_array($frontendPairs)) {
            return back()->withErrors('Data frontend tidak valid.');
        }

        try {
            $result = $service->submit(
                $decisionSession,
                $user,
                $frontendPairs,
                $request->input('pairwise', [])
            );
        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }

        return redirect()
            ->route('dms.index', $decisionSession->id)
            ->with('tab', 'weights')
            ->with(
                'success',
                'Penilaian berhasil disimpan. CR = ' . round($result['cr'], 4)
            );
    }
}
