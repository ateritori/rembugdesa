<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alternative extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'decision_session_id',
        'code',
        'name',
        'order',
        'is_active',
    ];

    /* ================= RELATIONS ================= */

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }
}
