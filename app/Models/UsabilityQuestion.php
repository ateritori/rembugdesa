<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsabilityQuestion extends Model
{
    protected $fillable = [
        'usability_instrument_id',
        'number',
        'question',
        'polarity',
        'is_active',
    ];

    /**
     * Relasi ke instrumen usability.
     */
    public function instrument()
    {
        return $this->belongsTo(UsabilityInstrument::class, 'usability_instrument_id');
    }

    /**
     * Relasi ke jawaban usability.
     */
    public function answers()
    {
        return $this->hasMany(UsabilityAnswer::class);
    }
}
