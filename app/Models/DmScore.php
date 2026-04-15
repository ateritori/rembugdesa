<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmScore extends Model
{
    protected $table = 'dm_scores';

    const METHOD_SAW = 'saw';
    const METHOD_SMART = 'smart';

    protected $fillable = [
        'decision_session_id',
        'dm_id',
        'alternative_id',
        'method',
        'score'
    ];

    protected $casts = [
        'score' => 'float'
    ];

    // ================= RELATION =================

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
}
