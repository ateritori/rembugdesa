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
            'Sesi sudah dikunci.'
        );
    }

    public function store(Request $request, Criteria $criteria)
    {
        $this->authorizeDraft($criteria);

        $validated = $request->validate([
            'input_type'      => 'required|in:scale,numeric',
            'preference_type' => 'required|in:linear,concave,convex',
            'scale_min'       => 'required_if:input_type,scale|numeric',
            'scale_max'       => 'required_if:input_type,scale|numeric|gte:scale_min',
            'semantics'       => 'nullable|array',
            'utilities'       => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $criteria) {

            $rule = CriteriaScoringRule::create([
                'criteria_id'         => $criteria->id,
                'decision_session_id' => $criteria->decision_session_id,
                'input_type'          => $validated['input_type'],
                'preference_type'     => $validated['preference_type'],
            ]);

            CriteriaScoringParameter::where('scoring_rule_id', $rule->id)->delete();

            if ($validated['input_type'] === 'scale') {

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_range',
                    'param_value'     => [
                        'min' => (int) $validated['scale_min'],
                        'max' => (int) $validated['scale_max'],
                    ],
                ]);

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_semantics',
                    'param_value'     => $validated['semantics'] ?? [],
                ]);

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_utilities',
                    'param_value'     => $validated['utilities'] ?? [],
                ]);
            }
        });

        return back()->with('success', 'Aturan penilaian berhasil disimpan.');
    }

    public function update(Request $request, Criteria $criteria, CriteriaScoringRule $rule)
    {
        $this->authorizeDraft($criteria);

        abort_if($rule->criteria_id !== $criteria->id, 404);

        $validated = $request->validate([
            'input_type'      => 'required|in:scale,numeric',
            'preference_type' => 'required|in:linear,concave,convex',
            'scale_min'       => 'required_if:input_type,scale|numeric',
            'scale_max'       => 'required_if:input_type,scale|numeric|gte:scale_min',
            'semantics'       => 'nullable|array',
            'utilities'       => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $rule) {

            $rule->update([
                'input_type'      => $validated['input_type'],
                'preference_type' => $validated['preference_type'],
            ]);

            CriteriaScoringParameter::where('scoring_rule_id', $rule->id)->delete();

            if ($validated['input_type'] === 'scale') {

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_range',
                    'param_value'     => [
                        'min' => (int) $validated['scale_min'],
                        'max' => (int) $validated['scale_max'],
                    ],
                ]);

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_semantics',
                    'param_value'     => $validated['semantics'] ?? [],
                ]);

                CriteriaScoringParameter::create([
                    'scoring_rule_id' => $rule->id,
                    'param_key'       => 'scale_utilities',
                    'param_value'     => $validated['utilities'] ?? [],
                ]);
            }
        });

        return back()->with('success', 'Aturan penilaian berhasil diperbarui.');
    }
}
