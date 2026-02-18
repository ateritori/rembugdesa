<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsabilityInstrument extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Relasi ke pertanyaan usability (SUS items).
     */
    public function questions()
    {
        return $this->hasMany(UsabilityQuestion::class)
            ->orderBy('number');
    }

    /**
     * Relasi ke response usability.
     */
    public function responses()
    {
        return $this->hasMany(UsabilityResponse::class);
    }
}
