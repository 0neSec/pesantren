<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    use HasFactory;

    // Specify the table name if it differs from the default plural form
    protected $table = 'aduan';

    // Fillable fields for mass assignment
    protected $fillable = [
        'tanggal_waktu',
        'jenis_aduan_id',
        'alasan',
        'keterangan',
        'dalam_tekanan',
        'kesadaran_penuh',
        'media_path',
        'pelapor_id'
    ];

    // Cast certain fields to appropriate types
    protected $casts = [
        'tanggal_waktu' => 'datetime',
        'dalam_tekanan' => 'boolean',
        'kesadaran_penuh' => 'boolean'
    ];

    // Relationship with Jenis Aduan (Complaint Type)
    public function jenisAduan()
    {
        return $this->belongsTo(JenisAduan::class, 'jenis_aduan_id');
    }

    // Relationship with User (Reporter)
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // Mutator for media path to ensure full storage path
    public function getMediaUrlAttribute()
    {
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }
}
