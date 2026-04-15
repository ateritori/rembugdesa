<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// 🔹 Import model relasi (biar eksplisit & aman)
use App\Models\User;
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\CriteriaPairwise;
use App\Models\CriteriaWeight;
use App\Models\CriteriaGroupWeight;
use App\Models\CriteriaScoringRule;
use App\Models\DecisionSessionAssignment;
use App\Models\AlternativeEvaluation;
use App\Models\DmScore;
use App\Models\EvaluationScore;
use App\Models\EvaluationResult;
use App\Models\EvaluationAggregation;
use App\Models\BordaAggregation;

class DecisionSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'year',
        'status',
        'created_by',
    ];

    /**
     * SINGLE SOURCE OF TRUTH untuk status decision_sessions.
     */
    public const STATUSES = [
        'draft',
        'configured',
        'scoring',
        'closed',
    ];

    /**
     * Urutan lifecycle resmi Decision Session.
     */
    public const STATUS_ORDER = [
        'draft',
        'configured',
        'scoring',
        'closed',
    ];

    /* ================= RELATIONS ================= */

    // 🔹 Kriteria
    public function criteria(): HasMany
    {
        return $this->hasMany(Criteria::class);
    }

    // 🔹 Alternatif
    public function alternatives(): HasMany
    {
        return $this->hasMany(Alternative::class)
            ->orderBy('order');
    }

    // 🔹 Pairwise (AHP)
    public function criteriaPairwise(): HasMany
    {
        return $this->hasMany(CriteriaPairwise::class, 'decision_session_id');
    }

    // 🔹 Bobot (JSON hasil AHP)
    public function criteriaWeights(): HasMany
    {
        return $this->hasMany(CriteriaWeight::class, 'decision_session_id');
    }

    // 🔹 Creator
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔹 Assignment DM
    public function assignments(): HasMany
    {
        return $this->hasMany(DecisionSessionAssignment::class);
    }

    public function decisionMakers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'decision_session_assignments',
            'decision_session_id',
            'user_id'
        )->distinct();
    }

    // Alias
    public function dms(): BelongsToMany
    {
        return $this->decisionMakers();
    }

    // 🔹 Evaluasi manual (optional legacy)
    public function evaluations(): HasMany
    {
        return $this->hasMany(AlternativeEvaluation::class, 'decision_session_id');
    }

    // 🔹 Skor DM
    public function dmScores(): HasMany
    {
        return $this->hasMany(DmScore::class, 'decision_session_id');
    }

    // 🔹 RAW VALUE (INI PENTING 🔥)
    public function evaluationScores(): HasMany
    {
        return $this->hasMany(EvaluationScore::class, 'decision_session_id');
    }

    // 🔹 Group weight (kalau ada)
    public function groupWeight(): HasOne
    {
        return $this->hasOne(CriteriaGroupWeight::class, 'decision_session_id');
    }

    // 🔹 HASIL PERHITUNGAN (SMART / SAW)
    public function evaluationResults(): HasMany
    {
        return $this->hasMany(EvaluationResult::class, 'decision_session_id');
    }

    // 🔹 HASIL AGREGASI (FINAL PER DM & SYSTEM)
    public function evaluationAggregations(): HasMany
    {
        return $this->hasMany(EvaluationAggregation::class, 'decision_session_id');
    }

    // 🔹 RULE KONVERSI (utility function)
    public function criteriaScoringRules(): HasMany
    {
        return $this->hasMany(CriteriaScoringRule::class, 'decision_session_id');
    }

    // 🔹 HASIL AGREGASI BORDA (GROUP, SYSTEM, FINAL)
    public function bordaAggregations(): HasMany
    {
        return $this->hasMany(BordaAggregation::class, 'decision_session_id');
    }
}
