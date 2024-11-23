<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitabSurah extends Model
{
    protected $table = 'kitab_surah';

    protected $fillable = [
        'jenis_setoran_id',
        'nama',
        'deskripsi'
    ];

    public function jenisSetoran()
    {
        return $this->belongsTo(JenisSetoran::class, 'jenis_setoran_id');
    }
}
