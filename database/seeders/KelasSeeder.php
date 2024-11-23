<?php

namespace Database\Seeders;

use App\Models\kelas\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $kelas = [
            [
                'nama_kelas' => 'Kelas 1A',
                'deskripsi' => 'Kelas untuk tingkat pemula tahun pertama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Kelas 1B',
                'deskripsi' => 'Kelas lanjutan untuk tingkat pemula',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Kelas 2A',
                'deskripsi' => 'Kelas untuk tingkat menengah pertama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Kelas 2B',
                'deskripsi' => 'Kelas lanjutan untuk tingkat menengah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Kelas 3A',
                'deskripsi' => 'Kelas untuk tingkat mahir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($kelas as $data) {
            Kelas::create($data);
        }
    }
}
