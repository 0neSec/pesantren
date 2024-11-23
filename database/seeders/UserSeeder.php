<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 4,
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3,
        ]);

        // Create Ustad
        User::create([
            'name' => 'Ustad Ahmad',
            'email' => 'ustad@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,
        ]);

        // Create Santri
        User::create([
            'name' => 'Santri Abdullah',
            'email' => 'santri@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 1,
        ]);

        // Create additional random users if needed
        User::factory()->count(10)->create();
    }
}
