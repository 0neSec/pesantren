<?php

namespace App\Models;

use App\Models\kelas\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mutabaah extends Model
{
    protected $table = 'mutabaah';

    protected $fillable = [
        'santri_id',
        'kelas_id',
        'ustadz_id',
        'jenis_storan_id',
        'kitab_surah_id',
        'waktu_mulai',
        'waktu_selesai',
        'mulai_storan',
        'akhir_storan',
        'nilai_bacaan',
        'nilai_hafalan',
        'kendala',
        'deskripsi',
        'media_path'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'nilai_bacaan' => 'decimal:2',
        'nilai_hafalan' => 'decimal:2'
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'santri_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }

    public function jenisSetoran(): BelongsTo
    {
        return $this->belongsTo(JenisSetoran::class, 'jenis_storan_id');
    }

    public function kitabSurah(): BelongsTo
    {
        return $this->belongsTo(KitabSurah::class);
    }
}
