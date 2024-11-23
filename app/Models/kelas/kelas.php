<?php

namespace App\Models\kelas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan
    protected $table = 'kelas';

    // Menentukan field yang bisa diisi (mass assignment)
    protected $fillable = [
        'nama_kelas',
        'deskripsi'
    ];

    // Menentukan field yang disembunyikan saat serialisasi
    protected $hidden = [
        'created_at',
        'updated_at'
    ];


}
