<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_employment_type')->insert([
            ['name' => 'Full-time', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Part-time', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contract', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Internship', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Temporary', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
