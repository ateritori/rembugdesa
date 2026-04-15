<?php

namespace App\Http\Controllers\Dm;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AHP\AhpIndividualSubmissionService;
use App\Services\AHP\AhpWorkspaceService;

class AhpPairwiseController extends Controller
{
    public function index(
        Request $request,
        DecisionSession $decisionSession,
        AhpWorkspaceService $workspaceService
    ) {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        $data = $workspaceService->getWorkspace($decisionSession, $user);

        return view('dms.index', $data);
    }

    public function store(
        Request $request,
        DecisionSession $decisionSession,
        AhpIndividualSubmissionService $service
    ) {
        $user = Auth::user();
        abort_if(!$user || !$user->hasRole('dm'), 403);

        $request->validate([
            'pairwise' => 'required|array|min:1',
        ]);

        try {
            $service->authorizeSubmission($decisionSession, $user);

            $service->submit(
                $decisionSession,
                $user,
                $request->input('pairwise')
            );

            return redirect()->route('dms.index', [
                'decisionSession' => $decisionSession->id,
                'tab' => 'penilaian-kriteria',
            ])->with('success', 'Penilaian disimpan dan dihitung di server.');
        } catch (\Exception $e) {
            Log::error('AHP Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id ?? null,
                'decision_session_id' => $decisionSession->id,
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}
