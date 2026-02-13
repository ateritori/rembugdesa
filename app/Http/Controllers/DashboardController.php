<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DecisionSession;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {

            $totalUsers = User::count();
            $totalAdmins = User::role('admin')->count();
            $totalDms = User::role('dm')->count();
            $totalSessions = DecisionSession::count();

            return view('dashboard.superadmin', compact(
                'totalUsers',
                'totalAdmins',
                'totalDms',
                'totalSessions'
            ));
        }

        if ($user->hasRole('admin')) {

            $totalSessions = DecisionSession::count();
            $draftSessions = DecisionSession::where('status', 'draft')->count();
            $activeSessions = DecisionSession::where('status', 'scoring')->count();
            $closedSessions = DecisionSession::where('status', 'final')->count();

            $latestSessions = DecisionSession::latest()
                ->take(5)
                ->get();

            return view('dashboard.admin', compact(
                'totalSessions',
                'draftSessions',
                'activeSessions',
                'closedSessions',
                'latestSessions'
            ));
        }

        if ($user->hasRole('dm')) {

            $assignedSessions = $user->decisionSessions()
                ->with(['criteria', 'criteriaWeights'])
                ->get();

            $assignedSessions->each(function ($session) use ($user) {
                $session->dmHasCompleted = $session->criteriaWeights
                    ->where('dm_id', $user->id)
                    ->isNotEmpty();
            });

            $activeSessions = $assignedSessions->where('status', '!=', 'draft');

            $pendingTaskCount = $activeSessions
                ->where('dmHasCompleted', false)
                ->count();

            return view('dashboard.dm', [
                'assignedCount'     => $assignedSessions->count(),
                'activeCount'       => $activeSessions->count(),
                'pendingTaskCount'  => $pendingTaskCount,
                'assignedSessions'  => $assignedSessions,
            ]);
        }

        abort(403);
    }
}
