<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisposisiPengajuan extends Model
{
    protected $table = 'disposisi_pengajuan';

    const CREATED_AT = 'tanggal_disposisi';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_pengajuan',
        'id_unit_tujuan',
        'id_admin_pengirim',
        'id_admin_tujuan',
        'catatan_disposisi',
        'status_disposisi',
    ];

    protected $casts = [
        'tanggal_disposisi' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }

    public function unitTujuan(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_unit_tujuan');
    }
}
