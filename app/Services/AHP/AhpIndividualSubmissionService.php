<?php

namespace App\Http\Controllers;

use App\Models\DecisionSession;
use App\Models\CriteriaPairwise; // Tambahkan ini
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AHP\AhpIndividualSubmissionService;

class AhpPairwiseController extends Controller
{
    public function index(DecisionSession $decisionSession)
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('dm'), 403);

        // 1. Ambil kriteria aktif
        $criterias = $decisionSession->criteria() // Sesuaikan dengan nama relasi (criteria/criterias)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // 2. AMBIL DARI CriteriaPairwise (Detail slider), BUKAN CriteriaWeight
        $existingPairwise = CriteriaPairwise::where('decision_session_id', $decisionSession->id)
            ->where('dm_id', $user->id)
            ->get()
            ->mapWithKeys(function ($item) {
                // Key harus sama dengan logic di Blade: min-max
                $key = min($item->criteria_id_1, $item->criteria_id_2) . '-' .
                    max($item->criteria_id_1, $item->criteria_id_2);
                return [$key => $item];
            });

        // 3. Hitung kelengkapan
        $criteriaCount = $criterias->count();
        $requiredPairs = $criteriaCount > 1 ? ($criteriaCount * ($criteriaCount - 1)) / 2 : 0;
        $hasCompletedPairwise = $requiredPairs > 0 && $existingPairwise->count() >= $requiredPairs;

        // 4. LOGIKA EDIT: Izinkan edit selama status 'configured' 
        // Meskipun sudah lengkap ($hasCompletedPairwise), DM tetap boleh ubah (edit)
        $pairwiseReadOnly = $decisionSession->status !== 'configured';

        return view('dms.index', [
            'decisionSession'      => $decisionSession,
            'criterias'            => $criterias,
            'existingPairwise'     => $existingPairwise,
            'hasCompletedPairwise' => $hasCompletedPairwise,
            'pairwiseReadOnly'     => $pairwiseReadOnly,
            'activeTab'            => 'pairwise',
        ]);
    }

    public function store(
        Request $request,
        DecisionSession $decisionSession,
        AhpIndividualSubmissionService $service
    ) {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('dm'), 403);
        abort_if($decisionSession->status !== 'configured', 403);

        $frontendPairs = json_decode($request->input('debug_frontend'), true);

        if (! is_array($frontendPairs)) {
            return back()->withInput()->with('error', 'Data perbandingan tidak valid.');
        }

        try {
            // Service akan otomatis menghapus data lama (Delete & Re-insert)
            $result = $service->submit(
                $decisionSession,
                $user,
                $frontendPairs,
                $request->input('pairwise', [])
            );

            return redirect()
                ->route('dms.weights.index', $decisionSession->id)
                ->with('success', 'Penilaian berhasil diperbarui. CR = ' . round($result['cr'], 4));
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
