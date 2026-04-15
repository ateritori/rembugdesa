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
use App\Services\Evaluation\SmartCalculationService;
use App\Services\Evaluation\SawCalculationService;
use App\Services\Evaluation\SmartAggregationPerDMService;
use App\Services\Evaluation\SawAggregationPerDMService;

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
     * Store evaluation data
     */
    public function store(
        Request $request,
        DecisionSession $decisionSession,
        EvaluationSubmissionService $service,
        SmartCalculationService $smartService,
        SawCalculationService $sawService,
        SmartAggregationPerDMService $smartAggService,
        SawAggregationPerDMService $sawAggService
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
            DB::transaction(function () use ($service, $smartService, $sawService, $smartAggService, $sawAggService, $decisionSession, $user, $evaluations) {
                $service->authorize(
                    $decisionSession,
                    $user,
                    $evaluations
                );

                $service->submit(
                    $decisionSession,
                    $user,
                    $evaluations
                );

                // Trigger calculation
                $smartService->calculate($decisionSession, $user->id);
                $sawService->calculate($decisionSession, $user->id);

                // Trigger aggregation
                $smartAggService->calculate($decisionSession);
                $sawAggService->calculate($decisionSession);
            });

            return back()->with('success', 'Evaluasi berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal simpan evaluasi', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menyimpan evaluasi.');
        }
    }
}
