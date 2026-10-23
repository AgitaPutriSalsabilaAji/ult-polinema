<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_layanan',
        'id_unit',
        'estimasi_waktu',
        'deskripsi_singkat',
        'tarif',
        'jam_operasional',
        'status',
        'tautan_eksternal',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function bagianLayanan(): HasMany
    {
        return $this->hasMany(
            BagianLayanan::class,
            'id_layanan'
        )->orderBy('urutan_bagian');
    }
}
