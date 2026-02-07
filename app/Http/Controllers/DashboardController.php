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
            $activeSessions = DecisionSession::where('status', 'active')->count();
            $closedSessions = DecisionSession::where('status', 'closed')->count();

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

            $activeSessions = $assignedSessions->where('status', 'active');

            $pendingTaskCount = $activeSessions->filter(function ($session) use ($user) {
                return ! $session->criteriaWeights
                    ->where('dm_id', $user->id)
                    ->count();
            })->count();

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
