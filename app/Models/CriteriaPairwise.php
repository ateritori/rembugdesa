<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaPairwise extends Model
{
    protected $table = 'criteria_pairwise';

    protected $fillable = [
        'decision_session_id',
        'dm_id',
        'criteria_id_1',
        'criteria_id_2',
        'value',
        'direction',
    ];

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'dm_id');
    }

    public function criteriaFirst()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id_1');
    }

    public function criteriaSecond()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id_2');
    }
}
