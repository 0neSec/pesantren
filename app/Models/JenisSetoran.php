<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSetoran extends Model
{
    protected $table = 'jenis_setoran';

    protected $fillable = [
        'nama',
        'deskripsi'
    ];
}
