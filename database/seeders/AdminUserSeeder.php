<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Timestamp;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Get admin role
        $adminRole = Role::where('name', 'Super Admin')->first();

        // Create admin user
        User::create([
            'email' => 'arojadoj6@gmail.com',
            'password' => Hash::make('admin12345'),
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);
    }
}