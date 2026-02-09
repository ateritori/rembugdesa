<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DecisionSessionController extends Controller
{
    /**
     * Middleware sudah didefinisikan di Route,
     * namun tetap bagus sebagai proteksi lapis kedua.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $sessions = DecisionSession::latest()->get();
        return view('decision-sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('decision-sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        DecisionSession::create([
            'name'       => $request->name,
            'year'       => $request->year,
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session created successfully.');
    }

    public function edit(DecisionSession $decisionSession)
    {
        // Edit detail session hanya boleh saat draft
        abort_if($decisionSession->status !== 'draft', 403, 'Hanya session berstatus draft yang bisa diubah.');

        return view('decision-sessions.edit', compact('decisionSession'));
    }

    public function update(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        $decisionSession->update([
            'name' => $request->name,
            'year' => $request->year,
        ]);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session updated.');
    }

    public function activate(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

        // Validasi kesiapan data sebelum aktif
        if ($decisionSession->criteria()->where('is_active', true)->count() < 2) {
            return back()->withErrors('Minimal 2 kriteria aktif diperlukan.');
        }

        if ($decisionSession->alternatives()->where('is_active', true)->count() < 2) {
            return back()->withErrors('Minimal 2 alternatif aktif diperlukan.');
        }

        if ($decisionSession->dms()->count() < 1) {
            return back()->withErrors('Minimal 1 decision maker diperlukan.');
        }

        $decisionSession->update(['status' => 'active']);

        return back()->with('success', 'Sesi berhasil diaktifkan.');
    }

    public function close(DecisionSession $decisionSession)
    {
        // Pastikan statusnya memang sudah aktif sebelum bisa ditutup
        abort_if($decisionSession->status !== 'active' && $decisionSession->status !== 'alternatives', 403);

        $decisionSession->update(['status' => 'closed']);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil ditutup.');
    }

    public function destroy(DecisionSession $decisionSession)
    {
        // Hanya izinkan hapus jika masih draft atau sesuai kebijakan Anda
        abort_if($decisionSession->status !== 'draft', 403, 'Session yang sudah berjalan tidak bisa dihapus.');

        $decisionSession->delete();

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session deleted.');
    }

    // ================= PENUGASAN DM (BAGIAN YANG BERMASALAH SEBELUMNYA) =================

    public function assignDms(DecisionSession $decisionSession)
    {
        /** * PERBAIKAN:
         * Jangan gunakan abort_if($status !== 'draft') jika Admin ingin
         * melihat daftar DM setelah session aktif. Cukup batasi pengeditan saja.
         */

        // Mengambil user dengan role 'dm'.
        // Jika Anda tidak pakai Spatie, gunakan: User::where('role', 'dm')->get();
        $dms = User::role('dm')->get();

        // Ambil ID DM yang sudah terhubung dengan session ini
        $assignedDmIds = $decisionSession->dms()->pluck('users.id')->toArray();

        return view('decision-sessions.assign-dms', compact(
            'decisionSession',
            'dms',
            'assignedDmIds'
        ));
    }

    public function storeAssignedDms(Request $request, DecisionSession $decisionSession)
    {
        // Proteksi: Hanya boleh edit DM jika session belum ditutup
        abort_if($decisionSession->status === 'closed', 403, 'Tidak bisa mengubah DM pada sesi yang sudah tutup.');

        $request->validate([
            'dm_ids' => 'nullable|array',
            'dm_ids.*' => 'exists:users,id'
        ]);

        // Filter hanya ID yang memang memiliki role DM untuk keamanan
        $dmIds = User::role('dm')
            ->whereIn('id', $request->input('dm_ids', []))
            ->pluck('id')
            ->toArray();

        // Sinkronisasi tabel pivot
        $decisionSession->dms()->sync($dmIds);

        return redirect()
            ->route('decision-sessions.assign-dms', $decisionSession->id)
            ->with('success', 'Daftar Decision Maker berhasil diperbarui.');
    }
}
