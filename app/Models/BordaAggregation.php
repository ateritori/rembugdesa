<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BordaAggregation extends Model
{
    protected $fillable = [
        'decision_session_id',
        'method',
        'level',
        'source',
        'alternative_id',
        'borda_score',
        'rank',
    ];

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }
}
