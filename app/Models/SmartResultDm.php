<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alternative;
use App\Models\User;

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

    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }

    public function dm()
    {
        return $this->belongsTo(User::class, 'dm_id');
    }
}
