<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'HR Staff'],
            ['name' => 'Employee'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}