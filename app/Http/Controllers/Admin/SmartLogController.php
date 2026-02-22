<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Services\SMART\ExplainableSmartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class SmartLogController extends Controller
{
    public function index(
        DecisionSession $decisionSession,
        ExplainableSmartService $explainService
    ) {
        // Optional: cek role admin
        // abort_if(!Auth::user()->is_admin, 403);

        abort_if($decisionSession->status === 'draft', 403, 'Sesi masih draft.');

        $decisionSession->load([
            'dms',
            'alternatives' => fn($q) => $q->where('is_active', true),
            'criteria'     => fn($q) => $q->where('is_active', true)->orderBy('order')
        ]);

        $smartLogs = [];

        foreach ($decisionSession->dms as $dm) {

            try {
                $result = $explainService->calculate($decisionSession, $dm);

                // 🔒 Batasi hanya alternatif milik session ini
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

                        // Ensure criteria key exists
                        if (!isset($altData['criteria']) || !is_array($altData['criteria'])) {
                            $altData['criteria'] = [];
                        }

                        // Attach alternative model (from loaded session)
                        $altData['alternative'] =
                            $decisionSession->alternatives->firstWhere('id', $altId);

                        // Normalize each criterion
                        foreach ($altData['criteria'] as &$crit) {

                            // Weighted
                            if (!isset($crit['weighted'])) {
                                $crit['weighted'] =
                                    $crit['weighted_value'] ?? 0;
                            }

                            // Raw value
                            if (!isset($crit['raw'])) {
                                $crit['raw'] =
                                    $crit['raw_value'] ?? 0;
                            }

                            // Utility value
                            if (!isset($crit['utility'])) {
                                $crit['utility'] =
                                    $crit['utility_value'] ?? 0;
                            }

                            // Semantic (optional, if provided by service)
                            $crit['semantic'] =
                                $crit['semantic'] ?? null;

                            // Weight
                            $crit['weight'] =
                                $crit['weight'] ?? 0;
                        }

                        $altData['total_score'] =
                            $altData['total_score'] ?? 0;
                    }

                    // Recalculate ranking safely
                    $scores = [];
                    foreach ($result as $altId => $altData) {
                        $scores[$altId] = $altData['total_score'];
                    }

                    arsort($scores);

                    $rankCounter = 1;
                    foreach ($scores as $altId => $score) {
                        $result[$altId]['rank'] = $rankCounter++;
                    }

                    $smartLogs[$dm->id] = [
                        'dm'           => $dm,
                        'alternatives' => $result,
                    ];
                }
            } catch (Exception $e) {
                Log::error("SMART LOG ERROR (DM {$dm->id}): " . $e->getMessage());
            }
        }

        // Prepare ordered criteria names for header (C1, C2, dst)
        $criteriaNames = $decisionSession->criteria
            ->sortBy('order')
            ->pluck('name')
            ->values()
            ->toArray();

        return view('admin.smart-log.index', [
            'decisionSession' => $decisionSession,
            'smartLogs'       => $smartLogs,
            'criteriaNames'   => $criteriaNames,
        ]);
    }
}
