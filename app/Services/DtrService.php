<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DtrEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DtrService
{
    /**
     * Process or update a DTR record for a specific employee and date
     * whenever they time in/out.
     */
    public function processDtr(int $employeeId, string $date): void
    {
        Log::info("Processing DTR for employee {$employeeId}...");

        $attendances = Attendance::with('attendanceType')
            ->where('employee_id', $employeeId)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        if ($attendances->isEmpty()) return;

        $amArrival = null;
        $amDeparture = null;
        $pmArrival = null;
        $pmDeparture = null;

        foreach ($attendances as $attendance) {
            $time = Carbon::parse($attendance->created_at)->timezone('Asia/Manila');
            $typeName = strtolower(optional($attendance->attendanceType)->name ?? '');

            // Fix: Adjusted cutoff to account for lunch break (11:30 AM for AM, 1:30 PM for PM)
            if ($time->lt(Carbon::createFromTime(11, 30))) {
                // AM period (before 11:30 AM)
                $amArrival = $amArrival ?? $time;
                $amDeparture = $time;
            } elseif ($time->lt(Carbon::createFromTime(13, 0))) {
                // Lunch break period (11:30 AM - 1:00 PM) - skip classification
                continue;
            } else {
                // PM period (1:00 PM or later)
                $pmArrival = $pmArrival ?? $time;
                $pmDeparture = $time;
            }
        }

        // Compute total worked minutes
        $totalMinutes = 0;
        if ($amArrival && $amDeparture) {
            // Validate: AM departure must be after or equal to AM arrival
            if ($amDeparture->lessThan($amArrival)) {
                Log::warning("Invalid AM times: departure before arrival for employee {$employeeId}");
                $amDeparture = $amArrival;
            }
            $totalMinutes += $amArrival->diffInMinutes($amDeparture);
        }
        if ($pmArrival && $pmDeparture) {
            // Validate: PM departure must be after or equal to PM arrival
            if ($pmDeparture->lessThan($pmArrival)) {
                Log::warning("Invalid PM times: departure before arrival for employee {$employeeId}");
                $pmDeparture = $pmArrival;
            }
            $totalMinutes += $pmArrival->diffInMinutes($pmDeparture);
        }

        $totalHours = floor($totalMinutes / 60);
        $remainingMinutes = $totalMinutes % 60;

        // Determine additional flags
        $day = Carbon::parse($date);
        $isWeekend = $day->isWeekend();
        
        // Fix: Check holiday table for actual holidays
        $isHoliday = $this->isHolidayDate($date);

        // 🔍 Use the helper function to compute DTR status
        [$status, $remarks, $underTimeMinutes] = $this->determineDtrStatus(
            $amArrival,
            $pmArrival,
            $totalMinutes,
            $isWeekend,
            $isHoliday
        );
        
        $isWeekendBinary = $isWeekend == true? 1: 0;
        
        // Save or update DTR entry
        DtrEntry::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'dtr_date' => $date,
            ],
            [
                'am_arrival' => $amArrival?->format('H:i'),
                'am_departure' => $amDeparture?->format('H:i'),
                'pm_arrival' => $pmArrival?->format('H:i'),
                'pm_departure' => $pmDeparture?->format('H:i'),
                'total_hours' => $totalHours,
                'total_minutes' => $remainingMinutes,
                'under_time_minutes' => $underTimeMinutes,
                'remarks' => $remarks,
                'is_holiday' => $isHoliday ? 1 : 0,
                'is_weekend' => $isWeekendBinary,
                'status' => $status,
            ]
        );

        Log::info("✅ DTR processed successfully for employee {$employeeId}");
    }

    /**
     * Check if a date is a holiday
     */
    private function isHolidayDate(string $date): bool
    {
        // Query holiday table - returns true if date is in holidays table
        $holiday = DB::table('tbl_holidays')
            ->whereDate('date', $date)
            ->exists();
        
        return $holiday;
    }

    /**
     * Determine the DTR status, remarks, and undertime based on work hours and attendance.
     *
     * @return array [status, remarks, undertimeMinutes]
     */
    private function determineDtrStatus(
        ?Carbon $amArrival,
        ?Carbon $pmArrival,
        int $totalMinutes,
        bool $isWeekend,
        bool $isHoliday
    ): array {
        $officialStart = Carbon::createFromTime(8, 0, 0);
        $officialEnd = Carbon::createFromTime(17, 0, 0);
        $requiredMinutes = 8 * 60;
        $gracePeriodMinutes = 5; // 5-minute grace period for late arrivals

        $status = 'absent';
        $remarks = '';
        $underTimeMinutes = 0;

        // Skip absence check for weekends/holidays
        if ($isWeekend || $isHoliday) {
            return ['present', $isWeekend ? 'Weekend' : 'Holiday', 0];
        }

        if ($amArrival || $pmArrival) {
            $status = 'present';
            $remarks = 'Present';

            // Late check (after 8:05 AM) - consistent grace period
            $gracePeriodEnd = $officialStart->copy()->addMinutes($gracePeriodMinutes);
            if ($amArrival && $amArrival->gt($gracePeriodEnd)) {
                $status = 'late';
                $remarks = 'Late arrival';
            }

            // Undertime check (less than 8 hours total)
            if ($totalMinutes < $requiredMinutes) {
                $status = 'undertime';
                $remarks = 'Undertime';
                $underTimeMinutes = $requiredMinutes - $totalMinutes;
            }

            // Overtime check (more than 8 hours)
            if ($totalMinutes > $requiredMinutes + 15) {
                $status = 'present';
                $remarks = 'With Overtime';
            }
        }

        return [$status, $remarks, $underTimeMinutes];
    }
}

