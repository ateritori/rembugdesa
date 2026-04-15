<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    protected $fillable = [
        'decision_session_id',
        'user_id',
        'alternative_id',
        'criteria_id',
        'method',
        'evaluation_score',
        'weighted_score',
    ];

    protected $casts = [
        'evaluation_score' => 'float',
        'weighted_score' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(DecisionSession::class, 'decision_session_id');
    }

    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }
}
