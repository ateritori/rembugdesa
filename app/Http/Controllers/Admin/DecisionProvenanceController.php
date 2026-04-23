<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DecisionSession;

use App\Services\Evaluation\SmartCalculationService;
use App\Services\Analysis\SmartTraceService;
// nanti tinggal tambah:
// use App\Services\Analysis\SawCalculationService;
// use App\Services\Analysis\SawTraceService;

class DecisionProvenanceController extends Controller
{
    public function show(Request $request, $sessionId)
    {
        // optional: specific user or all DM
        if (!$request->user()) {
            return response()->json([
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        if (method_exists($request->user(), 'isAdmin') && !$request->user()->isAdmin()) {
            abort(403);
        }

        $session = DecisionSession::findOrFail($sessionId);

        // 🔥 gunakan SmartTraceService sebagai sumber kebenaran (detail per kriteria)
        $traceService = new SmartTraceService();

        // ambil semua user (DM + system/null)
        $userIds = \App\Models\EvaluationScore::where('decision_session_id', $session->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->values()
            ->toArray();

        $traces = [];

        foreach ($userIds as $userId) {
            $traces[$userId] = $traceService->buildUserFullTrace($session, $userId);
        }

        $traces['system'] = $traceService->build(
            $session,
            null,
            []
        );

        return view('admin.provenance.index', [
            'traces' => $traces,
            'session' => $session
        ]);
    }
}
