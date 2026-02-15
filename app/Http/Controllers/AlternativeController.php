<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlternativeController extends Controller
{
    /**
     * Menampilkan daftar alternatif berdasarkan sesi keputusan.
     */
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
     * Menyimpan alternatif baru.
     * Guard: Hanya bisa dilakukan saat sesi berstatus 'draft'.
     */
    public function store(Request $request, DecisionSession $decisionSession)
    {
        // Guard: Validasi status sesi
        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat diubah.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request, $decisionSession) {
                // Tentukan urutan & kode (A1, A2, dst)
                // Menggunakan lockForUpdate untuk mencegah race condition pada penomoran kode
                $lastOrder = $decisionSession->alternatives()->max('order') ?? 0;
                $order = $lastOrder + 1;
                $code = 'A' . $order;

                $decisionSession->alternatives()->create([
                    'code' => $code,
                    'name' => $request->name,
                    'order' => $order,
                    'is_active' => true,
                ]);

                return redirect()
                    ->route('alternatives.index', $decisionSession->id)
                    ->with('success', 'Alternatif berhasil ditambahkan.');
            });
        } catch (\Exception $e) {
            Log::error('Error storing alternative: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan alternatif.');
        }
    }

    /**
     * Memperbarui nama alternatif.
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

        try {
            $alternative->update([
                'name' => $request->name,
            ]);

            return redirect()
                ->route('alternatives.index', $decisionSession->id)
                ->with('success', 'Alternatif berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating alternative: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui alternatif.');
        }
    }

    /**
     * Mengaktifkan/Nonaktifkan alternatif (Toggle).
     */
    public function toggle(Alternative $alternative)
    {
        $decisionSession = $alternative->decisionSession;

        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat diubah.');
        }

        try {
            $alternative->update([
                'is_active' => ! $alternative->is_active,
            ]);

            return redirect()
                ->route('alternatives.index', $decisionSession->id)
                ->with('success', 'Status alternatif berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error toggling alternative: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status alternatif.');
        }
    }

    /**
     * Menghapus alternatif (Soft delete).
     */
    public function destroy(Alternative $alternative)
    {
        $decisionSession = $alternative->decisionSession;

        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif. Alternatif tidak dapat dihapus.');
        }

        try {
            $alternative->delete();

            return redirect()
                ->route('alternatives.index', $decisionSession->id)
                ->with('success', 'Alternatif berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting alternative: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus alternatif.');
        }
    }
}
