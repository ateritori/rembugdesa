<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criteria extends Model
{
    use SoftDeletes;
    protected $table = 'criteria';

    protected $fillable = [
        'decision_session_id',
        'name',
        'type',
        'is_active',
        'order',
    ];

    /* ================= RELATIONS ================= */

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function pairwiseAsFirst()
    {
        return $this->hasMany(CriteriaPairwise::class, 'criteria_id_1');
    }

    public function pairwiseAsSecond()
    {
        return $this->hasMany(CriteriaPairwise::class, 'criteria_id_2');
    }

    public function weight()
    {
        return $this->hasOne(CriteriaWeight::class);
    }

    public function scoringRule()
    {
        return $this->hasOne(CriteriaScoringRule::class, 'criteria_id');
    }
}
