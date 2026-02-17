<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;

class DecisionSessionController extends Controller
{
    /**
     * Monitoring seluruh sesi keputusan (lintas admin).
     * Searching dilakukan di client (Alpine).
     */
    public function index()
    {
        $sessions = DecisionSession::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('superadmin.decision-sessions.index', compact('sessions'));
    }

    /**
     * Detail sesi keputusan (read-only).
     */
    public function show(DecisionSession $session)
    {
        return view('superadmin.decision-sessions.show', compact('session'));
    }

    /**
     * Superadmin hanya boleh melakukan rollback status sesi (mundur).
     */
    public function updateStatus(Request $request, DecisionSession $session)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        // Validasi status harus resmi dari model
        if (! in_array($request->status, DecisionSession::STATUSES, true)) {
            return back()->withErrors([
                'status' => 'Status sesi tidak valid.',
            ]);
        }

        if ($session->status === $request->status) {
            return back()->with('info', 'Status sesi tidak berubah.');
        }

        $fromIndex = array_search($session->status, DecisionSession::STATUS_ORDER, true);
        $toIndex   = array_search($request->status, DecisionSession::STATUS_ORDER, true);

        // Jika status tidak ditemukan di lifecycle
        if ($fromIndex === false || $toIndex === false) {
            return back()->withErrors([
                'status' => 'Lifecycle status tidak dikenali.',
            ]);
        }

        // Rollback-only: tidak boleh maju atau setara
        if ($toIndex >= $fromIndex) {
            return back()->withErrors([
                'status' => 'Superadmin hanya diperbolehkan melakukan rollback status.',
            ]);
        }

        $oldStatus = $session->status;

        $session->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            "Status sesi berhasil di-rollback dari {$oldStatus} ke {$session->status}."
        );
    }
}
