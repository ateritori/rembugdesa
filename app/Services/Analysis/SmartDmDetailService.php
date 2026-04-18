<?php

namespace App\Services\Analysis;

use Illuminate\Support\Facades\DB;

class SmartDmDetailService
{
    /**
     * Ambil detail penilaian DM (VALID sesuai assignment)
     */
    public function getScores(int $sessionId, int $alternativeId, int $criteriaId)
    {
        try {
            // STEP 1: ambil DM yang di-assign ke kriteria ini
            $assignedDmIds = DB::table('decision_session_assignments')
                ->where('decision_session_id', $sessionId)
                ->where('criteria_id', $criteriaId)
                ->where('can_evaluate', 1)
                ->pluck('user_id');

            if ($assignedDmIds->isEmpty()) {
                return collect([]);
            }

            // STEP 2: ambil nilai dari DM tersebut
            return DB::table('evaluation_scores as es')
                ->join('users as u', 'u.id', '=', 'es.user_id')
                ->where('es.decision_session_id', $sessionId)
                ->where('es.alternative_id', $alternativeId)
                ->where('es.criteria_id', $criteriaId)
                ->whereIn('es.user_id', $assignedDmIds)
                ->whereNotNull('es.value')
                ->select([
                    'u.id as dm_id',
                    'u.name as dm_name',
                    'es.value',
                ])
                ->orderBy('u.name')
                ->get();
        } catch (\Throwable $e) {
            return collect([]);
        }
    }

    /**
     * Detail + statistik (untuk modal)
     */
    public function getScoresWithStats(int $sessionId, int $alternativeId, int $criteriaId)
    {
        $rows = $this->getScores($sessionId, $alternativeId, $criteriaId);

        if ($rows->isEmpty()) {
            return [
                'data' => [],
                'stats' => [
                    'avg' => 0,
                    'min' => 0,
                    'max' => 0,
                    'count' => 0,
                ]
            ];
        }

        $values = collect($rows)
            ->pluck('value')
            ->map(fn($v) => (float)$v);

        return [
            'data' => $rows,
            'stats' => [
                'avg'   => round($values->avg(), 4),
                'min'   => $values->min(),
                'max'   => $values->max(),
                'count' => $values->count(),
            ]
        ];
    }

    /**
     * Jumlah DM yang seharusnya menilai (berdasarkan assignment)
     */
    public function getAssignedDmCount(int $sessionId, int $criteriaId)
    {
        return DB::table('decision_session_assignments')
            ->where('decision_session_id', $sessionId)
            ->where('criteria_id', $criteriaId)
            ->where('can_evaluate', 1)
            ->count();
    }

    /**
     * DM yang belum mengisi nilai (gap analysis 🔥)
     */
    public function getMissingEvaluators(int $sessionId, int $alternativeId, int $criteriaId)
    {
        $assigned = DB::table('decision_session_assignments')
            ->where('decision_session_id', $sessionId)
            ->where('criteria_id', $criteriaId)
            ->where('can_evaluate', 1)
            ->pluck('user_id');

        $scored = DB::table('evaluation_scores')
            ->where('decision_session_id', $sessionId)
            ->where('alternative_id', $alternativeId)
            ->where('criteria_id', $criteriaId)
            ->pluck('user_id');

        $missingIds = $assigned->diff($scored);

        return DB::table('users')
            ->whereIn('id', $missingIds)
            ->pluck('name');
    }
}
