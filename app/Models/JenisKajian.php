<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKajian extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_kajian';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'deskripsi'
    ];

    /**
     * The attributes that should be visible in arrays/JSON.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'nama',
        'deskripsi',
        'created_at',
        'updated_at'
    ];
}
