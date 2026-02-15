<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BordaResult extends Model
{
    protected $table = 'borda_results';

    protected $fillable = [
        'decision_session_id',
        'alternative_id',
        'borda_score',
        'final_rank',
    ];

    public function alternative()
    {
        return $this->belongsTo(
            Alternative::class,
            'alternative_id'
        );
    }
}
