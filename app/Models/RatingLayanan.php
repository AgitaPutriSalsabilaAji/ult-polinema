<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatingLayanan extends Model
{
    protected $table = 'rating_layanan';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_pengajuan',
        'id_pengguna',
        'id_layanan',
        'rating',
        'komentar',
        'dibuat_pada',
    ];

    protected $casts = [
        'rating' => 'integer',
        'dibuat_pada' => 'datetime',
    ];

    /**
     * Layanan yang mendapatkan rating.
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(
            Layanan::class,
            'id_layanan',
            'id_layanan'
        );
    }

    /**
     * Pengajuan yang diberikan rating.
     *
     * Sesuaikan nama model jika model pengajuan
     * di proyekmu menggunakan nama berbeda.
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            Pengajuan::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    /**
     * Pengguna yang memberikan rating.
     *
     * Contoh ini mengasumsikan model Pengguna
     * menggunakan primary key id_pengguna.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_pengguna',
            'id'
        );
    }
}