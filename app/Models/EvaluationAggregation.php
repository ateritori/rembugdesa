<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationAggregation extends Model
{
    protected $fillable = [
        'decision_session_id',
        'user_id',
        'alternative_id',
        'method',
        'score',
    ];

    // relasi
    public function session()
    {
        return $this->belongsTo(DecisionSession::class, 'decision_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }
}
