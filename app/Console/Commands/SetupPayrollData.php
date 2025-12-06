<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PayrollPeriod;
use App\Models\Employee;
use App\Models\DtrEntry;
use App\Models\JobStatus;
use App\Models\SalaryGrade;
use Carbon\Carbon;

class SetupPayrollData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-payroll-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup necessary data for payroll generation (Period, Active Employees, DTRs, Salaries)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Payroll Data Setup...');

        // 1. Create Payroll Period
        $this->info('1️⃣ Checking Payroll Period...');
        $startDate = Carbon::parse('2025-11-01');
        $endDate = Carbon::parse('2025-11-15');
        
        // Ensure Cutoff Type exists
        $cutoffType = \App\Models\CutOffType::firstOrCreate(['name' => 'Semi-Monthly']);

        $period = PayrollPeriod::firstOrCreate(
            [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            [
                'cut_off_type_id' => $cutoffType->id,
                'status' => 'draft',
            ]
        );
        $this->info("✅ Payroll Period ensured: {$period->start_date->format('M d')} - {$period->end_date->format('M d')}");

        // 2. Ensure Active Job Status
        $activeStatus = JobStatus::firstOrCreate(['name' => 'Active']);

        // 3. Update Employees to Active and Ensure Salary
        $this->info('2️⃣ Updating Employees...');
        $employees = Employee::all();
        
        if ($employees->isEmpty()) {
            $this->error('❌ No employees found! Please run seeders first.');
            return;
        }

        // Create a default salary grade if none exists
        $defaultGrade = SalaryGrade::firstOrCreate(
            ['grade' => 1, 'step' => 1],
            ['amount' => 15000, 'effective_date' => now(), 'is_active' => true]
        );

        foreach ($employees as $employee) {
            // Set to Active
            $employee->job_status_id = $activeStatus->id;
            
            // Ensure Salary (Assign Grade 1 if no salary set)
            if (!$employee->basic_salary && !$employee->salary_grade_id) {
                $employee->salary_grade_id = $defaultGrade->id;
            }
            
            $employee->save();
        }
        $this->info("✅ {$employees->count()} employees updated to Active status with valid salary configuration.");

        // 4. Generate Complete DTR Entries
        $this->info('3️⃣ Generating DTR Entries for Nov 1-15...');
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            $dateStr = $currentDate->format('Y-m-d');
            $this->line("   Processing date: {$dateStr}");

            foreach ($employees as $employee) {
                // Check if entry exists
                $exists = DtrEntry::where('employee_id', $employee->id)
                    ->where('dtr_date', $dateStr)
                    ->exists();

                if (!$exists) {
                    // Create a perfect attendance record (8-5)
                    DtrEntry::create([
                        'employee_id' => $employee->id,
                        'dtr_date' => $dateStr,
                        'am_arrival' => '08:00:00',
                        'am_departure' => '12:00:00',
                        'pm_arrival' => '13:00:00',
                        'pm_departure' => '17:00:00',
                        'total_hours' => 8,
                        'total_minutes' => 480,
                        'under_time_minutes' => 0,
                        'over_time_minutes' => 0,
                        'status' => 'present',
                        'is_holiday' => false,
                        'is_weekend' => false,
                        'remarks' => 'System Generated'
                    ]);
                }
            }
            $currentDate->addDay();
        }
        $this->info("✅ DTR Entries generated for all active employees.");

        $this->info('🎉 Payroll Data Setup Complete! You can now generate payroll.');
    }
}
