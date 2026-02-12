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
