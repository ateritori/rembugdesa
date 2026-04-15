<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Criteria;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CriteriaController extends Controller
{
    /**
     * Menampilkan daftar kriteria dan aturan penilaian.
     */
    public function index(DecisionSession $decisionSession)
    {
        $criteria = $decisionSession->criteria()
            ->orderBy('order')
            ->get()
            ->map(function ($c) use ($decisionSession) {
                $c->ui_locked = ($decisionSession->status !== 'draft') || !$c->is_active;
                return $c;
            });

        $scoringRules = CriteriaScoringRule::with('parameters')
            ->whereIn('criteria_id', $criteria->pluck('id'))
            ->where(function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id)
                    ->orWhereNull('decision_session_id');
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
            'level' => 'required|integer|min:1|max:5',
            'evaluator_type' => 'required|in:system,human',
        ]);

        try {
            $order = $decisionSession->criteria()->count() + 1;

            $decisionSession->criteria()->create([
                'name'  => $request->name,
                'level' => $request->level,
                'type'  => $request->type,
                'evaluator_type' => $request->evaluator_type,
                'order' => $order,
            ]);

            return redirect()
                ->route('criteria.index', $decisionSession->id)
                ->with('success', 'Kriteria berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Store criteria failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan kriteria.');
        }
    }

    /**
     * Memperbarui detail kriteria.
     */
    public function update(Request $request, Criteria $criteria)
    {
        $this->authorizeDraft($criteria->decisionSession);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'level' => 'required|integer|min:1|max:5',
            'evaluator_type' => 'required|in:system,human',
        ]);

        try {
            $criteria->update($request->only('name', 'type', 'level', 'evaluator_type'));

            return redirect()
                ->route('criteria.index', $criteria->decision_session_id)
                ->with('success', 'Kriteria berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update criteria failed: ' . $e->getMessage());
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
                'is_active' => !$criteria->is_active,
            ]);

            return redirect()
                ->route('criteria.index', $criteria->decision_session_id)
                ->with('success', 'Status kriteria diperbarui.');
        } catch (\Exception $e) {
            Log::error('Toggle criteria failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status.');
        }
    }

    /**
     * Menghapus kriteria dan mengatur ulang urutan.
     */
    public function destroy(Criteria $criteria)
    {
        $decisionSession = $criteria->decisionSession;
        $this->authorizeDraft($decisionSession);

        try {
            // Menggunakan koneksi model untuk transaksi database
            return $decisionSession->getConnection()->transaction(function () use ($criteria, $decisionSession) {
                $deletedOrder = $criteria->order;

                $criteria->delete();

                // Re-order menggunakan Eloquent builder
                $decisionSession->criteria()
                    ->where('order', '>', $deletedOrder)
                    ->decrement('order');

                return redirect()
                    ->route('criteria.index', $decisionSession->id)
                    ->with('success', 'Kriteria dihapus dan urutan diperbarui.');
            });
        } catch (\Exception $e) {
            Log::error('Delete criteria failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kriteria.');
        }
    }

    /**
     * Otorisasi status draft.
     */
    private function authorizeDraft(DecisionSession $decisionSession): void
    {
        if ($decisionSession->status !== 'draft') {
            abort(403, 'Sesi sudah aktif. Perubahan tidak diperbolehkan.');
        }
    }
}
