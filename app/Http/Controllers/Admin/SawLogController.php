<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Services\SAW\SawLogService;
use Illuminate\Support\Facades\Log;
use Exception;

class SawLogController extends Controller
{
    public function index(
        DecisionSession $decisionSession,
        SawLogService $sawService
    ) {

        abort_if($decisionSession->status === 'draft', 403, 'Sesi masih draft.');

        $decisionSession->load([
            'dms',
            'alternatives' => fn($q) => $q->where('is_active', true),
            'criteria'     => fn($q) => $q->where('is_active', true)->orderBy('order')
        ]);

        $sawLogs = [];

        foreach ($decisionSession->dms as $dm) {

            try {

                $result = $sawService->calculate($decisionSession, $dm);

                $validAlternativeIds = $decisionSession->alternatives
                    ->pluck('id')
                    ->toArray();

                $result = collect($result)
                    ->filter(function ($value, $altId) use ($validAlternativeIds) {
                        return in_array($altId, $validAlternativeIds);
                    })
                    ->toArray();

                if (!empty($result)) {

                    foreach ($result as $altId => &$altData) {

                        if (!isset($altData['criteria']) || !is_array($altData['criteria'])) {
                            $altData['criteria'] = [];
                        }

                        $altData['alternative'] =
                            $decisionSession->alternatives->firstWhere('id', $altId);

                        foreach ($altData['criteria'] as &$crit) {

                            $crit['weighted']   = $crit['weighted']   ?? 0;
                            $crit['raw']        = $crit['raw']        ?? 0;
                            $crit['normalized'] = $crit['normalized'] ?? 0;
                        }

                        $altData['total_score'] =
                            $altData['total_score'] ?? 0;
                    }

                    $scores = [];
                    foreach ($result as $altId => $altData) {
                        $scores[$altId] = $altData['total_score'];
                    }

                    arsort($scores);

                    $rankCounter = 1;
                    foreach ($scores as $altId => $score) {
                        $result[$altId]['rank'] = $rankCounter++;
                    }

                    $sawLogs[$dm->id] = [
                        'dm'             => $dm,
                        'alternatives'   => $result,
                        'criteria_names' => $decisionSession->criteria
                            ->sortBy('order')
                            ->pluck('name')
                            ->values()
                            ->toArray(),
                    ];
                }
            } catch (Exception $e) {
                Log::error("SAW LOG ERROR (DM {$dm->id}): " . $e->getMessage());
            }
        }

        $sawLogs = array_values($sawLogs);

        return view('admin.saw-log.index', [
            'decisionSession' => $decisionSession,
            'sawLogs'         => $sawLogs,
        ]);
    }
}
