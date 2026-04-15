<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRanking extends Model
{
    protected $fillable = [
        'decision_session_id',
        'alternative_id',
        'smart_score',
        'rank_system',
    ];
}
