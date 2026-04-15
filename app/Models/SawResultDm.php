<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SawResultDm extends Model
{
    protected $table = 'saw_result_dm';
    protected $fillable = [
        'decision_session_id',
        'dm_id',
        'alternative_id',
        'saw_score',
        'rank_dm',
    ];
}
