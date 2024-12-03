<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAduan extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'jenis_aduan';

    // Fillable fields for mass assignment
    protected $fillable = [
        'nama',
        'deskripsi'
    ];

}
