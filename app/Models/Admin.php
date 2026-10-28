<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Unit;

class Admin extends Authenticatable
{
    protected $table = 'admin';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nama_lengkap_gelar',
        'email',
        'kata_sandi',
        'nip',
        'peran',
        'id_unit',
        'nomor_telepon',
        'avatar',
    ];

    protected $hidden = [
        'kata_sandi',
    ];

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(
            Unit::class,
            'id_unit',
            'id'
        );
    }
}