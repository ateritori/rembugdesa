<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\DecisionSession;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        $alternatives = $decisionSession->alternatives()
            ->orderBy('order')
            ->get();

        return view('alternatives.index', compact(
            'decisionSession',
            'alternatives'
        ));
    }

    /**
     * Store new alternative
     */
    public function store(Request $request, DecisionSession $decisionSession)
    {
        // Guard: hanya saat draft
        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat diubah.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Tentukan urutan & kode (A1, A2, dst)
        $lastOrder = $decisionSession->alternatives()->max('order') ?? 0;
        $order = $lastOrder + 1;
        $code = 'A' . $order;

        Alternative::create([
            'decision_session_id' => $decisionSession->id,
            'code' => $code,
            'name' => $request->name,
            'order' => $order,
            'is_active' => true,
        ]);

        return redirect()
            ->route('alternatives.index', $decisionSession->id)
            ->with('success', 'Alternatif berhasil ditambahkan.');
    }

    /**
     * Update alternative name
     */
    public function update(Request $request, Alternative $alternative)
    {
        $decisionSession = $alternative->decisionSession;

        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat diubah.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $alternative->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('alternatives.index', $decisionSession->id)
            ->with('success', 'Alternatif berhasil diperbarui.');
    }

    /**
     * Toggle active / not active
     */
    public function toggle(Alternative $alternative)
    {
        $decisionSession = $alternative->decisionSession;

        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat diubah.');
        }

        $alternative->update([
            'is_active' => ! $alternative->is_active,
        ]);

        return redirect()
            ->route('alternatives.index', $decisionSession->id)
            ->with('success', 'Status alternatif diperbarui.');
    }

    /**
     * Soft delete alternative
     */
    public function destroy(Alternative $alternative)
    {
        $decisionSession = $alternative->decisionSession;

        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat dihapus.');
        }

        $alternative->delete();

        return redirect()
            ->route('alternatives.index', $decisionSession->id)
            ->with('success', 'Alternatif berhasil dihapus.');
    }
}
