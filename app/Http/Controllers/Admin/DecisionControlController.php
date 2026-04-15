<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Models\DecisionResult;
use Illuminate\Http\Request;
use App\Services\AHP\AhpGroupSubmissionService;
use App\Services\SMART\SystemRankingService;
use App\Services\Result\DecisionResultService;
use App\Services\SAW\SawRankingService;
use App\Services\BORDA\BordaRankingService;
use App\Services\Validation\ValidationService;
use App\Models\User;

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
        ValidationService $validationService
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
            $assignedCriteriaIds = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->pluck('criteria_id')
                ->unique();

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

                $actual = $dm->alternativeEvaluations()
                    ->where('decision_session_id', $decisionSession->id)
                    ->whereIn('criteria_id', $assignedCriteriaIds)
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

        $totalEvaluateAssignments = $assignments
            ->where('can_evaluate', true)
            ->count();

        $totalExpectedActions = $totalEvaluateAssignments;

        $totalActualActions = 0;

        foreach ($dms as $dm) {
            $assignedCriteriaIds = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->pluck('criteria_id')
                ->unique();

            if ($assignedCriteriaIds->isEmpty()) continue;

            $actual = $dm->alternativeEvaluations()
                ->where('decision_session_id', $decisionSession->id)
                ->whereIn('criteria_id', $assignedCriteriaIds)
                ->distinct('criteria_id')
                ->count('criteria_id');

            $totalActualActions += $actual;
        }

        $totalExpectation = $decisionSession->alternatives->count() * $decisionSession->criteria->count();

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

            $assignedCriteriaIds = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->pluck('criteria_id')
                ->unique();

            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) {
                return false;
            }

            $expected = $assignmentCount;

            $actual = $dm->alternativeEvaluations()
                ->where('decision_session_id', $decisionSession->id)
                ->whereIn('criteria_id', $assignedCriteriaIds)
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

        $dmEvaluationsDone = $dms->filter(function ($dm) use ($decisionSession, $assignments) {

            $assignedCriteriaIds = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->pluck('criteria_id')
                ->unique();

            $assignmentCount = $assignments
                ->where('user_id', $dm->id)
                ->where('can_evaluate', true)
                ->count();

            if ($assignmentCount === 0) {
                return false;
            }

            $expected = $assignmentCount;

            $actual = $dm->alternativeEvaluations()
                ->where('decision_session_id', $decisionSession->id)
                ->whereIn('criteria_id', $assignedCriteriaIds)
                ->distinct('criteria_id')
                ->count('criteria_id');

            return $actual >= $expected;
        })->count();

        $tab = $request->query('tab');
        $borda = collect();
        $sawBorda = collect();
        $smartByDm = collect();
        $rho = null; // inisialisasi korelasi
        // Derivative metrics for view (avoid Blade logic)
        $rhoPercentage = null;
        $rhoInterpretation = null;

        $resultService = app(DecisionResultService::class);

        // Initialize for view data
        $smartResults = null;
        $sawResults = null;
        $comparisonMatrix = null;
        $summary = null;

        if ($decisionSession->status === 'closed') {
            if (in_array($tab, ['hasil-akhir', 'analisis'])) {
                $borda = $resultService->borda($decisionSession);
            }

            if ($tab === 'analisis') {
                $sawService = app(SawRankingService::class);
                $sawBorda = $resultService->sawBordaBenchmark($decisionSession, $sawService);
                $smartByDm = $resultService->smartByDm($decisionSession);

                // Mapping ranking SMART & SAW (by alternative_id)
                $rankSmart = $borda->pluck('final_rank', 'alternative_id')->toArray();
                $rankSaw   = $sawBorda->pluck('final_rank', 'alternative_id')->toArray();

                // Align both rankings using common alternative IDs
                $commonIds = array_intersect(
                    array_keys($rankSmart),
                    array_keys($rankSaw)
                );

                $alignedSmart = [];
                $alignedSaw   = [];

                foreach ($commonIds as $id) {
                    $alignedSmart[] = $rankSmart[$id];
                    $alignedSaw[]   = $rankSaw[$id];
                }

                // Calculate Spearman only if data valid & consistent
                if (!empty($alignedSmart) && count($alignedSmart) === count($alignedSaw)) {
                    $rho = $validationService->calculateSpearmanRho($alignedSmart, $alignedSaw);
                } else {
                    $rho = null;
                }

                // Derivative metrics for view (avoid Blade logic)
                $rhoPercentage = !is_null($rho) ? $rho * 100 : null;

                $rhoInterpretation = match (true) {
                    is_null($rho)        => 'Tidak valid',
                    $rho >= 0.8          => 'Sangat Kuat',
                    $rho >= 0.6          => 'Kuat',
                    $rho >= 0.4          => 'Cukup',
                    $rho >= 0.2          => 'Lemah',
                    default              => 'Sangat Lemah',
                };

                // === TRANSFORM DATA FOR VIEW (Single Source of Truth) ===

                // SMART Results (ready for display)
                $smartResults = $borda->map(function ($row) {
                    return [
                        'alternative_id' => $row->alternative_id,
                        'name' => $row->alternative->name ?? '-',
                        'rank' => $row->final_rank,
                        'score' => $row->final_score ?? null,
                    ];
                })->values();

                // SAW Results (ready for display)
                $sawResults = $sawBorda->map(function ($row) {
                    return [
                        'alternative_id' => $row->alternative_id,
                        'name' => $row->alternative->name ?? '-',
                        'rank' => $row->final_rank,
                        'score' => $row->final_score ?? null,
                    ];
                })->values();

                // Comparison Matrix
                $comparisonMatrix = [];

                $smartMap = $borda->keyBy('alternative_id');
                $sawMap   = $sawBorda->keyBy('alternative_id');

                $allIds = collect($smartMap->keys())
                    ->merge($sawMap->keys())
                    ->unique();

                foreach ($allIds as $id) {
                    $smart = $smartMap->get($id);
                    $saw   = $sawMap->get($id);

                    $rankSmart = $smart->final_rank ?? null;
                    $rankSaw   = $saw->final_rank ?? null;

                    $diff = (is_null($rankSmart) || is_null($rankSaw))
                        ? null
                        : abs($rankSmart - $rankSaw);

                    $status = match (true) {
                        is_null($diff) => 'INVALID',
                        $diff === 0    => 'MATCH',
                        default        => 'SHIFT',
                    };

                    $comparisonMatrix[] = [
                        'alternative_id' => $id,
                        'name' => $smart->alternative->name ?? $saw->alternative->name ?? '-',
                        'rank_smart' => $rankSmart,
                        'rank_saw'   => $rankSaw,
                        'diff' => $diff,
                        'status' => $status,
                    ];
                }

                // Summary
                $summary = [
                    'total_match' => collect($comparisonMatrix)->where('status', 'MATCH')->count(),
                    'total_shift' => collect($comparisonMatrix)->where('status', 'SHIFT')->count(),
                    'total_invalid' => collect($comparisonMatrix)->where('status', 'INVALID')->count(),
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

            // (optional legacy / compatibility)
            app(SystemRankingService::class)->calculate($sessionFresh, true);
        }

        return back()->with('success', 'Status sesi diperbarui.');
    }

    /**
     * Menutup sesi
     */
    public function close(DecisionSession $decisionSession, BordaRankingService $bordaRankingService)
    {
        abort_if($decisionSession->status !== 'scoring', 403);

        // Pastikan system ranking tersedia
        app(SystemRankingService::class)
            ->calculate($decisionSession->fresh(), false);

        $assignedDmIds = $decisionSession->assignments
            ->pluck('user_id')
            ->unique()
            ->toArray();
        $totalAssigned = count($assignedDmIds);
        $alternativeCount = $decisionSession->alternatives()->count();

        $expectedSmartCount = $totalAssigned * $alternativeCount;
        $actualSmartCount = $decisionSession->dmScores()
            ->where('method', \App\Models\DmScore::METHOD_SMART)
            ->count();

        if ($actualSmartCount < $expectedSmartCount) {
            return back()->with('error', 'Penutupan gagal. Penilaian SMART belum lengkap untuk semua alternatif dan DM.');
        }

        $decisionSession->getConnection()->transaction(function () use ($decisionSession, $bordaRankingService) {
            // Hitung Borda
            // $bordaRankingService->calculateAndPersist($decisionSession); // Flat Borda persistence REMOVED

            // Agregasi SMART (rata-rata per alternatif)
            $smartAggregate = $decisionSession->dmScores()
                ->where('method', \App\Models\DmScore::METHOD_SMART)
                ->get()
                ->groupBy('alternative_id')
                ->map(fn($rows) => $rows->avg('score'))
                ->sortDesc();

            $rank = 1;
            foreach ($smartAggregate as $altId => $score) {
                $signature = md5($decisionSession->id . '|' . $altId . '|SMART|DIRECT|SMART');

                DecisionResult::updateOrCreate(
                    ['signature' => $signature],
                    [
                        'signature' => $signature,
                        'decision_session_id' => $decisionSession->id,
                        'alternative_id' => $altId,
                        'source_method' => 'SMART',
                        'aggregation_method' => 'DIRECT',
                        'pipeline' => 'SMART',
                        'score' => round($score, 6),
                        'rank' => $rank++,
                    ]
                );
            }

            // SAW direct (berdasarkan hasil yang sudah ada)
            $sawAggregate = $decisionSession->dmScores()
                ->where('method', \App\Models\DmScore::METHOD_SAW)
                ->get()
                ->groupBy('alternative_id')
                ->map(fn($rows) => $rows->avg('score'))
                ->sortDesc();

            // Simpan hasil SAW ke decision_results
            $rank = 1;
            foreach ($sawAggregate as $altId => $score) {
                $signature = md5(
                    $decisionSession->id . '|' .
                        $altId . '|SAW|DIRECT|SAW'
                );

                DecisionResult::updateOrCreate(
                    ['signature' => $signature],
                    [
                        'signature' => $signature,
                        'decision_session_id' => $decisionSession->id,
                        'alternative_id' => $altId,
                        'source_method' => 'SAW',
                        'aggregation_method' => 'DIRECT',
                        'pipeline' => 'SAW',
                        'score' => round($score, 6),
                        'rank' => $rank++,
                    ]
                );
            }

            // Hitung Nested Borda (SMART & SAW)
            $bordaRankingService->nestedFinal($decisionSession, 'SMART');
            $bordaRankingService->nestedFinal($decisionSession, 'SAW');

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
