<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Services\Borda\BordaLogService;
use App\Models\SmartResultDm;
use App\Models\User;
use App\Models\Alternative;

class BordaLogController extends Controller
{
    protected BordaLogService $bordaLogService;

    public function __construct(BordaLogService $bordaLogService)
    {
        $this->bordaLogService = $bordaLogService;
    }

    /**
     * Audit & Explain Borda aggregation
     */
    public function show(DecisionSession $decisionSession)
    {
        $log = $this->bordaLogService->generate($decisionSession);

        $matrix = $log['matrix'] ?? [];

        // Ambil DM yang terlibat dalam sesi ini
        $dmIds = SmartResultDm::where('decision_session_id', $decisionSession->id)
            ->distinct()
            ->pluck('dm_id');

        $dms = User::whereIn('id', $dmIds)
            ->get()
            ->keyBy('id');

        // Buat mapping D1, D2, ...
        $dmMapping = [];
        $counter = 1;
        foreach ($dmIds as $dmId) {
            $dmMapping[$dmId] = 'D' . $counter++;
        }

        // Ambil alternatif sesuai sesi
        $alternatives = Alternative::where('decision_session_id', $decisionSession->id)
            ->get()
            ->keyBy('id');

        return view('admin.borda-log.index', [
            'matrix'       => $matrix,
            'dms'          => $dms,
            'dmMapping'    => $dmMapping,
            'alternatives' => $alternatives,
        ]);
    }
}
