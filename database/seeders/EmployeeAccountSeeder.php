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
use Illuminate\Support\Facades\Hash;

class EmployeeAccountSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding Sample Employee Accounts...');

        // Get required reference data
        $employeeRole = Role::where('name', 'Employee')->first();
        $departments = Department::where('name', '!=', 'HR')->get();
        $positions = Position::where('name', '!=', 'Admin')->get();
        $fullTimeEmployment = EmploymentType::first();
        $activeJobStatus = JobStatus::first();
        $salaryGrade = SalaryGrade::first();

        // Validate data exists
        if (!$employeeRole) {
            $this->command->error('Employee role not found.');
            return;
        }
        if ($departments->isEmpty()) {
            $this->command->error('No departments found.');
            return;
        }

        $sampleEmployees = [
            [
                'email' => 'john.doe@bipsu.edu.ph',
                'password' => Hash::make('employee@123'),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Michael',
                'gender' => 'male',
                'birthdate' => '1988-06-14',
                'contact_number' => '09101234567',
                'address' => '100 Faculty Street, Benguet',
            ],
            [
                'email' => 'jane.smith@bipsu.edu.ph',
                'password' => Hash::make('employee@123'),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'middle_name' => 'Patricia',
                'gender' => 'female',
                'birthdate' => '1990-11-28',
                'contact_number' => '09109876543',
                'address' => '200 Professor Lane, Benguet',
            ],
            [
                'email' => 'robert.johnson@bipsu.edu.ph',
                'password' => Hash::make('employee@123'),
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'middle_name' => 'James',
                'gender' => 'male',
                'birthdate' => '1987-02-09',
                'contact_number' => '09115555555',
                'address' => '300 Instructor Avenue, Benguet',
            ],
            [
                'email' => 'maria.garcia@bipsu.edu.ph',
                'password' => Hash::make('employee@123'),
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'middle_name' => 'Isabelle',
                'gender' => 'female',
                'birthdate' => '1991-07-21',
                'contact_number' => '09125555555',
                'address' => '400 Academic Road, Benguet',
            ],
            [
                'email' => 'carlos.santos@bipsu.edu.ph',
                'password' => Hash::make('employee@123'),
                'first_name' => 'Carlos',
                'last_name' => 'Santos',
                'middle_name' => 'Luis',
                'gender' => 'male',
                'birthdate' => '1989-09-13',
                'contact_number' => '09135555555',
                'address' => '500 Campus Drive, Benguet',
            ],
        ];

        foreach ($sampleEmployees as $index => $employeeData) {
            // Create User
            $user = User::firstOrCreate(
                ['email' => $employeeData['email']],
                [
                    'password' => $employeeData['password'],
                    'role_id' => $employeeRole->id,
                    'status' => 'active',
                ]
            );

            // Rotate through departments and positions
            $department = $departments->get($index % $departments->count());
            $position = $positions->get($index % $positions->count());

            // Create Employee Record
            Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'],
                    'middle_name' => $employeeData['middle_name'],
                    'gender' => $employeeData['gender'],
                    'birthdate' => $employeeData['birthdate'],
                    'contact_number' => $employeeData['contact_number'],
                    'address' => $employeeData['address'],
                    'hire_date' => now()->subMonths(rand(1, 24)),
                    'department_id' => $department->id,
                    'position_id' => $position->id,
                    'employment_type_id' => $fullTimeEmployment->id,
                    'job_status_id' => $activeJobStatus->id,
                    'civil_status' => 'single',
                    'basic_salary' => $salaryGrade ? ($salaryGrade->amount + rand(-5000, 10000)) : 20000,
                    'salary_grade' => $salaryGrade ? $salaryGrade->grade : 10,
                    'salary_step' => $salaryGrade ? $salaryGrade->step : 1,
                ]
            );

            $this->command->info("Created Employee: {$employeeData['email']} in {$department->name}");
        }

        $this->command->info('Employee accounts seeded successfully!');
    }
}
