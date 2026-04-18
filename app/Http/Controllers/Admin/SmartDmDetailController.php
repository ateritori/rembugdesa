<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Analysis\SmartDmDetailService;

class SmartDmDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request, SmartDmDetailService $service)
    {
        try {
            $request->validate([
                'session_id'     => 'required|integer',
                'alternative_id' => 'required|integer',
                'criteria_id'    => 'required|integer',
            ]);

            $result = $service->getScoresWithStats(
                $request->session_id,
                $request->alternative_id,
                $request->criteria_id
            );

            $assignedCount = $service->getAssignedDmCount(
                $request->session_id,
                $request->criteria_id
            );

            $missing = $service->getMissingEvaluators(
                $request->session_id,
                $request->alternative_id,
                $request->criteria_id
            );

            return response()->json([
                'data' => $result['data'],
                'stats' => $result['stats'],
                'assigned_count' => $assignedCount,
                'missing' => $missing,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ], 500);
        }
    }
}
