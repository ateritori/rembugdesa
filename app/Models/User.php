<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\DecisionSession;
use App\Models\CriteriaWeight;
use App\Models\AlternativeEvaluation;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function decisionSessions()
    {
        return $this->belongsToMany(
            DecisionSession::class,
            'decision_session_dm'
        )->withTimestamps();
    }

    public function criteriaPairwise()
    {
        return $this->hasMany(CriteriaPairwise::class, 'dm_id');
    }

    /**
     * Relasi ke bobot kriteria (hasil agregasi / input DM).
     */
    public function criteriaWeights()
    {
        return $this->hasMany(CriteriaWeight::class, 'dm_id');
    }

    /**
     * Relasi ke hasil penilaian alternatif yang dilakukan oleh User ini (sebagai DM).
     */
    public function alternativeEvaluations()
    {
        return $this->hasMany(AlternativeEvaluation::class, 'dm_id');
    }
}
