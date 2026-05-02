<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DecisionSession;

use App\Services\Analysis\SmartTraceService;
use App\Services\Borda\NestedBordaService;
use App\Services\Analysis\SawTraceService;
use App\Services\AHP\AhpProvenanceService;

class DecisionProvenanceController extends Controller
{
    public function show(Request $request, int $sessionId)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            if (method_exists($request->user(), 'isAdmin') && !$request->user()->isAdmin()) {
                abort(403);
            }

            $session = DecisionSession::findOrFail($sessionId);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat sesi keputusan',
                'error' => $e->getMessage()
            ], 500);
        }

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
        // AHP PROVENANCE (SERVICE-BASED)
        // ================================
        $ahpService = new AhpProvenanceService();

        $ahp = $ahpService->build($session);

        $individualProvenance = $ahp['individual'] ?? [];
        $groupProvenance = $ahp['group'] ?? [];

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

        // Validasi: pastikan ada DM scores
        if (empty($userIds)) {
            return response()->json([
                'message' => 'Tidak ada data evaluasi dari Decision Maker untuk sesi ini'
            ], 422);
        }

        $traces = [];

        try {
            foreach ($userIds as $userId) {
                $traces[$userId] = $traceService->buildUserFullTrace($session, $userId);
            }

            $traces['system'] = $traceService->buildUserFullTrace($session, null);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses SMART trace',
                'error' => $e->getMessage()
            ], 422);
        }

        // ================================
        // SAW TRACE (ALL DM + SYSTEM) - PARALLEL
        // ================================
        $sawTraceService = new SawTraceService();

        $sawTraces = [];

        try {
            foreach ($userIds as $userId) {
                $sawTraces[$userId] = $sawTraceService->buildUserFullTrace($session, $userId);
            }

            $sawTraces['system'] = $sawTraceService->buildUserFullTrace($session, null);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses SAW trace',
                'error' => $e->getMessage()
            ], 422);
        }

        // ================================
        // NESTED BORDA (DM → DOMAIN → FINAL)
        // ================================
        $bordaService = new NestedBordaService();

        try {
            $borda = $bordaService->calculateFromTraces($traces);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghitung SMART Borda',
                'error' => $e->getMessage()
            ], 422);
        }

        // SAW BORDA (parallel result)
        try {
            $sawBorda = $bordaService->calculateFromTraces($sawTraces);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghitung SAW Borda',
                'error' => $e->getMessage()
            ], 422);
        }

        return view('admin.provenance.index', compact(
            'session',
            'traces',
            'sawTraces',
            'sectorWeights',
            'borda',
            'sawBorda',
            'groupProvenance',
            'individualProvenance'
        ));
    }
}
