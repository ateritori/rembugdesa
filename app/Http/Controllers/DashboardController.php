<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\CriteriaPairwise;
use App\Models\AlternativeEvaluation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_if(!$user, 401);

        // SUPERADMIN
        if ($user->hasRole('superadmin')) {
            return view('dashboard.superadmin', [
                'totalUsers'    => User::count(),
                'totalAdmins'   => User::role('admin')->count(),
                'totalDms'      => User::role('dm')->count(),
                'totalSessions' => DecisionSession::count(),
            ]);
        }

        // ADMIN
        if ($user->hasRole('admin')) {
            $sessionStats = DecisionSession::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'scoring' THEN 1 ELSE 0 END) as scoring,
                SUM(CASE WHEN status = 'final' THEN 1 ELSE 0 END) as final
            ")->first();

            return view('dashboard.admin', [
                'totalSessions'  => $sessionStats->total,
                'draftSessions'  => $sessionStats->draft,
                'activeSessions' => $sessionStats->scoring,
                'closedSessions' => $sessionStats->final,
                'latestSessions' => DecisionSession::latest()->take(5)->get(),
            ]);
        }

        // DECISION MAKER
        if ($user->hasRole('dm')) {

            // QUERY ASLI — TIDAK DIUBAH
            $assignedSessions = DecisionSession::whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
                ->withCount(['criteriaWeights as has_weighted' => function ($query) use ($user) {
                    $query->where('dm_id', $user->id);
                }])
                ->with([
                    'criteria',
                    'assignments' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
                ])
                ->get();

            // TAMBAHAN INFO SAJA
            $assignedSessions->each(function ($session) use ($user) {
                $session->dmHasCompleted =
                    CriteriaPairwise::where('decision_session_id', $session->id)
                    ->where('dm_id', $user->id)
                    ->exists();

                $session->hasCompletedEvaluation =
                    AlternativeEvaluation::where('decision_session_id', $session->id)
                    ->where('dm_id', $user->id)
                    ->exists();
            });

            // LOGIKA ASLI — TIDAK DIUBAH
            $activeSessions = $assignedSessions->where('status', '!=', 'draft');

            $pendingTaskCount = $activeSessions
                ->where('has_weighted', 0)
                ->count();

            return view('dashboard.dm', [
                'assignedCount'    => $assignedSessions->count(),
                'activeCount'      => $activeSessions->count(),
                'pendingTaskCount' => $pendingTaskCount,
                'assignedSessions' => $assignedSessions,
            ]);
        }

        abort(403, 'Akses ditolak.');
    }
}
