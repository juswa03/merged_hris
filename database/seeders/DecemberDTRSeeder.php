<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\DtrEntry;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DecemberDTRSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $this->command->info('Generating December 2025 DTR entries for all employees...');

        $employees = Employee::all();
        $startDate = Carbon::parse('2025-12-01');
        $endDate = Carbon::parse('2025-12-31');

        foreach ($employees as $employee) {
            $this->command->info("Generating DTR for: {$employee->first_name} {$employee->last_name}");
            
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $isWeekend = $currentDate->isWeekend();
                
                // Skip existing entries
                $exists = DtrEntry::where('employee_id', $employee->id)
                    ->where('dtr_date', $currentDate->format('Y-m-d'))
                    ->exists();
                
                if ($exists) {
                    $currentDate->addDay();
                    continue;
                }

                if ($isWeekend) {
                    // Weekend - Rest Day
                    DtrEntry::create([
                        'employee_id' => $employee->id,
                        'dtr_date' => $currentDate->format('Y-m-d'),
                        'status' => 'absent',
                        'remarks' => 'Rest Day',
                        'is_weekend' => true,
                        'is_holiday' => false,
                        'total_hours' => 0,
                        'total_minutes' => 0,
                        'under_time_minutes' => 0,
                    ]);
                } else {
                    // Check for special holidays in December 2025
                    $isHoliday = $this->isSpecialHoliday($currentDate);
                    
                    if ($isHoliday) {
                        DtrEntry::create([
                            'employee_id' => $employee->id,
                            'dtr_date' => $currentDate->format('Y-m-d'),
                            'status' => 'absent',
                            'remarks' => 'Holiday',
                            'is_weekend' => false,
                            'is_holiday' => true,
                            'total_hours' => 0,
                            'total_minutes' => 0,
                            'under_time_minutes' => 0,
                        ]);
                    } else {
                        // Regular working day - 90% attendance
                        if ($faker->boolean(90)) {
                            // Generate attendance times with variance that can trigger undertime
                            // Morning: Some arrive late (after 8 AM) or depart early (before 12 PM)
                            $amArrival = $currentDate->copy()->setTime(8, 0)->addMinutes($faker->numberBetween(-30, 45)); // Can be 7:30-8:45
                            $amDeparture = $currentDate->copy()->setTime(12, 0)->addMinutes($faker->numberBetween(-45, 15)); // Can be 11:15-12:15
                            
                            // Afternoon: Some arrive late (after 1 PM) or depart early (before 5 PM)
                            $pmArrival = $currentDate->copy()->setTime(13, 0)->addMinutes($faker->numberBetween(-15, 45)); // Can be 12:45-1:45
                            $pmDeparture = $currentDate->copy()->setTime(17, 0)->addMinutes($faker->numberBetween(-45, 30)); // Can be 4:15-5:30

                            // Expected times
                            $expectedAmArrival = $currentDate->copy()->setTime(8, 0);
                            $expectedAmDeparture = $currentDate->copy()->setTime(12, 0);
                            $expectedPmArrival = $currentDate->copy()->setTime(13, 0);
                            $expectedPmDeparture = $currentDate->copy()->setTime(17, 0);

                            // Calculate actual minutes worked in each shift
                            $amMinutesWorked = $amDeparture->diffInMinutes($amArrival);
                            $pmMinutesWorked = $pmDeparture->diffInMinutes($pmArrival);
                            $totalMinutesWorked = $amMinutesWorked + $pmMinutesWorked;
                            $totalHours = $totalMinutesWorked / 60;

                            // Expected minutes per shift: 4 hours = 240 minutes
                            $expectedAmMinutes = 240;
                            $expectedPmMinutes = 240;

                            // Calculate undertime for each shift
                            // Morning undertime: if arrival is after 8 AM or departure is before 12 PM
                            $amUndertime = 0;
                            if ($amArrival->gt($expectedAmArrival)) {
                                $amUndertime += $amArrival->diffInMinutes($expectedAmArrival);
                            }
                            if ($amDeparture->lt($expectedAmDeparture)) {
                                $amUndertime += $expectedAmDeparture->diffInMinutes($amDeparture);
                            }

                            // Afternoon undertime: if arrival is after 1 PM or departure is before 5 PM
                            $pmUndertime = 0;
                            if ($pmArrival->gt($expectedPmArrival)) {
                                $pmUndertime += $pmArrival->diffInMinutes($expectedPmArrival);
                            }
                            if ($pmDeparture->lt($expectedPmDeparture)) {
                                $pmUndertime += $expectedPmDeparture->diffInMinutes($pmDeparture);
                            }

                            // Total undertime
                            $totalUndertime = $amUndertime + $pmUndertime;

                            // Status determination
                            $status = 'present';
                            $remarks = 'Present';
                            
                            // Check if late (arrived after 8 AM)
                            if ($amArrival->gt($expectedAmArrival)) {
                                $lateMinutes = $amArrival->diffInMinutes($expectedAmArrival);
                                $status = 'late';
                                $remarks = "Late {$lateMinutes} mins";
                            }
                            
                            // Check if undertime
                            if ($totalUndertime > 0) {
                                $status = 'undertime';
                                $remarks = "Undertime " . floor($totalUndertime / 60) . "h " . ($totalUndertime % 60) . "m";
                            }

                            DtrEntry::create([
                                'employee_id' => $employee->id,
                                'dtr_date' => $currentDate->format('Y-m-d'),
                                'am_arrival' => $amArrival->format('H:i:s'),
                                'am_departure' => $amDeparture->format('H:i:s'),
                                'pm_arrival' => $pmArrival->format('H:i:s'),
                                'pm_departure' => $pmDeparture->format('H:i:s'),
                                'total_hours' => round($totalHours, 2),
                                'total_minutes' => round($totalMinutesWorked),
                                'under_time_minutes' => round($totalUndertime),
                                'status' => $status,
                                'remarks' => $remarks,
                                'is_weekend' => false,
                                'is_holiday' => false,
                            ]);
                        } else {
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
                        }
                    }
                }

                $currentDate->addDay();
            }
        }

        $this->command->info('December 2025 DTR entries generated successfully!');
    }

    private function isSpecialHoliday($date)
    {
        // December 2025 Special Holidays
        $holidays = [
            '2025-12-08', // Feast of the Immaculate Conception
            '2025-12-25', // Christmas Day
            '2025-12-30', // Rizal Day (observed)
        ];

        return in_array($date->format('Y-m-d'), $holidays);
    }
}
