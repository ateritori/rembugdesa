<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use App\Services\AHP\AhpGroupSubmissionService;
use App\Services\Borda\BordaPipelineService;
use App\Services\State\SessionStateDashboardService;
use App\Services\State\SessionStateTransitionService;

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

        return view(
            'control.index',
            $service->build($decisionSession, $tab)
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
        BordaPipelineService $bordaPipelineService,
        SessionStateTransitionService $transitionService
    ) {
        try {
            $transitionService->close($decisionSession, $bordaPipelineService);
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
