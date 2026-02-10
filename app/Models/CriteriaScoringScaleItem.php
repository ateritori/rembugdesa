<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaScoringScaleItem extends Model
{
    protected $table = 'criteria_scoring_scale_items';

    protected $fillable = [
        'scoring_rule_id',
        'ordinal',
        'label',
        'utility_value',
    ];

    public function scoringRule()
    {
        return $this->belongsTo(CriteriaScoringRule::class, 'scoring_rule_id');
    }
}
