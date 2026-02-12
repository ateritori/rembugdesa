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

    public function scaleItems()
    {
        return $this->hasMany(CriteriaScoringScaleItem::class, 'scoring_rule_id')
            ->orderBy('ordinal');
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

    /**
     * Menentukan apakah aturan scoring kriteria sudah lengkap dan siap digunakan.
     */
    public function isComplete(): bool
    {
        // input dasar wajib ada
        if (!$this->input_type || !$this->preference_type) {
            return false;
        }

        // parameter wajib
        $range     = $this->getParameter('scale_range');
        $utilities = $this->getParameter('scale_utilities');
        $semantics = $this->getParameter('scale_semantics');

        if (empty($range) || empty($utilities) || empty($semantics)) {
            return false;
        }

        // validasi minimal isi range
        if (
            !isset($range['min'], $range['max']) ||
            $range['min'] >= $range['max']
        ) {
            return false;
        }

        // utilities dan semantics harus seimbang
        if (count($utilities) !== count($semantics)) {
            return false;
        }

        return true;
    }
}
