<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DecisionSession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard utama berdasarkan peran user.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        // Dashboard Superadmin
        if ($user->hasRole('superadmin')) {
            $stats = [
                'totalUsers'    => User::count(),
                'totalAdmins'   => User::role('admin')->count(),
                'totalDms'      => User::role('dm')->count(),
                'totalSessions' => DecisionSession::count(),
            ];

            return view('dashboard.superadmin', $stats);
        }

        // Dashboard Admin
        if ($user->hasRole('admin')) {
            $sessionStats = DecisionSession::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'scoring' THEN 1 ELSE 0 END) as scoring,
                SUM(CASE WHEN status = 'final' THEN 1 ELSE 0 END) as final
            ")->first();

            $latestSessions = DecisionSession::latest()
                ->take(5)
                ->get();

            return view('dashboard.admin', [
                'totalSessions'  => $sessionStats->total,
                'draftSessions'  => $sessionStats->draft,
                'activeSessions' => $sessionStats->scoring,
                'closedSessions' => $sessionStats->final,
                'latestSessions' => $latestSessions,
            ]);
        }

        // Dashboard Decision Maker (DM)
        if ($user->hasRole('dm')) {
            $assignedSessions = $user->decisionSessions()
                ->withCount(['criteriaWeights as has_weighted' => function ($query) use ($user) {
                    $query->where('dm_id', $user->id);
                }])
                ->with(['criteria'])
                ->get();

            $activeSessions = $assignedSessions->where('status', '!=', 'draft');

            $pendingTaskCount = $activeSessions
                ->where('has_weighted', 0)
                ->count();

            return view('dashboard.dm', [
                'assignedCount'     => $assignedSessions->count(),
                'activeCount'       => $activeSessions->count(),
                'pendingTaskCount'  => $pendingTaskCount,
                'assignedSessions'  => $assignedSessions,
            ]);
        }

        abort(403, 'Akses ditolak: Peran tidak valid.');
    }
}
