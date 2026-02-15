<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartResultDm extends Model
{
    protected $table = 'smart_results_dm';

    protected $fillable = [
        'decision_session_id',
        'dm_id',
        'alternative_id',
        'smart_score',
        'rank_dm',
    ];
}
