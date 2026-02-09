<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AHP\AhpIndividualSubmissionService;

class AhpPairwiseController extends Controller
{
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
