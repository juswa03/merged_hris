<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmploymentType;
use App\Models\JobStatus;
use App\Models\SalaryGrade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class HRUserSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding HR Staff Accounts...');

        // Get required reference data
        $hrRole = Role::where('name', 'HR Staff')->first();
        $hrDepartment = Department::where('name', 'HR')->first();
        $adminPosition = Position::where('name', 'Admin')->first();
        $fullTimeEmployment = EmploymentType::first();
        $activeJobStatus = JobStatus::first();
        $salaryGrade = SalaryGrade::first();

        // Validate data exists
        if (!$hrRole) {
            $this->command->error('HR Staff role not found.');
            return;
        }
        if (!$hrDepartment) {
            $this->command->error('HR Department not found.');
            return;
        }

        // Use environment variable for default password; never hardcode credentials in source code
        $defaultPassword = Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'hr@12345'));

        $hrStaffData = [
            [
                'email' => 'hr.manager@bipsu.edu.ph',
                'password' => $defaultPassword,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'middle_name' => 'Garcia',
                'gender' => 'female',
                'birthdate' => '1985-05-15',
                'contact_number' => '09171234567',
                'address' => '123 HR Street, Benguet',
            ],
            [
                'email' => 'hr.specialist@bipsu.edu.ph',
                'password' => $defaultPassword,
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Manuel',
                'gender' => 'male',
                'birthdate' => '1990-08-22',
                'contact_number' => '09187654321',
                'address' => '456 HR Avenue, Benguet',
            ],
            [
                'email' => 'hr.officer@bipsu.edu.ph',
                'password' => $defaultPassword,
                'first_name' => 'Ana',
                'last_name' => 'Rodriguez',
                'middle_name' => 'Reyes',
                'gender' => 'female',
                'birthdate' => '1992-03-10',
                'contact_number' => '09165432109',
                'address' => '789 HR Boulevard, Benguet',
            ],
        ];

        foreach ($hrStaffData as $staffData) {
            // Create User
            $user = User::firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'password' => $staffData['password'],
                    'role_id' => $hrRole->id,
                    'status' => 'active',
                ]
            );

            // Create Employee Record
            Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $staffData['first_name'],
                    'last_name' => $staffData['last_name'],
                    'middle_name' => $staffData['middle_name'],
                    'gender' => $staffData['gender'],
                    'birthdate' => $staffData['birthdate'],
                    'contact_number' => $staffData['contact_number'],
                    'address' => $staffData['address'],
                    'hire_date' => now(),
                    'department_id' => $hrDepartment->id,
                    'position_id' => $adminPosition->id,
                    'employment_type_id' => $fullTimeEmployment->id,
                    'job_status_id' => $activeJobStatus->id,
                    'civil_status' => 'single',
                    'basic_salary' => $salaryGrade ? $salaryGrade->amount : 25000,
                    'salary_grade' => $salaryGrade ? $salaryGrade->grade : 11,
                    'salary_step' => $salaryGrade ? $salaryGrade->step : 1,
                ]
            );

            $this->command->info("Created HR Staff: {$staffData['email']}");
        }

        $this->command->info('HR Staff accounts seeded successfully!');
    }
}
