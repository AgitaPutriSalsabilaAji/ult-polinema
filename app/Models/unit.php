<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $table = 'unit';

    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_unit',
        'kode_unit',
        'deskripsi',
    ];
    

    /**
     * Daftar layanan milik unit.
     */
    public function layanans(): HasMany
    {
        return $this->hasMany(
            Layanan::class,
            'id_unit',
            'id'
        );
    }
}