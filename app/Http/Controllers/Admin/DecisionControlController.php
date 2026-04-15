<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use App\Services\AHP\AhpGroupSubmissionService;
use App\Services\SMART\SystemRankingService;
use App\Services\Borda\BordaPipelineService;
use App\Services\Validation\ValidationService;
use App\Services\Analysis\FinalRankingAnalysisService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DecisionControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Panel Kontrol Sesi (Dashboard Admin)
     */
    public function index(
        Request $request,
        DecisionSession $decisionSession,
        ValidationService $validationService,
        FinalRankingAnalysisService $analysisService
    ) {
        $decisionSession->load(['alternatives', 'criteria', 'assignments']);

        $assignments = $decisionSession->assignments;

        $assignedDmIds = $assignments->pluck('user_id')->unique();
        $dms = User::whereIn('id', $assignedDmIds)->get();

        $dmProgress = $dms->map(function ($dm) use ($decisionSession, $assignments) {
            // Pairwise based on assignment
            $hasPairwiseAssignment = $assignments
                ->where('user_id', $dm->id)
                ->where('can_pairwise', true)
                ->isNotEmpty();

            $hasEvaluateAssignment = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->isNotEmpty();

            $pairwiseDone = $hasPairwiseAssignment
                ? $dm->criteriaWeights()
                ->where('decision_session_id', $decisionSession->id)
                ->exists()
                : false;

            // Alternative based on assignment
            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) {
                $alternativeDone = false;
                $expected = 0;
                $actual = 0;
            } else {
                $expected = $assignmentCount;

                $actual = DB::table('evaluation_scores')
                    ->where('decision_session_id', $decisionSession->id)
                    ->where('user_id', $dm->id)
                    ->whereNotNull('user_id')
                    ->where('source', '!=', 'system')
                    ->distinct('criteria_id')
                    ->count('criteria_id');

                $alternativeDone = $actual >= $expected;
            }

            return [
                'id' => $dm->id,
                'name' => $dm->name,
                'has_pairwise' => $hasPairwiseAssignment,
                'has_evaluate' => $hasEvaluateAssignment,
                'pairwise' => $pairwiseDone,
                'alternative' => $alternativeDone,
                'expected' => $expected,
                'actual' => $actual,
            ];
        });

        $assignedDmCount = $assignedDmIds->count();
        $selectedActionsCount = $assignments->count();

        // $totalEvaluateAssignments = $assignments
        //     ->where('can_evaluate', true)
        //     ->count();

        $totalExpectedActions = 0;
        foreach ($dms as $dm) {
            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            $totalExpectedActions += $assignmentCount;
        }

        $totalActualActions = 0;

        foreach ($dms as $dm) {
            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) continue;

            $actual = DB::table('evaluation_scores')
                ->where('decision_session_id', $decisionSession->id)
                ->where('user_id', $dm->id)
                ->whereNotNull('user_id')
                ->where('source', '!=', 'system')
                ->distinct('criteria_id')
                ->count('criteria_id');

            $totalActualActions += $actual;
        }



        $activeCriteriaCount = $decisionSession->criteria->where('is_active', true)->count();
        $activeAlternativesCount = $decisionSession->alternatives->where('is_active', true)->count();

        $dmPairwiseDone = $dms->filter(function ($dm) use ($decisionSession, $assignments) {

            $hasPairwiseAssignment = $assignments
                ->where('user_id', $dm->id)
                ->where('can_pairwise', true)
                ->isNotEmpty();

            if (!$hasPairwiseAssignment) {
                return false;
            }

            return $dm->criteriaWeights()
                ->where('decision_session_id', $decisionSession->id)
                ->exists();
        })->count();

        $dmAltDone = $dms->filter(function ($dm) use ($decisionSession, $assignments) {
            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) {
                return false;
            }

            $expected = $assignmentCount;

            $actual = DB::table('evaluation_scores')
                ->where('decision_session_id', $decisionSession->id)
                ->where('user_id', $dm->id)
                ->whereNotNull('user_id')
                ->where('source', '!=', 'system')
                ->distinct('criteria_id')
                ->count('criteria_id');

            return $actual >= $expected;
        })->count();

        $pairwiseEligible = $assignments
            ->where('can_pairwise', true)
            ->pluck('user_id')
            ->unique()
            ->count();

        $altEligible = $assignments
            ->where('can_evaluate', true)
            ->pluck('user_id')
            ->unique()
            ->count();

        $canActivate = $activeCriteriaCount >= 2
            && $activeAlternativesCount >= 2
            && $assignedDmCount >= 1;

        $dmEvaluationsDone = $dmAltDone;

        $tab = $request->query('tab', 'hasil-akhir');
        $borda = collect();
        $sawBorda = collect();
        $smartByDm = collect();
        $rho = null; // inisialisasi korelasi
        // Derivative metrics for view (avoid Blade logic)
        $rhoPercentage = null;
        $rhoInterpretation = null;


        // Initialize for view data
        $smartResults = null;
        $sawResults = null;
        $comparisonMatrix = null;
        $summary = null;

        if ($decisionSession->status === 'closed') {
            // Always load SMART final results when session is closed
            $borda = \App\Models\BordaAggregation::where('decision_session_id', $decisionSession->id)
                ->where('level', 'final')
                ->where('method', 'SMART')
                ->orderBy('rank')
                ->get();

            if ($tab === 'analisis') {
                $data = $analysisService->build($decisionSession);

                // Use service as single source of truth
                $smartResults = collect($data['smart']);
                $sawResults   = collect($data['saw']);
                $comparisonMatrix = collect($data['comparison']);

                // Spearman (from service)
                $rhoPercentage = $data['rhoPercentage'] ?? null;
                $rhoInterpretation = $data['rhoInterpretation'] ?? null;

                // Summary
                $summary = [
                    'total_match' => $comparisonMatrix->where('status', 'MATCH')->count(),
                    'total_shift' => $comparisonMatrix->where('status', 'SHIFT')->count(),
                    'total_invalid' => $comparisonMatrix->where('status', 'INVALID')->count(),
                ];
            }
        }
        // Ensure sorted results before sending to view
        $borda = $borda->sortBy('final_rank')->values();
        $sawBorda = $sawBorda->sortBy('final_rank')->values();

        // Fallback init if not set (tab !== analisis)
        $smartResults = $smartResults ?? collect();
        $sawResults = $sawResults ?? collect();
        $comparisonMatrix = $comparisonMatrix ?? [];
        $summary = $summary ?? [
            'total_match' => 0,
            'total_shift' => 0,
            'total_invalid' => 0,
        ];
        $rhoPercentage = $rhoPercentage ?? null;
        $rhoInterpretation = $rhoInterpretation ?? null;

        return view('control.index', compact(
            'decisionSession',
            'assignedDmCount',
            'selectedActionsCount',
            'activeCriteriaCount',
            'activeAlternativesCount',
            'dmPairwiseDone',
            'dmAltDone',
            'canActivate',
            'dmEvaluationsDone',
            'dmProgress',
            'rhoPercentage',
            'rhoInterpretation',
            'smartResults',
            'sawResults',
            'comparisonMatrix',
            'summary',
            'pairwiseEligible',
            'altEligible',
            'totalExpectedActions',
            'totalActualActions',
        ));
    }

    /**
     * Aktivasi Sesi
     */
    public function activate(DecisionSession $decisionSession, AhpGroupSubmissionService $groupService)
    {
        $validStatuses = ['draft', 'configured'];
        abort_unless(in_array($decisionSession->status, $validStatuses), 403);

        try {
            $groupService->calculateAndStore($decisionSession);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $nextStatus = ($decisionSession->status === 'draft') ? 'configured' : 'scoring';
        $decisionSession->update(['status' => $nextStatus]);

        // Trigger perhitungan system saat masuk fase scoring
        if ($nextStatus === 'scoring') {

            $sessionFresh = $decisionSession->fresh();

            // STEP 1: generate raw system evaluation (RAB, coverage, beneficiaries)
            app(\App\Services\Evaluation\SystemCriteriaEvaluationService::class)
                ->generate($sessionFresh);

            // STEP 2: hitung SAW (system)
            app(\App\Services\Evaluation\SystemSawService::class)
                ->calculate($sessionFresh);

            // STEP 3: hitung SMART (system)
            app(\App\Services\Evaluation\SystemSmartService::class)
                ->calculate($sessionFresh);
        }

        return back()->with('success', 'Status sesi diperbarui.');
    }

    /**
     * Menutup sesi
     */
    public function close(DecisionSession $decisionSession, BordaPipelineService $bordaPipelineService)
    {
        abort_if($decisionSession->status !== 'scoring', 403);

        // Pastikan system ranking tersedia
        app(SystemRankingService::class)
            ->calculate($decisionSession->fresh(), false);

        $assignedDmIds = $decisionSession->assignments
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $expectedSmartCount = \App\Models\EvaluationAggregation::where('decision_session_id', $decisionSession->id)
            ->whereNotNull('user_id')
            ->where('method', 'SMART')
            ->distinct(DB::raw('CONCAT(user_id, "-", alternative_id)'))
            ->count(DB::raw('CONCAT(user_id, "-", alternative_id)'));

        $actualSmartCount = \App\Models\EvaluationAggregation::where('decision_session_id', $decisionSession->id)
            ->whereNotNull('user_id')
            ->where('method', 'SMART')
            ->count();

        if ($actualSmartCount < $expectedSmartCount) {
            return back()->with('error', 'Penutupan gagal. Data SMART belum lengkap (ada DM atau alternatif yang belum memiliki skor).');
        }

        $decisionSession->getConnection()->transaction(function () use ($decisionSession, $bordaPipelineService) {
            // Hitung Nested Borda (SMART & SAW) via pipeline
            $bordaPipelineService->run($decisionSession, 'SMART');
            $bordaPipelineService->run($decisionSession, 'SAW');

            // Update status sesi
            $decisionSession->update(['status' => 'closed']);
        });

        return redirect()
            ->route('control.index', [
                'decisionSession' => $decisionSession->id,
                'tab' => 'analisis'
            ])
            ->with('success', 'Sesi ditutup.');
    }
}
