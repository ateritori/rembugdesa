<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'alamat',
        'dusun',
        'rt',
        'rw',
        'unsur',
        'jabatan',
        'instansi',
        'kategori_dm',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
