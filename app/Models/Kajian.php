<?php

namespace App\Models;

use App\Models\kelas\Kelas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kajian extends Model
{
    use HasFactory;

    protected $table = 'kajian';

    protected $fillable = [
        'tanggal_waktu',
        'santri_id',
        'kelas_id',
        'jenis_kajian_id',
        'nama_ustadz',
        'judul_kitab',
        'media_path',
        'pelapor_id',
        'catatan'
    ];

    protected $dates = [
        'tanggal_waktu',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function santri()
    {
        return $this->belongsTo(User::class, 'santri_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jenisKajian()
    {
        return $this->belongsTo(JenisKajian::class);
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}
