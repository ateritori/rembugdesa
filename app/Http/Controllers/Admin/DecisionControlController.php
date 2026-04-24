<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use App\Services\AHP\AhpGroupSubmissionService;
use App\Services\Borda\NestedBordaService;
use App\Services\State\SessionStateDashboardService;
use App\Services\State\SessionStateTransitionService;
use App\Services\Analysis\SmartTraceService;
use App\Services\Analysis\SawTraceService;

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
        SessionStateDashboardService $service
    ) {
        $tab = $request->query('tab', 'hasil-akhir');

        $data = $service->build($decisionSession, $tab);

        // 🔥 MULTI-DM TRACE (ikuti ProvenanceController)

        $smartTraceService = app(SmartTraceService::class);
        $sawTraceService   = app(SawTraceService::class);

        // Ambil semua DM
        $userIds = \App\Models\EvaluationScore::where('decision_session_id', $decisionSession->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->values()
            ->toArray();

        // SMART traces per DM + system
        $smartTraces = [];
        foreach ($userIds as $userId) {
            $smartTraces[$userId] = $smartTraceService->buildUserFullTrace($decisionSession, $userId);
        }
        $smartTraces['system'] = $smartTraceService->build($decisionSession, null, []);

        // SAW traces per DM + system
        $sawTraces = [];
        foreach ($userIds as $userId) {
            $sawTraces[$userId] = $sawTraceService->buildUserFullTrace($decisionSession, $userId);
        }
        $sawTraces['system'] = $sawTraceService->build($decisionSession, null, []);

        // Nested Borda
        $bordaService = app(NestedBordaService::class);

        $smartBorda = $bordaService->calculateFromTraces($smartTraces);
        $sawBorda   = $bordaService->calculateFromTraces($sawTraces);

        // 🔥 Mapping alternatif (id + name)
        $alternatives = $decisionSession->alternatives()
            ->pluck('name', 'id')
            ->toArray();

        foreach ($smartBorda['ranking'] as $altId => &$row) {
            $row['alternative_id'] = $altId;
            $row['name'] = $alternatives[$altId] ?? null;
        }
        unset($row); // Avoid reference issues

        foreach ($sawBorda['ranking'] as $altId => &$row) {
            $row['alternative_id'] = $altId;
            $row['name'] = $alternatives[$altId] ?? null;
        }
        unset($row); // Avoid reference issues

        // 🔥 Build comparison matrix (SYNC dengan Nested Borda)
        $comparisonMatrix = [];

        foreach ($smartBorda['ranking'] as $altId => $smart) {
            $rankSmart = $smart['rank'];
            $rankSaw   = $sawBorda['ranking'][$altId]['rank'] ?? null;

            $comparisonMatrix[] = [
                'alternative_id' => $altId,
                'name' => $smart['name'] ?? null,
                'rank_smart' => $rankSmart,
                'rank_saw'   => $rankSaw,
                'diff' => $rankSaw !== null ? $rankSmart - $rankSaw : null,
            ];
        }

        // 🔥 Spearman Rank Correlation (pakai RANK.AVG)

        // Ambil skor Borda (bukan rank)
        $smartScores = [];
        $sawScores   = [];

        foreach ($smartBorda['ranking'] as $altId => $row) {
            $smartScores[$altId] = $row['score'];
        }

        foreach ($sawBorda['ranking'] as $altId => $row) {
            $sawScores[$altId] = $row['score'];
        }

        // Function rank average
        $computeRankAvg = function ($scores) {
            arsort($scores);
            $ranks = [];
            $i = 1;

            while (!empty($scores)) {
                $value = current($scores);

                $ties = array_keys($scores, $value, true);
                $count = count($ties);

                $avgRank = ($i + ($i + $count - 1)) / 2;

                foreach ($ties as $key) {
                    $ranks[$key] = $avgRank;
                    unset($scores[$key]);
                }

                $i += $count;
            }

            return $ranks;
        };

        $rankSmartAvg = $computeRankAvg($smartScores);
        $rankSawAvg   = $computeRankAvg($sawScores);

        // Hitung Spearman
        $n = count($rankSmartAvg);
        $sum_d2 = 0;

        foreach ($rankSmartAvg as $altId => $r1) {
            $r2 = $rankSawAvg[$altId] ?? null;

            if ($r2 !== null) {
                $d = $r1 - $r2;
                $sum_d2 += pow($d, 2);
            }
        }

        $spearman = $n > 1
            ? 1 - ((6 * $sum_d2) / ($n * (pow($n, 2) - 1)))
            : null;

        return view(
            'control.index',
            array_merge($data, [
                'smartBorda' => $smartBorda,
                'sawBorda'   => $sawBorda,
                'comparisonMatrix' => $comparisonMatrix,
                'spearman' => $spearman,
            ])
        );
    }

    /**
     * Aktivasi Sesi
     */
    public function activate(
        DecisionSession $decisionSession,
        AhpGroupSubmissionService $groupService,
        SessionStateTransitionService $transitionService
    ) {
        try {
            $transitionService->activate($decisionSession, $groupService);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status sesi diperbarui.');
    }

    /**
     * Menutup sesi
     */
    public function close(
        DecisionSession $decisionSession,
        SessionStateTransitionService $transitionService
    ) {
        try {
            $transitionService->close($decisionSession, null);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('control.index', [
                'decisionSession' => $decisionSession->id,
                'tab' => 'analisis'
            ])
            ->with('success', 'Sesi ditutup.');
    }
}
