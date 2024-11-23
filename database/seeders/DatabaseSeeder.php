<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,    // Jalankan Role seeder terlebih dahulu
            UserSeeder::class,    // Kemudian User seeder
            KelasSeeder::class,   // Terakhir Kelas seeder
            JenisSetoranSeeder::class,
        ]);
    }
}
