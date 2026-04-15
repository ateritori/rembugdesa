<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EvaluationAggregation;
use App\Models\BordaAggregation;

class Alternative extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'decision_session_id',
        'code',
        'name',
        'order',
        'is_active',
        'rab',
        'coverage',
        'beneficiaries',
        'criteria_id',
    ];

    /* ================= RELATIONS ================= */

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function evaluationResults()
    {
        return $this->hasMany(EvaluationResult::class);
    }

    public function evaluationAggregations()
    {
        return $this->hasMany(EvaluationAggregation::class);
    }

    public function bordaAggregations()
    {
        return $this->hasMany(BordaAggregation::class);
    }
}
