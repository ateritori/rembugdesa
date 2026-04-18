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
        $userId = $request->user()->id ?? null;

        if (!$userId) {
            return response()->json([
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        $session = DecisionSession::findOrFail($sessionId);

        // === PIPELINE ===
        // SMART (existing calculation)
        $smartResults = app(SmartCalculationService::class)
            ->calculate($session, $userId);

        // SMART TRACE (provenance)
        $smartTrace = app(SmartTraceService::class)
            ->build($session, $userId, $smartResults);

        // === EXTENSION POINT ===
        // nanti tinggal aktifkan:
        /*
        $sawResults = app(SawCalculationService::class)
            ->calculate($session, $userId);

        $sawTrace = app(SawTraceService::class)
            ->build($session, $userId, $sawResults);
        */

        return view('admin.provenance.index', [
            'data' => [
                'meta' => [
                    'decision_session_id' => $session->id,
                    'user_id' => $userId,
                ],
                'pipeline' => [
                    'smart' => [
                        'results' => $smartResults,
                        'trace' => $smartTrace,
                    ],
                ],
                'trace' => $this->flattenTrace($smartTrace),
            ]
        ]);
    }

    /**
     * Flatten trace supaya mudah ditampilkan (opsional)
     */
    protected function flattenTrace(array $trace)
    {
        return collect($trace)->map(function ($item) {

            return [
                'alternative_id' => $item['alternative_id'],
                'name' => $item['name'],
                'final_score' => $item['final_score'],
                'reconstructed_score' => $item['reconstructed_score'],
                'delta' => $item['delta'],
                'steps_count' => count($item['steps']),
            ];
        })->values();
    }
}
