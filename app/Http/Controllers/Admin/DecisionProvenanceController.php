<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DecisionSession;

use App\Services\Analysis\SmartTraceService;
use App\Services\Borda\NestedBordaService;
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

        // Load sector/group weights
        $groupWeightRecord = $session->groupWeight;

        if ($groupWeightRecord) {
            $rawGroup = $groupWeightRecord->weights;

            if (is_string($rawGroup)) {
                $sectorWeights = json_decode($rawGroup, true) ?: [];
            } elseif (is_array($rawGroup)) {
                $sectorWeights = $rawGroup;
            } else {
                $sectorWeights = [];
            }

            // STRICT normalization: keys must be integer sector_id
            $sectorWeights = collect($sectorWeights)
                ->mapWithKeys(function ($v, $k) {
                    if (!is_numeric($k)) {
                        throw new \Exception("Invalid sector key in JSON: {$k}");
                    }
                    return [(int)$k => (float)$v];
                })
                ->all();
        } else {
            $sectorWeights = [];
        }

        // ================================
        // SMART TRACE (ALL DM + SYSTEM)
        // ================================
        $traceService = new SmartTraceService();

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

        $traces['system'] = $traceService->build($session, null, []);

        // ================================
        // NESTED BORDA (DM → DOMAIN → FINAL)
        // ================================
        $bordaService = new NestedBordaService();
        $borda = $bordaService->calculateFromTraces($traces);

        return view('admin.provenance.index', compact(
            'session',
            'traces',
            'sectorWeights',
            'borda'
        ));
    }
}
