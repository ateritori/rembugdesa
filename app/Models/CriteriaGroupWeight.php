<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaGroupWeight extends Model
{
    protected $fillable = [
        'decision_session_id',
        'weights',
        'cr',
    ];

    protected $casts = [
        'weights' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(DecisionSession::class, 'decision_session_id');
    }
}
