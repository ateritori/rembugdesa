<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\CriteriaScoringRule;
use App\Models\CriteriaScoringParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $request->validate([
            'input_type'      => 'required|in:scale,numeric',
            'preference_type' => 'required|in:linear,concave,convex',
            'scale_min'       => 'required_if:input_type,scale|numeric',
            'scale_max'       => 'required_if:input_type,scale|numeric|gte:scale_min',
            'semantics'       => 'nullable|array',
            'utilities'       => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $criteria) {

            /* ================= RULE UTAMA ================= */
            $rule = CriteriaScoringRule::create(
                [
                    'criteria_id'        => $criteria->id,
                    'decision_session_id' => $criteria->decision_session_id,
                    'input_type'          => $request->input('input_type'),
                    'preference_type'     => $request->input('preference_type'),
                ]
            );

            /* ================= BERSIHKAN PARAMETER LAMA ================= */
            CriteriaScoringParameter::where('scoring_rule_id', $rule->id)->delete();

            /* ================= PARAMETER SKALA ================= */
            if ($request->input('input_type') === 'scale') {

                // 1. Range Skala
                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_range',
                    'param_value'     => [
                        'min' => (int) $request->scale_min,
                        'max' => (int) $request->scale_max,
                    ],
                ]);

                // 2. Semantik Skala
                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_semantics',
                    'param_value'     => $request->semantics ?? [],
                ]);

                // 3. Utilitas (hanya jika non-linear)
                if ($request->preference_type !== 'linear') {
                    CriteriaScoringParameter::create([
                        'scoring_rule_id' => $rule->id,
                        'param_key'       => 'scale_utilities',
                        'param_value'     => $request->utilities ?? [],
                    ]);
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Aturan penilaian kriteria "' . $criteria->name . '" berhasil disimpan.');
    }

    public function update(Request $request, Criteria $criteria, CriteriaScoringRule $rule)
    {
        $this->authorizeDraft($criteria);

        abort_if(
            $rule->criteria_id !== $criteria->id,
            404
        );

        $request->validate([
            'input_type'      => 'required|in:scale,numeric',
            'preference_type' => 'required|in:linear,concave,convex',
            'scale_min'       => 'required_if:input_type,scale|numeric',
            'scale_max'       => 'required_if:input_type,scale|numeric|gte:scale_min',
            'semantics'       => 'nullable|array',
            'utilities'       => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $criteria, $rule) {

            /* ================= UPDATE RULE ================= */
            $rule->update([
                'input_type'      => $request->input('input_type'),
                'preference_type' => $request->input('preference_type'),
            ]);

            /* ================= RESET PARAMETER ================= */
            CriteriaScoringParameter::where('scoring_rule_id', $rule->id)->delete();

            if ($request->input('input_type') === 'scale') {

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_range',
                    'param_value'     => [
                        'min' => (int) $request->scale_min,
                        'max' => (int) $request->scale_max,
                    ],
                ]);

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_semantics',
                    'param_value'     => $request->semantics ?? [],
                ]);

                if ($request->preference_type !== 'linear') {
                    CriteriaScoringParameter::create([
                        'scoring_rule_id' => $rule->id,
                        'param_key'       => 'scale_utilities',
                        'param_value'     => $request->utilities ?? [],
                    ]);
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Aturan penilaian kriteria "' . $criteria->name . '" berhasil diperbarui.');
    }
}
