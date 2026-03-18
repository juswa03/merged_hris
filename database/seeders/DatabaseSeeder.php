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
            AdminUserSeeder::class,
            HRUserSeeder::class,
            EmployeeSeeder::class,
            EmployeeAccountSeeder::class,
            DeductionsAndAllowancesSeeder::class,
            PerformanceCriteriaSeeder::class,
            SalaryGrade2025Seeder::class,
            DecemberDTRSeeder::class,
        ]);
    }
}