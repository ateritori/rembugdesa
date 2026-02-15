<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\CriteriaScoringRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CriteriaScoringRuleController extends Controller
{
    private function authorizeDraft(Criteria $criteria): void
    {
        abort_if(
            $criteria->decisionSession->status !== 'draft',
            403,
            'Sesi sudah dikunci. Aturan penilaian tidak dapat diubah.'
        );
    }

    public function store(Request $request, Criteria $criteria)
    {
        $this->authorizeDraft($criteria);
        $validated = $this->validateRequest($request);

        try {
            DB::transaction(function () use ($validated, $criteria) {
                $rule = CriteriaScoringRule::create([
                    'criteria_id'         => $criteria->id,
                    'decision_session_id' => $criteria->decision_session_id,
                    'input_type'          => $validated['input_type'],
                    'preference_type'     => $validated['preference_type'],
                ]);

                $this->saveParameters($rule, $validated);
            });

            return back()->with('success', 'Aturan penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Scoring Rule: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Criteria $criteria, CriteriaScoringRule $rule)
    {
        $this->authorizeDraft($criteria);
        abort_if($rule->criteria_id !== $criteria->id, 404);

        $validated = $this->validateRequest($request);

        try {
            DB::transaction(function () use ($validated, $rule) {
                $rule->update([
                    'input_type'      => $validated['input_type'],
                    'preference_type' => $validated['preference_type'],
                ]);

                // Bersihkan parameter lama
                $rule->parameters()->delete();

                $this->saveParameters($rule, $validated);
            });

            return back()->with('success', 'Aturan penilaian berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Scoring Rule: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'input_type'      => 'required|in:scale,numeric',
            'preference_type' => 'required|in:linear,concave,convex',
            'scale_min'       => 'required_if:input_type,scale|nullable|numeric',
            'scale_max'       => 'required_if:input_type,scale|nullable|numeric',
            'semantics'       => 'nullable|array',
            'utilities'       => 'nullable|array',
        ]);
    }

    /**
     * PERBAIKAN UTAMA: Memastikan integer key pada array tetap terjaga
     */
    private function saveParameters(CriteriaScoringRule $rule, array $validated): void
    {
        if ($validated['input_type'] === 'scale') {
            $semantics = $validated['semantics'] ?? [];
            $utilities = $validated['utilities'] ?? [];

            // Urutkan agar urutan 1, 2, 3... konsisten
            ksort($semantics);
            ksort($utilities);

            $params = [
                'scale_range' => [
                    'min' => (int) ($validated['scale_min'] ?? 1),
                    'max' => (int) ($validated['scale_max'] ?? 5),
                ],
                'scale_semantics' => $semantics,
                'scale_utilities' => $utilities,
            ];

            foreach ($params as $key => $value) {
                // PERBAIKAN: Langsung kirim $value (array),
                // jangan di-json_encode manual di sini.
                $rule->parameters()->create([
                    'param_key'   => $key,
                    'param_value' => $value,
                ]);
            }
        }
    }
}
