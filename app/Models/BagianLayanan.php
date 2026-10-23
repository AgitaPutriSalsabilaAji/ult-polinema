<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BagianLayanan extends Model
{
    protected $table = 'bagian_layanan';

    public $timestamps = false;

    protected $fillable = [
        'id_layanan',
        'judul_bagian',
        'konten',
        'gambar_pendukung',
        'urutan_bagian',
        'dibuat_pada',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(
            Layanan::class,
            'id_layanan'
        );
    }
}
