<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsabilityResponse extends Model
{
    protected $fillable = [
        'usability_instrument_id',
        'user_id',
        'role',
        'decision_session_id',
        'total_score',
    ];

    /**
     * Relasi ke instrumen usability.
     */
    public function instrument()
    {
        return $this->belongsTo(UsabilityInstrument::class, 'usability_instrument_id');
    }

    /**
     * Relasi ke user (responden).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke sesi keputusan (opsional).
     */
    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    /**
     * Relasi ke jawaban usability.
     */
    public function answers()
    {
        return $this->hasMany(UsabilityAnswer::class);
    }
}
