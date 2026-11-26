<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

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

    protected static function filterMissingColumns(array $attributes): array
    {
        if (!Schema::hasTable((new static)->getTable())) {
            return $attributes;
        }

        $allowedColumns = array_flip(Schema::getColumnListing((new static)->getTable()));

        return array_intersect_key($attributes, $allowedColumns);
    }

    public static function create(array $attributes = [])
    {
        return static::query()->create(static::filterMissingColumns($attributes));
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }
}
