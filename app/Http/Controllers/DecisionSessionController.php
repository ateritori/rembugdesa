<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\Criteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

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
        // Konfigurasi hanya boleh dilakukan dari draft
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

        // Validasi kelengkapan aturan scoring setiap kriteria aktif
        $activeCriteria = $decisionSession->criteria()
            ->where('is_active', true)
            ->with(['scoringRule.parameters'])
            ->get();

        $criteriaScoringComplete = $activeCriteria->every(function ($criteria) {
            if (!$criteria->scoringRule) {
                return false;
            }

            return $criteria->scoringRule->isComplete();
        });

        if (!$criteriaScoringComplete) {
            return back()->withErrors(
                'Aturan scoring kriteria belum lengkap. Lengkapi semua kriteria sebelum membuka sesi.'
            );
        }

        $decisionSession->update(['status' => 'configured']);

        return back()->with('success', 'Sesi berhasil diaktifkan.');
    }

    public function close(DecisionSession $decisionSession)
    {
        // Pastikan statusnya memang sudah aktif sebelum bisa ditutup
        abort_if($decisionSession->status !== 'aggregated', 403);

        $decisionSession->update(['status' => 'final']);

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
        try {
            // Ambil user dengan role dm jika role ada
            $dms = User::role('dm')->get();
        } catch (RoleDoesNotExist $e) {
            // Jika role dm belum ada sama sekali
            $dms = collect();
        }

        // Ambil ID DM yang sudah terhubung dengan session ini
        $assignedDmIds = $decisionSession->dms()
            ->pluck('users.id')
            ->toArray();

        return view('decision-sessions.assign-dms', compact(
            'decisionSession',
            'dms',
            'assignedDmIds'
        ));
    }

    public function storeAssignedDms(Request $request, DecisionSession $decisionSession)
    {
        // Proteksi: tidak boleh ubah DM jika session sudah dikunci
        abort_if(in_array($decisionSession->status, ['aggregated', 'final']), 403, 'Tidak bisa mengubah DM pada sesi yang sudah dikunci.');

        $request->validate([
            'dm_ids' => 'nullable|array',
            'dm_ids.*' => 'exists:users,id'
        ]);

        try {
            // Ambil hanya user yang benar-benar punya role dm
            $dmIds = User::role('dm')
                ->whereIn('id', $request->input('dm_ids', []))
                ->pluck('id')
                ->toArray();
        } catch (RoleDoesNotExist $e) {
            // Jika role dm tidak ada, pastikan pivot dikosongkan
            $dmIds = [];
        }

        // Sinkronisasi pivot dengan aman
        $decisionSession->dms()->sync($dmIds);

        return redirect()
            ->route('decision-sessions.assign-dms', $decisionSession->id)
            ->with('success', 'Daftar Decision Maker berhasil diperbarui.');
    }
}
