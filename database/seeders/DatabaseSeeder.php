<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmploymentTypeSeeder::class,
            JobStatusSeeder::class,
            RoleSeeder::class,
            AttendanceTypeSeeder::class,
            AttendanceSourceSeeder::class,
        ]);
    }
}