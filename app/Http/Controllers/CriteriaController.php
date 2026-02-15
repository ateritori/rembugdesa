<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CriteriaController extends Controller
{
    /**
     * Menampilkan daftar kriteria dan aturan penilaiannya.
     */
    public function index(DecisionSession $decisionSession)
    {
        // Eager load kriteria untuk performa lebih baik
        $criteria = $decisionSession->criteria()
            ->orderBy('order')
            ->get();

        // Mengambil scoring rules yang terkait dengan kriteria pada sesi ini
        $scoringRules = CriteriaScoringRule::with('parameters')
            ->whereIn('criteria_id', $criteria->pluck('id'))
            ->where(function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id)
                    ->orWhereNull('decision_session_id'); // Support untuk rule global jika ada
            })
            ->get()
            ->keyBy('criteria_id');

        return view('criteria.index', compact(
            'decisionSession',
            'criteria',
            'scoringRules'
        ));
    }

    /**
     * Menyimpan kriteria baru.
     */
    public function store(Request $request, DecisionSession $decisionSession)
    {
        $this->authorizeDraft($decisionSession);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
        ]);

        try {
            return DB::transaction(function () use ($request, $decisionSession) {
                // Menggunakan count() langsung pada relasi untuk menentukan urutan
                $order = $decisionSession->criteria()->count() + 1;

                $decisionSession->criteria()->create([
                    'name'  => $request->name,
                    'type'  => $request->type,
                    'order' => $order,
                ]);

                return redirect()
                    ->route('criteria.index', $decisionSession->id)
                    ->with('success', 'Kriteria berhasil ditambahkan.');
            });
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan kriteria: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan kriteria.');
        }
    }

    /**
     * Memperbarui kriteria.
     */
    public function update(Request $request, Criteria $criteria)
    {
        $this->authorizeDraft($criteria->decisionSession);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
        ]);

        try {
            $criteria->update($request->only('name', 'type'));

            return redirect()
                ->route('criteria.index', $criteria->decision_session_id)
                ->with('success', 'Kriteria berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui kriteria: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Mengubah status aktif kriteria.
     */
    public function toggle(Criteria $criteria)
    {
        $this->authorizeDraft($criteria->decisionSession);

        try {
            $criteria->update([
                'is_active' => ! $criteria->is_active,
            ]);

            return redirect()
                ->route('criteria.index', $criteria->decision_session_id)
                ->with('success', 'Status kriteria berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal toggle kriteria: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status.');
        }
    }

    /**
     * Menghapus kriteria.
     */
    public function destroy(Criteria $criteria)
    {
        $decisionSession = $criteria->decisionSession;
        $this->authorizeDraft($decisionSession);

        try {
            DB::transaction(function () use ($criteria, $decisionSession) {
                $deletedOrder = $criteria->order;

                // Hapus kriteria
                $criteria->delete();

                // Re-order kriteria yang tersisa agar tidak ada nomor urut yang melompat
                $decisionSession->criteria()
                    ->where('order', '>', $deletedOrder)
                    ->decrement('order');
            });

            return redirect()
                ->route('criteria.index', $decisionSession->id)
                ->with('success', 'Kriteria berhasil dihapus dan urutan diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus kriteria: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kriteria.');
        }
    }

    /* ================= INTERNAL ================= */

    /**
     * Memastikan sesi masih dalam status draft sebelum diubah.
     */
    private function authorizeDraft(DecisionSession $decisionSession): void
    {
        if ($decisionSession->status !== 'draft') {
            abort(403, 'Sesi sudah aktif. Perubahan kriteria tidak diperbolehkan.');
        }
    }
}
