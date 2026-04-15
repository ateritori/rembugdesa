<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    protected $fillable = [
        'decision_session_id',
        'user_id',
        'criteria_id',
        'alternative_id',
        'value',
        'source',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(DecisionSession::class, 'decision_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }
}
