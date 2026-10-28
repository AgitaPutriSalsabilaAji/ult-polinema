<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $primaryKey = 'id_layanan';

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

    /**
     * Unit pemilik layanan.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(
            Unit::class,
            'id_unit',
            'id_unit'
        );
    }

    /**
     * Bagian-bagian layanan.
     */
    public function bagianLayanan(): HasMany
    {
        return $this->hasMany(
            BagianLayanan::class,
            'id_layanan',
            'id_layanan'
        )->orderBy('urutan_bagian');
    }

    /**
     * Rating dan ulasan untuk layanan ini.
     */
    public function ratingUlasans(): HasMany
    {
        return $this->hasMany(
            RatingLayanan::class,
            'id_layanan',
            'id_layanan'
        );
    }
}