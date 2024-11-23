<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'Santri',
                'description' => 'Student role with basic access'
            ],
            [
                'id' => 2,
                'name' => 'Ustad',
                'description' => 'Teacher or instructor role'
            ],
            [
                'id' => 3,
                'name' => 'Admin',
                'description' => 'Administrative role with management capabilities'
            ],
            [
                'id' => 4,
                'name' => 'Super Admin',
                'description' => 'Highest level administrative role with full system access'
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                [
                    'name' => $role['name'],
                    'description' => $role['description']
                ]
            );
        }
    }
}
