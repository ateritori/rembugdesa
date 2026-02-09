<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaScoringRule extends Model
{
    protected $fillable = [
        'decision_session_id',
        'criteria_id',
        'input_type',
        'preference_type',
    ];

    /* ================= RELATIONS ================= */

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    public function parameters()
    {
        // Tetap gunakan scoring_rule_id karena sesuai struktur tabelmu
        return $this->hasMany(CriteriaScoringParameter::class, 'scoring_rule_id');
    }

    /* ================= HELPERS ================= */

    /**
     * Helper untuk mengambil nilai parameter berdasarkan key-nya.
     */
    public function getParameter(string $key, $default = null)
    {
        // Menggunakan firstWhere pada collection parameters yang sudah di-load
        $param = $this->parameters->firstWhere('param_key', $key);

        return $param ? $param->param_value : $default;
    }
}
