<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'role_name' => 'Super Admin',
            ],
            [
                'id' => 2,
                'role_name' => 'Admin',
            ],
            [
                'id' => 3,
                'role_name' => 'Ketua Tim',
            ],
            [
                'id' => 4,
                'role_name' => 'User',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}