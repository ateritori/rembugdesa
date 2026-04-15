<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecisionSessionAssignment extends Model
{
    protected $fillable = [
        'decision_session_id',
        'user_id',
        'criteria_id',
        'can_pairwise',
        'can_evaluate',
    ];

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }
}
