<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecisionResult extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'decision_results';

    // Source method constants
    const SOURCE_SAW = 'SAW';
    const SOURCE_SMART = 'SMART';
    const SOURCE_BORDA = 'BORDA';

    // Aggregation method constants
    const AGGREGATION_DIRECT = 'DIRECT';
    const AGGREGATION_FLAT = 'FLAT';

    // Pipeline constants
    const PIPELINE_SAW = 'SAW';
    const PIPELINE_SMART = 'SMART';
    const PIPELINE_SMART_BORDA = 'SMART+Borda';
    const PIPELINE_SAW_BORDA = 'SAW+Borda';
    protected $fillable = [
        'signature',
        'decision_session_id',
        'alternative_id',
        'source_method',
        'aggregation_method',
        'pipeline',
        'score',
        'rank',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class, 'decision_session_id');
    }

    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }
}
