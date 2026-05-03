<?php

namespace App\Http\Controllers\Dm;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Services\Evaluation\EvaluationWorkspaceService;
use App\Services\Evaluation\EvaluationSubmissionService;

class AlternativeEvaluationController extends Controller
{
    /**
     * Display evaluation workspace
     */
    public function index(
        Request $request,
        DecisionSession $decisionSession,
        EvaluationWorkspaceService $workspaceService
    ) {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        $data = $workspaceService->getWorkspace($decisionSession, $user);

        return view('dms.evaluation', $data);
    }

    /**
     * Store evaluation data (INPUT ONLY)
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        EvaluationSubmissionService $service
    ) {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        $request->validate([
            'evaluations' => 'required|array|min:1',
        ]);

        $evaluations = $request->input('evaluations');

        // Get allowed criteria for this user
        $allowedCriteriaIds = $decisionSession->assignments()
            ->where('user_id', $user->id)
            ->where('can_evaluate', true)
            ->pluck('criteria_id')
            ->toArray();

        // Filter evaluations to only allowed criteria
        $evaluations = collect($evaluations)
            ->only($allowedCriteriaIds)
            ->toArray();

        try {
            DB::transaction(function () use ($service, $decisionSession, $user, $evaluations) {

                // authorize input
                $service->authorize(
                    $decisionSession,
                    $user,
                    $evaluations
                );

                // store HUMAN evaluation only
                $service->submit(
                    $decisionSession,
                    $user,
                    $evaluations
                );
            });

            return redirect()->route('dms.index', [
                'decisionSession' => $decisionSession->id,
                'tab' => 'evaluasi-alternatif'
            ])->with('success', 'Evaluasi berhasil disimpan.');
        } catch (\Exception $e) {

            Log::error('Gagal simpan evaluasi', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menyimpan evaluasi.');
        }
    }
}
