<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPengajuan extends Model
{
    protected $table = 'riwayat_pengajuan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    public $timestamps = false;

    protected $fillable = [
        'id_pengajuan',
        'status_sebelumnya',
        'status_baru',
        'judul_status',
        'deskripsi',
        'nama_aktor',
        'id_admin',
        'dibuat_pada',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }
}
