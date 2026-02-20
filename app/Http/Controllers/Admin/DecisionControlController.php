<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Models\SmartResultDm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Services\AHP\AhpGroupWeightService;
use App\Services\Result\DecisionResultService;
use App\Services\SAW\SawRankingService;
use App\Services\BORDA\BordaRankingService;
use App\Services\Validation\ValidationService;

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
        $decisionSession->load(['dms', 'alternatives', 'criteria']);

        $dmProgress = $decisionSession->dms->map(function ($dm) use ($decisionSession) {
            $pairwiseDone = $dm->criteriaWeights()
                ->where('decision_session_id', $decisionSession->id)
                ->exists();

            $alternativeDone = $dm->alternativeEvaluations()
                ->where('decision_session_id', $decisionSession->id)
                ->exists();

            return [
                'id' => $dm->id,
                'name' => $dm->name,
                'pairwise' => $pairwiseDone,
                'alternative' => $alternativeDone,
            ];
        });

        $assignedDmCount = $decisionSession->dms->count();
        $totalExpectation = $decisionSession->alternatives->count() * $decisionSession->criteria->count();

        $dmEvaluationsDone = $decisionSession->dms()
            ->whereHas('alternativeEvaluations', function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id);
            }, '>=', $totalExpectation)
            ->count();

        $tab = $request->query('tab');
        $borda = collect();
        $sawBorda = collect();
        $smartByDm = collect();
        $rho = null; // inisialisasi korelasi

        $resultService = app(DecisionResultService::class);

        if ($decisionSession->status === 'closed') {
            if (in_array($tab, ['hasil-akhir', 'analisis'])) {
                $borda = $resultService->borda($decisionSession);
            }

            if ($tab === 'analisis') {
                $sawService = app(SawRankingService::class);
                $sawBorda = $resultService->sawBordaBenchmark($decisionSession, $sawService);
                $smartByDm = $resultService->smartByDm($decisionSession);

                // Mapping ranking SMART
                $rankSmart = $borda->pluck('final_rank', 'alternative_id')->toArray();
                // Mapping ranking SAW
                $rankSaw = $sawBorda->pluck('final_rank', 'alternative_id')->toArray();

                if (!empty($rankSmart) && !empty($rankSaw)) {
                    $rho = $validationService->calculateSpearmanRho($rankSmart, $rankSaw);
                }
            }
        }

        return view('control.index', compact(
            'decisionSession',
            'assignedDmCount',
            'dmEvaluationsDone',
            'borda',
            'sawBorda',
            'smartByDm',
            'dmProgress',
            'rho' // kirim ke view
        ));
    }

    /**
     * Aktivasi Sesi
     */
    public function activate(DecisionSession $decisionSession, AhpGroupWeightService $ahpGroupWeightService)
    {
        $validStatuses = ['draft', 'configured'];
        abort_unless(in_array($decisionSession->status, $validStatuses), 403);

        if ($decisionSession->status === 'configured') {
            try {
                $decisionSession->getConnection()->transaction(function () use ($decisionSession, $ahpGroupWeightService) {
                    $ahpGroupWeightService->aggregate($decisionSession);
                });
            } catch (\Throwable $e) {
                Log::error('AHP Aggregation Error: ' . $e->getMessage());
                return back()->with('error', 'Gagal melakukan agregasi kriteria.');
            }
        }

        $nextStatus = ($decisionSession->status === 'draft') ? 'configured' : 'scoring';
        $decisionSession->update(['status' => $nextStatus]);

        return back()->with('success', 'Status sesi diperbarui.');
    }

    /**
     * Menutup sesi
     */
    public function close(DecisionSession $decisionSession, BordaRankingService $bordaRankingService)
    {
        abort_if($decisionSession->status !== 'scoring', 403);

        $assignedDmIds = $decisionSession->dms()->pluck('users.id')->toArray();
        $totalAssigned = count($assignedDmIds);
        $alternativeCount = $decisionSession->alternatives()->count();

        $expectedSmartCount = $totalAssigned * $alternativeCount;
        $actualSmartCount = SmartResultDm::where('decision_session_id', $decisionSession->id)->count();

        if ($actualSmartCount < $expectedSmartCount) {
            return back()->with('error', 'Penutupan gagal. Data SMART belum lengkap.');
        }

        try {
            $decisionSession->getConnection()->transaction(function () use ($decisionSession, $bordaRankingService) {
                $bordaRankingService->calculateAndPersist($decisionSession);
                $decisionSession->update(['status' => 'closed']);
            });

            return redirect()
                ->route('control.index', [
                    'decisionSession' => $decisionSession->id,
                    'tab' => 'analisis'
                ])
                ->with('success', 'Sesi ditutup.');
        } catch (\Exception $e) {
            Log::error('Borda Calculation Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
