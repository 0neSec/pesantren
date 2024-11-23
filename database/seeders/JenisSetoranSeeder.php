<?php

namespace Database\Seeders;

use App\Models\JenisSetoran;
use Illuminate\Database\Seeder;

class JenisSetoranSeeder extends Seeder  // Nama class harus sama dengan nama file
{
    public function run()
    {
        $jenisSetoran = [  // Ubah nama variable untuk konsistensi
            [
                'nama' => 'Quraan',
                'deskripsi' => 'Setoran hafalan Al-Quran'
            ],
            [
                'nama' => 'Hadist',
                'deskripsi' => 'Setoran hafalan Hadist'
            ],
            [
                'nama' => 'Matan',
                'deskripsi' => 'Setoran hafalan Matan'
            ],
        ];

        foreach ($jenisSetoran as $jenis) {
            JenisSetoran::create($jenis);
        }
    }
}
