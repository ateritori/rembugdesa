<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\User;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DecisionSessionController extends Controller
{
    public function index()
    {
        $sessions = DecisionSession::latest()->get();
        return view('decision-sessions.index', compact('sessions'));
    }

    public function show(DecisionSession $decisionSession)
    {
        // 1. Ambil data Kriteria
        $criteria = $decisionSession->criteria()->orderBy('order')->get();
        $activeCriteriaCount = $criteria->where('is_active', true)->count();

        // 2. Ambil data Alternatif
        $alternatives = $decisionSession->alternatives()->get();
        $activeAlternativesCount = $alternatives->where('is_active', true)->count();

        // 3. Ambil data Decision Maker (DM)
        // Seluruh user dengan role DM (untuk pilihan di tab DM)
        $dms = User::role('dm')->get();

        // Monitoring DM yang ditugaskan (dengan status pengisian)
        $assignedDms = $decisionSession->dms()->get()->map(function ($dm) use ($decisionSession) {
            $dm->has_submitted = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', $dm->id)
                ->exists();
            return $dm;
        });

        $assignedDmIds = $assignedDms->pluck('id')->toArray();
        $assignedDmCount = $assignedDms->count();

        // 4. Data Pairwise (untuk DM yang login)
        $existingPairwise = collect();
        if (auth()->check() && auth()->user()->hasRole('dm')) {
            $existingPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
                ->where('dm_id', auth()->id())
                ->get()
                ->keyBy(fn($p) => $p->criteria_id_1 . '-' . $p->criteria_id_2);
        }

        return view('decision-sessions.workspace', [
            'decisionSession'         => $decisionSession,
            'criteria'                => $criteria,
            'criterias'               => $criteria, // Alias untuk sinkronisasi kode blade Anda
            'activeCriteriaCount'     => $activeCriteriaCount,
            'alternatives'            => $alternatives,
            'activeAlternativesCount' => $activeAlternativesCount,
            'dms'                     => $dms,
            'assignedDms'             => $assignedDms, // Variabel utama monitoring
            'assignedDmIds'           => $assignedDmIds,
            'assignedDmCount'         => $assignedDmCount,
            'existingPairwise'        => $existingPairwise,
        ]);
    }

    public function create()
    {
        return view('decision-sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
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
            ->with('success', 'Decision session created');
    }

    public function edit(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);
        return view('decision-sessions.edit', compact('decisionSession'));
    }

    public function update(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

        $request->validate([
            'name' => 'required|string',
            'year' => 'required|digits:4',
        ]);

        $decisionSession->update([
            'name' => $request->name,
            'year' => $request->year,
        ]);

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session updated');
    }

    public function activate(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);

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
        abort_if($decisionSession->status !== 'active', 403);
        $decisionSession->update(['status' => 'closed']);
        return back()->with('success', 'Sesi berhasil ditutup.');
    }

    public function destroy(DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status !== 'draft', 403);
        $decisionSession->delete();

        return redirect()
            ->route('decision-sessions.index')
            ->with('success', 'Decision session deleted');
    }

    public function assignDms(Request $request, DecisionSession $decisionSession)
    {
        if ($decisionSession->status !== 'draft') {
            return back()->withErrors('Sesi sudah aktif.');
        }

        $dmIds = $request->input('dm_ids', []);

        $validDmIds = User::role('dm')
            ->whereIn('id', $dmIds)
            ->pluck('id')
            ->toArray();

        $decisionSession->dms()->sync($validDmIds);

        return redirect()
            ->to(route('decision-sessions.show', $decisionSession->id) . '?tab=dm')
            ->with('success', 'Assignment Decision Maker diperbarui.');
    }


    public function storePairwise(Request $request, DecisionSession $decisionSession)
    {
        $user = auth()->user();

        abort_if(! $user->hasRole('dm'), 403);
        abort_if($decisionSession->status !== 'active', 403);

        $frontendPairs = json_decode($request->input('debug_frontend'), true);

        if (! is_array($frontendPairs)) {
            return back()->withErrors('Data frontend tidak valid.');
        }

        $criterias = $decisionSession->criteria()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // --- PERBAIKAN MATRIKS MULAI DI SINI ---
        $ids = $criterias->pluck('id')->toArray();
        $idToIndex = array_flip($ids); // Petakan ID Database ke urutan 0, 1, 2...
        $n = count($ids);

        // Bangun matriks dengan index numerik murni agar terbaca oleh Service
        $matrix = array_fill(0, $n, array_fill(0, $n, 1.0));

        foreach ($frontendPairs as $key => $pair) {
            [$id1, $id2] = array_map('intval', explode('-', $key));

            if (isset($idToIndex[$id1]) && isset($idToIndex[$id2])) {
                $idx1 = $idToIndex[$id1];
                $idx2 = $idToIndex[$id2];

                $matrix[$idx1][$idx2] = (float) $pair['a_ij'];
                $matrix[$idx2][$idx1] = (float) $pair['a_ji'];
            }
        }
        // --- PERBAIKAN MATRIKS SELESAI ---

        // 2. Hitung AHP
        $ahp = app(\App\Services\AHP\AhpService::class)->calculate($matrix);

        // Validasi CR
        if (! isset($ahp['cr']) || $ahp['cr'] >= 0.10) {
            return back()
                ->withInput()
                ->withErrors([
                    'cr' => 'Consistency Ratio (CR) = ' .
                        round($ahp['cr'] ?? 0, 4) .
                        '. Data hanya dapat disimpan jika CR < 0.10.'
                ]);
        }

        // 3. Reset data lama
        CriteriaPairwise::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->delete();

        CriteriaWeight::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->delete();

        // 4. Simpan pairwise (Disesuaikan dengan kolom 'value' dan 'direction')
        $pairwiseInput = $request->input('pairwise', []);
        $processed = [];

        foreach ($pairwiseInput as $c1 => $rows) {
            foreach ($rows as $c2 => $data) {
                $id1 = (int) $c1;
                $id2 = (int) $c2;

                if ($id1 === $id2) continue;

                // Pastikan kita hanya memproses satu pasang satu kali
                $key = min($id1, $id2) . '-' . max($id1, $id2);
                if (isset($processed[$key])) continue;
                $processed[$key] = true;

                // Tentukan direction dan value murni (1-9) untuk disimpan
                // Jika a_ij >= 1, berarti arah ke kiri (kriteria 1 lebih penting)
                $valIJ = (float) $data['a_ij'];
                $direction = ($valIJ >= 1) ? 'left' : 'right';
                $saveValue = ($valIJ >= 1) ? $valIJ : (1 / $valIJ);

                \App\Models\CriteriaPairwise::create([
                    'decision_session_id' => $decisionSession->id,
                    'dm_id'               => $user->id,
                    'criteria_id_1'       => min($id1, $id2),
                    'criteria_id_2'       => max($id1, $id2),
                    'value'               => $saveValue, // Kolom yang diminta error tadi
                    'direction'           => $direction, // Kolom pendukung di Blade
                ]);
            }
        }

        // 5. Simpan bobot (Mapping index kembali ke ID kriteria agar datanya benar)
        $weightedResult = [];
        foreach ($ids as $index => $id) {
            $weightedResult[$id] = $ahp['weights'][$index];
        }

        CriteriaWeight::create([
            'decision_session_id' => $decisionSession->id,
            'dm_id'               => $user->id,
            'weights'             => $weightedResult,
            'cr'                  => $ahp['cr'],
        ]);

        return redirect()
            ->route('dashboard')
            ->with('tab', 'pairwise')
            ->with('success', 'Penilaian berhasil disimpan. CR = ' . round($ahp['cr'], 4));
    }
}
