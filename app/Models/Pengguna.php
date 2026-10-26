<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Model
{
    protected $table = 'pengguna';

    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'kata_sandi',
        'nim_nip',
        'nomor_telepon',
    ];

    protected $hidden = [
        'kata_sandi',
    ];

    public function ratingLayanan(): HasMany
    {
        return $this->hasMany(
            RatingLayanan::class,
            'id_pengguna',
            'id'
        );
    }
}
