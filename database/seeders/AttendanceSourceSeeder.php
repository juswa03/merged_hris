<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class AttendanceSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sources = [
            [
                'name' => 'biometric',
                'description' => 'Fingerprint or biometric device',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'manual',
                'description' => 'Manual entry by administrator',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'web',
                'description' => 'Web portal entry',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'mobile',
                'description' => 'Mobile app entry',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($sources as $source) {
            DB::table('tbl_attendance_sources')->insert($source);
        }

        $this->command->info('Attendance sources seeded successfully!');
    }
}
