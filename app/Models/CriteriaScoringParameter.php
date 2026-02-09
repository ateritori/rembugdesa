<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaScoringParameter extends Model
{
    protected $table = 'criteria_scoring_parameters';

    protected $fillable = [
        'scoring_rule_id',
        'param_key',
        'param_value',
    ];

    protected $casts = [
        'param_value' => 'array',
    ];

    public function scoringRule()
    {
        return $this->belongsTo(
            CriteriaScoringRule::class,
            'scoring_rule_id'
        );
    }
}
