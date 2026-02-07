<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaWeight extends Model
{
    protected $fillable = [
        'decision_session_id',
        'dm_id',
        'weights',
        'cr',
    ];

    protected $casts = [
        'weights' => 'array',
        'cr' => 'float',
    ];

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'dm_id');
    }
}
