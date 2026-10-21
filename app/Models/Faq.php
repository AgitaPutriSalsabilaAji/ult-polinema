<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faq';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'id_unit',
        'status',
    ];

    public function unit()
    {
        return $this->belongsTo(unit::class, 'id_unit', 'id');
    }
}
