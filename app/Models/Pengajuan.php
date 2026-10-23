<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_tiket',
        'id_pengguna',
        'id_layanan',
        'id_unit',
        'id_admin_fo',
        'subjek',
        'deskripsi',
        'status',
        'tanggal_selesai',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function disposisi(): HasMany
    {
        return $this->hasMany(
            DisposisiPengajuan::class,
            'id_pengajuan'
        );
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(
            RiwayatPengajuan::class,
            'id_pengajuan'
        );
    }
}
