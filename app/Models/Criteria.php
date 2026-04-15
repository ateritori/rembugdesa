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
        'level', // level kriteria: sector atau parameter
        'type',  // tipe atribut: benefit atau cost
        'is_active',
        'evaluator_type', // system | human
        'order',
    ];

    /* ================= RELATIONS ================= */

    // Relasi ke sesi keputusan
    public function decisionSession()
    {
        return $this->belongsTo(DecisionSession::class);
    }

    // Relasi pairwise sebagai kriteria pertama
    public function pairwiseAsFirst()
    {
        return $this->hasMany(CriteriaPairwise::class, 'criteria_id_1');
    }

    // Relasi pairwise sebagai kriteria kedua
    public function pairwiseAsSecond()
    {
        return $this->hasMany(CriteriaPairwise::class, 'criteria_id_2');
    }


    // Relasi aturan penilaian (SMART)
    public function scoringRule()
    {
        return $this->hasOne(CriteriaScoringRule::class, 'criteria_id');
    }

    // Relasi ke hasil evaluasi
    public function evaluationResults()
    {
        return $this->hasMany(EvaluationResult::class);
    }
}
