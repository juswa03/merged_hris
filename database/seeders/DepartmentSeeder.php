<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'HR', 'description' => 'Human Resource Department'],
            ['name' => 'STCS', 'description' => 'School of Technology and Computer Studies'],
            ['name' => 'SME', 'description' => 'School of Management and Entrepreneurship'],
            ['name' => 'SAS', 'description' => 'School of Arts and Sciences'],
            ['name' => 'SOE', 'description' => 'School of Engineering'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}