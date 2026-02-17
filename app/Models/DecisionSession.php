<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Alternative;

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
     * HARUS sama persis dengan ENUM di migration.
     */
    public const STATUSES = [
        'draft',
        'configured',
        'scoring',
        'closed',
    ];

    /**
     * Urutan lifecycle resmi Decision Session.
     * Digunakan untuk validasi rollback-only oleh superadmin.
     */
    public const STATUS_ORDER = [
        'draft',
        'configured',
        'scoring',
        'closed',
    ];

    /* ================= RELATIONS ================= */

    public function criteria()
    {
        return $this->hasMany(Criteria::class);
    }

    public function criterias()
    {
        return $this->criteria();
    }

    public function alternatives()
    {
        return $this->hasMany(Alternative::class)
            ->orderBy('order');
    }

    public function criteriaPairwise()
    {
        return $this->hasMany(CriteriaPairwise::class);
    }

    public function criteriaWeights()
    {
        return $this->hasMany(CriteriaWeight::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dms()
    {
        return $this->belongsToMany(
            User::class,
            'decision_session_dm',
            'decision_session_id',
            'user_id'
        );
    }
}
