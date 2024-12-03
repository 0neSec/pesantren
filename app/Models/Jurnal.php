<?php

namespace App\Models;

use App\Models\kelas\Kelas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnal';

    protected $fillable = [
        'tanggal',
        'waktu',
        'nama_santri',
        'kelas_id',
        'temuan_perilaku',
        'jenis_temuan',
        'media_path',
        'pelapor_id'
    ];

    // Relationship with Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relationship with User (Pelapor)
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // Accessor for media_path to get full URL
    public function getMediaUrlAttribute()
    {
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }

    // Scopes for filtering
    public function scopePositiveFindings($query)
    {
        return $query->where('jenis_temuan', 'positif');
    }

    public function scopeNegativeFindings($query)
    {
        return $query->where('jenis_temuan', 'negatif');
    }
}
