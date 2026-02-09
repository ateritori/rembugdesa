<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlternativeEvaluation extends Model
{
    protected $fillable = [
        'decision_session_id',
        'dm_id',
        'alternative_id',
        'criteria_id',
        'raw_value',
        'utility_value',
    ];

    /* ================= RELATIONS ================= */

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function dm()
    {
        return $this->belongsTo(User::class, 'dm_id');
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
