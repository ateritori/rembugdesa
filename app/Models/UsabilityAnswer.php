<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsabilityAnswer extends Model
{
    protected $fillable = [
        'usability_response_id',
        'usability_question_id',
        'value',
    ];

    /**
     * Relasi ke response usability.
     */
    public function response()
    {
        return $this->belongsTo(UsabilityResponse::class, 'usability_response_id');
    }

    /**
     * Relasi ke pertanyaan usability.
     */
    public function question()
    {
        return $this->belongsTo(UsabilityQuestion::class, 'usability_question_id');
    }
}
