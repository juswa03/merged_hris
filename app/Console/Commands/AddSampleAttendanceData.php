<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\AttendanceType;
use App\Models\AttendanceSource;
use App\Models\DtrEntry;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AddSampleAttendanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-sample-attendance-data {--months=2 : Number of months to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample attendance and DTR data for all registered employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $months = $this->option('months');
            $this->info("🔄 Generating {$months} months of sample attendance data...");

            // Create attendance types if they don't exist
            $this->createAttendanceTypes();

            // Create attendance sources if they don't exist
            $this->createAttendanceSources();

            // Get all active employees
            $employees = Employee::whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            })->get();

            if ($employees->isEmpty()) {
                $this->error('❌ No active employees found!');
                return Command::FAILURE;
            }

            $this->info("📊 Found {$employees->count()} active employees");

            $bar = $this->output->createProgressBar($employees->count());
            $bar->start();

            foreach ($employees as $employee) {
                $this->generateEmployeeAttendanceData($employee, $months);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✅ Successfully generated {$months} months of sample attendance data!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Create attendance types if they don't exist
     */
    private function createAttendanceTypes()
    {
        $types = [
            ['name' => 'Check In', 'description' => 'Employee check-in time'],
            ['name' => 'Check Out', 'description' => 'Employee check-out time'],
            ['name' => 'Late', 'description' => 'Employee arrived late'],
            ['name' => 'Early Leave', 'description' => 'Employee left early'],
            ['name' => 'Absent', 'description' => 'Employee was absent'],
        ];

        foreach ($types as $type) {
            AttendanceType::firstOrCreate(['name' => $type['name']], $type);
        }

        $this->line('✓ Attendance types ready');
    }

    /**
     * Create attendance sources if they don't exist
     */
    private function createAttendanceSources()
    {
        $sources = [
            ['name' => 'Biometric', 'description' => 'Biometric fingerprint scanner'],
            ['name' => 'Manual', 'description' => 'Manually entered by admin'],
            ['name' => 'System', 'description' => 'System generated'],
        ];

        foreach ($sources as $source) {
            AttendanceSource::firstOrCreate(['name' => $source['name']], $source);
        }

        $this->line('✓ Attendance sources ready');
    }

    /**
     * Generate attendance data for an employee
     */
    private function generateEmployeeAttendanceData($employee, $months)
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        $endDate = now()->endOfMonth();

        $checkIn = AttendanceType::where('name', 'Check In')->first();
        $checkOut = AttendanceType::where('name', 'Check Out')->first();
        $biometricSource = AttendanceSource::where('name', 'Biometric')->first();

        $holidays = Holiday::all()->pluck('date')->toArray();

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            // Skip holidays
            if (in_array($currentDate->format('Y-m-d'), $holidays)) {
                $currentDate->addDay();
                continue;
            }

            // 80% attendance rate - realistic data
            if (rand(1, 100) <= 80) {
                $this->createDtrEntry($employee, $currentDate);
                
                // Create check-in record
                $checkInTime = $currentDate->copy()
                    ->addHours(rand(7, 8))
                    ->addMinutes(rand(0, 59))
                    ->addSeconds(rand(0, 59));

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_type_id' => $checkIn->id,
                    'attendance_source_id' => $biometricSource->id,
                    'remarks' => null,
                    'created_at' => $checkInTime,
                    'updated_at' => $checkInTime,
                ]);

                // Create check-out record
                $checkOutTime = $currentDate->copy()
                    ->addHours(rand(17, 18))
                    ->addMinutes(rand(0, 59))
                    ->addSeconds(rand(0, 59));

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_type_id' => $checkOut->id,
                    'attendance_source_id' => $biometricSource->id,
                    'remarks' => null,
                    'created_at' => $checkOutTime,
                    'updated_at' => $checkOutTime,
                ]);
            } else {
                // Create absent DTR entry
                $this->createAbsentDtrEntry($employee, $currentDate);
            }

            $currentDate->addDay();
        }
    }

    /**
     * Create a DTR entry for an employee on a specific date
     * Official Hours: 8:00 AM - 5:00 PM with 1 hour lunch break = 8 hours total
     */
    private function createDtrEntry($employee, $date)
    {
        // AM Arrival: 7:00 AM - 9:00 AM (mostly on time with some early arrivals)
        $amArrivalHour = rand(7, 9);
        $amArrivalMinute = rand(0, 59);
        $amArrival = sprintf('%02d:%02d:00', $amArrivalHour, $amArrivalMinute);

        // AM Departure (Lunch break start): 12:00 PM - 1:00 PM
        $amDepartureHour = 12;
        $amDepartureMinute = rand(0, 59);
        $amDeparture = sprintf('%02d:%02d:00', $amDepartureHour, $amDepartureMinute);

        // PM Arrival (Back from lunch): 1:00 PM - 2:00 PM
        $pmArrivalHour = 13;
        $pmArrivalMinute = rand(0, 59);
        $pmArrival = sprintf('%02d:%02d:00', $pmArrivalHour, $pmArrivalMinute);

        // PM Departure (End of shift): 5:00 PM - 6:00 PM
        $pmDepartureHour = rand(17, 18);
        $pmDepartureMinute = rand(0, 59);
        $pmDeparture = sprintf('%02d:%02d:00', $pmDepartureHour, $pmDepartureMinute);

        // Calculate AM hours
        $amArrivalTime = Carbon::parse("{$date->format('Y-m-d')} {$amArrival}");
        $amDepartureTime = Carbon::parse("{$date->format('Y-m-d')} {$amDeparture}");
        $amMinutes = $amDepartureTime->diffInMinutes($amArrivalTime);
        $amHours = $amMinutes / 60;

        // Calculate PM hours
        $pmArrivalTime = Carbon::parse("{$date->format('Y-m-d')} {$pmArrival}");
        $pmDepartureTime = Carbon::parse("{$date->format('Y-m-d')} {$pmDeparture}");
        $pmMinutes = $pmDepartureTime->diffInMinutes($pmArrivalTime);
        $pmHours = $pmMinutes / 60;

        // Total working hours (excluding 1-hour lunch break)
        $totalMinutes = $amMinutes + $pmMinutes;
        $totalHours = round($totalMinutes / 60, 2);

        // Official hours is 8 hours (9 AM to 5 PM - 1 hour lunch)
        $officialHours = 8;
        $underTimeMinutes = max(0, (int) (($officialHours - $totalHours) * 60));
        
        // Calculate overtime minutes (only if more than 8 hours)
        $overtimeMinutes = max(0, (int) (($totalHours - $officialHours) * 60));

        // Determine status
        if ($totalHours >= $officialHours) {
            $status = 'present';
        } elseif ($totalHours >= ($officialHours * 0.75)) {
            $status = 'undertime'; // Less than 8 hours but more than 6 hours
        } else {
            $status = 'absent'; // Very little work hours
        }

        DtrEntry::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'dtr_date' => $date->format('Y-m-d'),
            ],
            [
                'am_arrival' => $amArrival,
                'am_departure' => $amDeparture,
                'pm_arrival' => $pmArrival,
                'pm_departure' => $pmDeparture,
                'total_hours' => $totalHours,
                'total_minutes' => $totalMinutes,
                'under_time_minutes' => $underTimeMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'status' => $status,
                'is_holiday' => false,
                'is_weekend' => false,
                'remarks' => null,
            ]
        );
    }

    /**
     * Create an absent DTR entry
     */
    private function createAbsentDtrEntry($employee, $date)
    {
        DtrEntry::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'dtr_date' => $date->format('Y-m-d'),
            ],
            [
                'am_arrival' => null,
                'am_departure' => null,
                'pm_arrival' => null,
                'pm_departure' => null,
                'total_hours' => 0,
                'total_minutes' => 0,
                'under_time_minutes' => 480, // 8 hours
                'status' => 'absent',
                'is_holiday' => false,
                'is_weekend' => false,
                'remarks' => 'Absent - No time recorded',
            ]
        );
    }
}
