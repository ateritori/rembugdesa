<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlternativeController extends Controller
{
    /**
     * Menampilkan daftar alternatif per sesi.
     */
    public function index(DecisionSession $decisionSession)
    {
        $alternatives = $decisionSession->alternatives()
            ->orderBy('order')
            ->get();

        $criteriaLevel1 = Criteria::where('decision_session_id', $decisionSession->id)
            ->where('level', 1)
            ->orderBy('order')
            ->get();

        return view('alternatives.index', compact('decisionSession', 'alternatives', 'criteriaLevel1'));
    }

    /**
     * Menyimpan alternatif baru dengan penomoran otomatis.
     */
    public function store(Request $request, DecisionSession $decisionSession)
    {
        if ($decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'rab' => 'required',
            'coverage' => 'required|integer',
            'beneficiaries' => 'required|integer|min:0',
            'criteria_id' => 'required|exists:criteria,id',
        ]);

        try {
            $lastOrder = $decisionSession->alternatives()->max('order') ?? 0;
            $order = $lastOrder + 1;

            $rab = str_replace('.', '', $request->rab);

            $decisionSession->alternatives()->create([
                'code' => 'A' . $order,
                'name' => $request->name,
                'order' => $order,
                'is_active' => true,
                'rab' => $rab,
                'coverage' => $request->coverage,
                'beneficiaries' => $request->beneficiaries,
                'criteria_id' => $request->criteria_id,
            ]);

            return redirect()
                ->route('alternatives.index', $decisionSession->id)
                ->with('success', 'Alternatif berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Store alternative failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    /**
     * Memperbarui detail nama alternatif.
     */
    public function update(Request $request, Alternative $alternative)
    {
        if ($alternative->decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'rab' => 'required',
            'coverage' => 'required|integer',
            'beneficiaries' => 'required|integer|min:0',
            'criteria_id' => 'required|exists:criteria,id',
        ]);

        try {
            $rab = str_replace('.', '', $request->rab);

            $alternative->update([
                'name' => $request->name,
                'rab' => $rab,
                'coverage' => $request->coverage,
                'beneficiaries' => $request->beneficiaries,
                'criteria_id' => $request->criteria_id,
            ]);

            return redirect()
                ->route('alternatives.index', $alternative->decision_session_id)
                ->with('success', 'Alternatif diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update alternative failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggle(Alternative $alternative)
    {
        if ($alternative->decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif.');
        }

        try {
            $alternative->update([
                'is_active' => !$alternative->is_active,
            ]);

            return back()->with('success', 'Status berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('Toggle status failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status.');
        }
    }

    /**
     * Menghapus record alternatif.
     */
    public function destroy(Alternative $alternative)
    {
        if ($alternative->decisionSession->status !== 'draft') {
            return back()->with('error', 'Sesi sudah aktif.');
        }

        try {
            $alternative->delete();

            return redirect()
                ->route('alternatives.index', $alternative->decision_session_id)
                ->with('success', 'Alternatif dihapus.');
        } catch (\Exception $e) {
            Log::error('Delete alternative failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
