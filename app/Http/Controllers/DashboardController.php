<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DecisionSession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama berdasarkan role user.
     */
    public function index()
    {
        $user = Auth::user();

        // Guard: Jika tidak terautentikasi (meskipun middleware auth biasanya sudah menangani ini)
        if (!$user) {
            abort(401);
        }

        // --- DASHBOARD SUPERADMIN ---
        if ($user->hasRole('superadmin')) {
            $stats = [
                'totalUsers'    => User::count(),
                'totalAdmins'   => User::role('admin')->count(),
                'totalDms'      => User::role('dm')->count(),
                'totalSessions' => DecisionSession::count(),
            ];

            return view('dashboard.superadmin', $stats);
        }

        // --- DASHBOARD ADMIN ---
        if ($user->hasRole('admin')) {
            // Optimasi: Gunakan agregasi kolom tunggal untuk mengurangi query database
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

        // --- DASHBOARD DM (Decision Maker) ---
        if ($user->hasRole('dm')) {
            /**
             * Optimasi: Gunakan withCount untuk mengecek keberadaan relasi
             * tanpa harus meload seluruh data criteriaWeights ke memori.
             */
            $assignedSessions = $user->decisionSessions()
                ->withCount(['criteriaWeights as has_weighted' => function ($query) use ($user) {
                    $query->where('dm_id', $user->id);
                }])
                ->with(['criteria']) // Tetap load criteria jika dibutuhkan di View
                ->get();

            // Memisahkan data menggunakan Collection method (lebih cepat daripada query ulang)
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

        abort(403, 'Anda tidak memiliki peran yang valid untuk mengakses dashboard.');
    }
}
