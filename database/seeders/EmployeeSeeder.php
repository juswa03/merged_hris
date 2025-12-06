<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmploymentType;
use App\Models\JobStatus;
use App\Models\SalaryGrade;
use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\AttendanceSource;
use App\Models\DtrEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $this->command->info('Seeding 100 Employees using existing reference tables...');

        // 1. Fetch Existing Reference Data
        $departments = Department::pluck('id')->toArray();
        $positions = Position::pluck('id')->toArray();
        $employmentTypes = EmploymentType::pluck('id')->toArray();
        $jobStatuses = JobStatus::pluck('id')->toArray();
        $availableGrades = SalaryGrade::select('grade', 'step')->get();

        // Validate Data Existence
        if (empty($departments)) { $this->command->error('No Departments found.'); return; }
        if (empty($positions)) { $this->command->error('No Positions found.'); return; }
        if (empty($employmentTypes)) { $this->command->error('No Employment Types found.'); return; }
        if (empty($jobStatuses)) { $this->command->error('No Job Statuses found.'); return; }
        if ($availableGrades->isEmpty()) { $this->command->error('No Salary Grades found.'); return; }

        // 2. Setup Attendance Types
        $attendanceTypes = AttendanceType::all()->pluck('id', 'name')->toArray();
        if (!isset($attendanceTypes['in'])) {
            $attendanceTypes['in'] = AttendanceType::firstOrCreate(['name' => 'in'], ['description' => 'Time In'])->id;
        }
        if (!isset($attendanceTypes['out'])) {
            $attendanceTypes['out'] = AttendanceType::firstOrCreate(['name' => 'out'], ['description' => 'Time Out'])->id;
        }
        $sourceId = AttendanceSource::firstOrCreate(['name' => 'biometric'])->id;
        $employeeRole = \App\Models\Role::firstOrCreate(['name' => 'Employee']);

        // 3. Create Employees
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();

        for ($i = 0; $i < 100; $i++) {
            // Create User
            $user = User::create([
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'status' => 'Active',
                'role_id' => $employeeRole->id,
            ]);

            // Pick Random Existing Grade
            $randomGrade = $availableGrades->random();

            // Create Employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'middle_name' => $faker->lastName,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'birthdate' => $faker->date('Y-m-d', '-20 years'),
                'contact_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'hire_date' => $faker->date('Y-m-d', '-1 year'),
                'department_id' => $faker->randomElement($departments),
                'position_id' => $faker->randomElement($positions),
                'employment_type_id' => $faker->randomElement($employmentTypes),
                'job_status_id' => $faker->randomElement($jobStatuses),
                'salary_grade' => $randomGrade->grade,
                'salary_step' => $randomGrade->step,
            ]);

            // 4. Generate Attendance (Optional but requested for testing)
            $this->generateAttendanceForEmployee($employee, $startDate, $endDate, $attendanceTypes, $sourceId);

            if (($i + 1) % 10 == 0) {
                $this->command->info("Created " . ($i + 1) . " employees...");
            }
        }
    }

    private function generateAttendanceForEmployee($employee, $startDate, $endDate, $types, $sourceId)
    {
        $faker = Faker::create();
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $isWeekend = $currentDate->isWeekend();
            
            // 90% attendance on weekdays
            if (!$isWeekend && $faker->boolean(90)) {
                // AM Arrival: 7:30 - 8:30
                $amArrival = $currentDate->copy()->setTime(7, 30)->addMinutes($faker->numberBetween(0, 60));
                // AM Departure: 12:00 - 12:30
                $amDeparture = $currentDate->copy()->setTime(12, 0)->addMinutes($faker->numberBetween(0, 30));
                // PM Arrival: 13:00 - 13:30
                $pmArrival = $currentDate->copy()->setTime(13, 0)->addMinutes($faker->numberBetween(0, 30));
                // PM Departure: 17:00 - 18:00
                $pmDeparture = $currentDate->copy()->setTime(17, 0)->addMinutes($faker->numberBetween(0, 60));

                // Create Attendance Records
                $this->createAttendance($employee->id, $types['in'], $sourceId, $amArrival, 'AM IN');
                $this->createAttendance($employee->id, $types['out'], $sourceId, $amDeparture, 'AM OUT');
                $this->createAttendance($employee->id, $types['in'], $sourceId, $pmArrival, 'PM IN');
                $this->createAttendance($employee->id, $types['out'], $sourceId, $pmDeparture, 'PM OUT');

                // Calculate Hours
                $totalHours = ($amDeparture->diffInMinutes($amArrival) + $pmDeparture->diffInMinutes($pmArrival)) / 60;
                
                // Status
                $status = 'present';
                $lateMinutes = 0;
                $expectedIn = $currentDate->copy()->setTime(8, 0);
                if ($amArrival->gt($expectedIn)) {
                    $lateMinutes = $amArrival->diffInMinutes($expectedIn);
                    $status = 'late';
                }

                DtrEntry::create([
                    'employee_id' => $employee->id,
                    'dtr_date' => $currentDate->format('Y-m-d'),
                    'am_arrival' => $amArrival->format('H:i:s'),
                    'am_departure' => $amDeparture->format('H:i:s'),
                    'pm_arrival' => $pmArrival->format('H:i:s'),
                    'pm_departure' => $pmDeparture->format('H:i:s'),
                    'total_hours' => round($totalHours, 2),
                    'total_minutes' => round($totalHours * 60),
                    'under_time_minutes' => 0, // Simplified
                    'status' => $status,
                    'remarks' => $status == 'late' ? "Late {$lateMinutes} mins" : 'Present',
                    'is_weekend' => false,
                    'is_holiday' => false,
                ]);

            } elseif (!$isWeekend) {
                // Absent
                DtrEntry::create([
                    'employee_id' => $employee->id,
                    'dtr_date' => $currentDate->format('Y-m-d'),
                    'status' => 'absent',
                    'remarks' => 'Absent',
                    'is_weekend' => false,
                    'is_holiday' => false,
                    'total_hours' => 0,
                    'total_minutes' => 0,
                    'under_time_minutes' => 0,
                ]);
            } else {
                // Weekend
                DtrEntry::create([
                    'employee_id' => $employee->id,
                    'dtr_date' => $currentDate->format('Y-m-d'),
                    'status' => 'absent', // Using 'absent' as per user's previous error with 'rest_day'
                    'remarks' => 'Rest Day',
                    'is_weekend' => true,
                    'is_holiday' => false,
                    'total_hours' => 0,
                    'total_minutes' => 0,
                    'under_time_minutes' => 0,
                ]);
            }

            $currentDate->addDay();
        }
    }

    private function createAttendance($employeeId, $typeId, $sourceId, $time, $remarks)
    {
        Attendance::create([
            'employee_id' => $employeeId,
            'attendance_type_id' => $typeId,
            'attendance_source_id' => $sourceId,
            'created_at' => $time,
            'updated_at' => $time,
            'device_uid' => 'SEEDER',
            'remarks' => $remarks
        ]);
    }
}
