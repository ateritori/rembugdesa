<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use Illuminate\Http\Request;

class DecisionSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Daftar semua sesi keputusan.
     */
    public function index()
    {
        $sessions = DecisionSession::withCount(['dms', 'alternatives'])
            ->latest()
            ->get();

        return view('decision-sessions.index', compact('sessions'));
    }

    /**
     * Detail Sesi Keputusan
     */
    public function show(DecisionSession $decisionSession)
    {
        return view('decision-sessions.show', compact('decisionSession'));
    }

    /**
     * Form Buat Sesi Keputusan
     */
    public function create()
    {
        return view('decision-sessions.create');
    }

    /**
     * Store new session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        $session = DecisionSession::create(array_merge($validated, [
            'status'     => 'draft',
            'created_by' => auth()->id(),
        ]));

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil dibuat.');
    }

    /**
     * Form Edit Sesi Keputusan
     */
    public function edit(DecisionSession $decisionSession)
    {
        return view('decision-sessions.edit', compact('decisionSession'));
    }

    /**
     * Update Sesi Keputusan
     */
    public function update(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status === 'closed', 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|digits:4',
        ]);

        $decisionSession->update($validated);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil diperbarui.');
    }

    /**
     * Hapus Sesi Keputusan
     */
    public function destroy(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

        $decisionSession->delete();

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Sesi berhasil dihapus.');
    }
}
