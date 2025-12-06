<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\DtrEntry;
use Carbon\Carbon;

class PayrollCalculationService
{
    // Philippine Labor Code Constants
    const HOURS_PER_DAY = 8;
    const DAYS_PER_MONTH = 22; // Standard working days per month
    const MINUTES_PER_HOUR = 60;
    const MINUTES_PER_DAY = 480; // 8 hours * 60 minutes

    // Overtime Rates (Philippine Labor Code)
    const REGULAR_OT_RATE = 1.25;      // Regular overtime (after 8 hours on regular day)
    const RESTDAY_RATE = 1.30;         // Rest day work
    const RESTDAY_OT_RATE = 1.69;      // Rest day overtime (1.30 * 1.30)
    const SPECIAL_HOLIDAY_RATE = 1.30; // Special holiday
    const REGULAR_HOLIDAY_RATE = 2.00; // Regular holiday
    const NIGHT_DIFF_RATE = 0.10;      // Night differential (10% of hourly rate)

    // Night shift hours (10 PM to 6 AM)
    const NIGHT_SHIFT_START = 22; // 10 PM
    const NIGHT_SHIFT_END = 6;    // 6 AM

    protected $taxCalculationService;

    public function __construct(TaxCalculationService $taxCalculationService = null)
    {
        $this->taxCalculationService = $taxCalculationService ?? new TaxCalculationService();
    }

    /**
     * Calculate hourly rate from monthly salary
     */
    public function calculateHourlyRate($monthlySalary)
    {
        return $monthlySalary / self::DAYS_PER_MONTH / self::HOURS_PER_DAY;
    }

    /**
     * Resolve the monthly salary for the employee based on the assigned salary grade.
     * Falls back to the persisted basic salary when no grade is set.
     */
    protected function resolveMonthlySalary(Employee $employee)
    {
        $salaryFromGrade = $employee->syncSalaryFromGrade();

        if ($salaryFromGrade !== null) {
            return (float) $salaryFromGrade;
        }

        return (float) ($employee->basic_salary ?? 0);
    }

    /**
     * Calculate overtime pay based on DTR entries
     */
    public function calculateOvertimePay(Employee $employee, PayrollPeriod $period)
    {
        $basicSalary = $this->resolveMonthlySalary($employee);
        $hourlyRate = $this->calculateHourlyRate($basicSalary);

        // Get DTR entries for the period
        $dtrEntries = DtrEntry::where('employee_id', $employee->id)
            ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
            ->get();

        $regularOvertimeHours = 0;
        $restdayOvertimeHours = 0;
        $holidayOvertimeHours = 0;

        foreach ($dtrEntries as $dtr) {
            // Fix: Calculate total minutes from both hours and remaining minutes
            $totalMinutes = ($dtr->total_hours * self::MINUTES_PER_HOUR) + ($dtr->total_minutes ?? 0);
            $totalHours = $totalMinutes / self::MINUTES_PER_HOUR;

            // Only count overtime if more than 8 hours worked
            if ($totalHours > self::HOURS_PER_DAY) {
                $overtimeHours = $totalHours - self::HOURS_PER_DAY;

                if ($dtr->is_holiday) {
                    $holidayOvertimeHours += $overtimeHours;
                } elseif ($dtr->is_weekend) {
                    $restdayOvertimeHours += $overtimeHours;
                } else {
                    $regularOvertimeHours += $overtimeHours;
                }
            }
        }

        // Calculate overtime pay
        $regularOvertimePay = $regularOvertimeHours * $hourlyRate * self::REGULAR_OT_RATE;
        $restdayOvertimePay = $restdayOvertimeHours * $hourlyRate * self::RESTDAY_OT_RATE;
        $holidayOvertimePay = $holidayOvertimeHours * $hourlyRate * self::REGULAR_HOLIDAY_RATE;

        $totalOvertimePay = $regularOvertimePay + $restdayOvertimePay + $holidayOvertimePay;

        return [
            'total_overtime_pay' => $totalOvertimePay,
            'regular_overtime_hours' => round($regularOvertimeHours, 2),
            'regular_overtime_pay' => round($regularOvertimePay, 2),
            'restday_overtime_hours' => round($restdayOvertimeHours, 2),
            'restday_overtime_pay' => round($restdayOvertimePay, 2),
            'holiday_overtime_hours' => round($holidayOvertimeHours, 2),
            'holiday_overtime_pay' => round($holidayOvertimePay, 2),
        ];
    }

    /**
     * Calculate allowances for the period
     */
    public function calculateAllowances(Employee $employee, PayrollPeriod $period)
    {
        $totalAllowances = 0;

        // Get active allowances for this employee
        $activeAllowances = $employee->allowances()
            ->wherePivot('effective_from', '<=', $period->end_date)
            ->where(function($query) use ($period) {
                $query->whereNull('tbl_employee_allowances.effective_to')
                      ->orWhere('tbl_employee_allowances.effective_to', '>=', $period->start_date);
            })
            ->get();

        foreach ($activeAllowances as $allowance) {
            $amount = $allowance->amount;

            // If annual allowance, pro-rate it to monthly
            if ($allowance->type === 'annual') {
                $amount = $amount / 12;
            }

            $totalAllowances += $amount;
        }

        return round($totalAllowances, 2);
    }

    /**
     * Calculate attendance-based deductions (lates, absences, undertime)
     */
    public function calculateAttendanceDeductions(Employee $employee, PayrollPeriod $period)
    {
        $basicSalary = $this->resolveMonthlySalary($employee);
        $hourlyRate = $this->calculateHourlyRate($basicSalary);
        $minuteRate = $hourlyRate / self::MINUTES_PER_HOUR;

        // Get DTR entries for the period
        $dtrEntries = DtrEntry::where('employee_id', $employee->id)
            ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
            ->get();

        $lateDeductions = 0;
        $absentDeductions = 0;
        $undertimeDeductions = 0;

        foreach ($dtrEntries as $dtr) {
            // Absent deduction (no attendance or status is absent)
            if ($dtr->status === 'absent' || ($dtr->total_minutes === 0 && !$dtr->is_weekend && !$dtr->is_holiday)) {
                $absentDeductions += $hourlyRate * self::HOURS_PER_DAY; // Full day deduction
                continue;
            }

            // Undertime deduction
            if ($dtr->under_time_minutes > 0) {
                $undertimeDeductions += $dtr->under_time_minutes * $minuteRate;
            }

            // Late deduction (if status is late)
            // Apply consistent 5-minute grace period
            if ($dtr->status === 'late' && $dtr->am_arrival) {
                $scheduledStart = Carbon::parse('08:00:00');
                $actualArrival = Carbon::parse($dtr->am_arrival);
                $gracePeriodEnd = $scheduledStart->copy()->addMinutes(5);

                // Only charge late if after grace period
                if ($actualArrival->greaterThan($gracePeriodEnd)) {
                    $lateMinutes = $gracePeriodEnd->diffInMinutes($actualArrival);
                    $lateDeductions += $lateMinutes * $minuteRate;
                }
            }
        }

        return [
            'late_deductions' => round($lateDeductions, 2),
            'absent_deductions' => round($absentDeductions, 2),
            'undertime_deductions' => round($undertimeDeductions, 2),
        ];
    }

    /**
     * Calculate GSIS contribution (9% of basic salary for employee share)
     */
    public function calculateGSISContribution($monthlySalary)
    {
        // GSIS Life and Retirement Premium: 9% of Basic Monthly Salary
        $rate = \App\Models\PayrollSetting::getValue('gsis_rate', 0.09);
        return $monthlySalary * $rate;
    }

    /**
     * Calculate PhilHealth contribution based on 2025 rates
     */
    public function calculatePhilHealthContribution($monthlySalary)
    {
        // 2025 PhilHealth: 5% of basic salary (2.5% employee share)
        $rate = \App\Models\PayrollSetting::getValue('philhealth_rate', 0.05);
        $contribution = $monthlySalary * $rate / 2; // Divide by 2 for employee share only

        // Minimum and maximum contribution
        $minContribution = \App\Models\PayrollSetting::getValue('philhealth_min_contribution', 500.00);
        $maxContribution = \App\Models\PayrollSetting::getValue('philhealth_max_contribution', 5000.00);

        return max($minContribution, min($contribution, $maxContribution));
    }

    /**
     * Calculate Pag-IBIG contribution
     */
    public function calculatePagIbigContribution($monthlySalary)
    {
        // Pag-IBIG: 2% of monthly salary (employee share)
        $rate = \App\Models\PayrollSetting::getValue('pagibig_rate', 0.02);
        $minContribution = \App\Models\PayrollSetting::getValue('pagibig_min_contribution', 100.00);
        $maxContribution = \App\Models\PayrollSetting::getValue('pagibig_max_contribution', 200.00);

        $contribution = $monthlySalary * $rate;

        return max($minContribution, min($contribution, $maxContribution));
    }

    /**
     * Calculate withholding tax based on BIR tax table (2025)
     */
    public function calculateWithholdingTax($monthlySalary, $allowances = 0, $mandatoryDeductions = 0)
    {
        // Use new BIR tax calculator with progressive tax brackets
        // Note: mandatoryDeductions is passed as a total, but TaxCalculationService expects an array
        // We'll approximate by passing it as 'gsis' since it's deductible anyway
        $deductions = [
            'gsis' => $mandatoryDeductions,
            'philhealth' => 0,
            'pagibig' => 0,
        ];

        // Extract actual deduction amounts from mandatory deductions
        // This is an estimate based on standard rates
        $taxResult = $this->taxCalculationService->calculateMonthlyTax(
            $monthlySalary,
            $deductions,
            0,  // No overtime for tax calculation
            $allowances
        );

        return round($taxResult['withholding_tax'], 2);
    }

    /**
     * Get detailed tax calculation with breakdown
     */
    public function getDetailedTaxCalculation($monthlySalary, $allowances = 0, $mandatoryDeductions = 0)
    {
        $deductions = [
            'gsis' => $mandatoryDeductions,
            'philhealth' => 0,
            'pagibig' => 0,
        ];

        return $this->taxCalculationService->calculateMonthlyTax(
            $monthlySalary,
            $deductions,
            0,
            $allowances
        );
    }

    /**
     * Calculate other deductions (loans, advances, etc.)
     */
    public function calculateOtherDeductions(Employee $employee, PayrollPeriod $period)
    {
        $totalOtherDeductions = 0;

        // Get active deductions for this employee (excluding government mandated)
        $activeDeductions = $employee->deductions()
            ->whereHas('deductionType', function($query) {
                $query->where('name', '!=', 'Government Mandated');
            })
            ->wherePivot('effective_from', '<=', $period->end_date)
            ->where(function($query) use ($period) {
                $query->whereNull('tbl_employee_deductions.effective_to')
                      ->orWhere('tbl_employee_deductions.effective_to', '>=', $period->start_date);
            })
            ->get();

        foreach ($activeDeductions as $deduction) {
            // Use custom amount if set, otherwise use default amount
            $amount = $deduction->pivot->custom_amount ?? $deduction->amount;
            $totalOtherDeductions += $amount;
        }

        return round($totalOtherDeductions, 2);
    }

    /**
     * Calculate holiday pay
     */
    public function calculateHolidayPay(Employee $employee, PayrollPeriod $period)
    {
        $basicSalary = $this->resolveMonthlySalary($employee);
        $hourlyRate = $this->calculateHourlyRate($basicSalary);

        // Get DTR entries for holidays within the period
        $holidayEntries = DtrEntry::where('employee_id', $employee->id)
            ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
            ->where('is_holiday', true)
            ->get();

        $holidayPay = 0;

        foreach ($holidayEntries as $dtr) {
            // Fix: Calculate total hours from both hours and remaining minutes
            $totalMinutes = ($dtr->total_hours * self::MINUTES_PER_HOUR) + ($dtr->total_minutes ?? 0);
            $hoursWorked = min($totalMinutes / self::MINUTES_PER_HOUR, self::HOURS_PER_DAY);

            // Regular holiday rate is 200% (already paid regular rate, so add 100% extra)
            $holidayPay += $hoursWorked * $hourlyRate * (self::REGULAR_HOLIDAY_RATE - 1);
        }

        return round($holidayPay, 2);
    }

    /**
     * Calculate night differential
     */
    public function calculateNightDifferential(Employee $employee, PayrollPeriod $period)
    {
        $basicSalary = $this->resolveMonthlySalary($employee);
        $hourlyRate = $this->calculateHourlyRate($basicSalary);

        // Get DTR entries for the period
        $dtrEntries = DtrEntry::where('employee_id', $employee->id)
            ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
            ->whereNotNull('pm_arrival')
            ->get();

        $nightDiffPay = 0;

        foreach ($dtrEntries as $dtr) {
            // Check if work extended into night shift (10 PM onwards)
            if ($dtr->pm_departure) {
                $departure = Carbon::parse($dtr->pm_departure);

                // If departure is after 10 PM or before 6 AM next day
                if ($departure->hour >= self::NIGHT_SHIFT_START || $departure->hour < self::NIGHT_SHIFT_END) {
                    // Simplified: assume 2 hours night differential if worked past 10 PM
                    // In production, you'd calculate exact night hours
                    $nightHours = 2; // Simplified
                    $nightDiffPay += $nightHours * $hourlyRate * self::NIGHT_DIFF_RATE;
                }
            }
        }

        return round($nightDiffPay, 2);
    }

    /**
     * Main method to calculate complete payroll
     */
    public function calculateCompletePayroll(Employee $employee, PayrollPeriod $period, $includeOvertime = true)
    {
        $basicSalary = $this->resolveMonthlySalary($employee);
        $hourlyRate = $this->calculateHourlyRate($basicSalary);

        // Calculate overtime
        $overtimeData = $includeOvertime ? $this->calculateOvertimePay($employee, $period) : [
            'total_overtime_pay' => 0,
            'regular_overtime_hours' => 0,
            'regular_overtime_pay' => 0,
            'restday_overtime_hours' => 0,
            'restday_overtime_pay' => 0,
            'holiday_overtime_hours' => 0,
            'holiday_overtime_pay' => 0,
        ];

        // Calculate allowances
        $totalAllowances = $this->calculateAllowances($employee, $period);

        // Calculate holiday pay
        $holidayPay = $this->calculateHolidayPay($employee, $period);

        // Calculate night differential
        $nightDifferential = $this->calculateNightDifferential($employee, $period);

        // Bonuses (placeholder - would be set manually or from another source)
        $bonuses = 0;

        // Calculate gross pay
        $grossPay = $basicSalary
                  + $overtimeData['total_overtime_pay']
                  + $totalAllowances
                  + $holidayPay
                  + $nightDifferential
                  + $bonuses;

        // Calculate government deductions
        $gsisContribution = $this->calculateGSISContribution($basicSalary);
        $philhealthContribution = $this->calculatePhilHealthContribution($basicSalary);
        $pagibigContribution = $this->calculatePagIbigContribution($basicSalary);

        $mandatoryDeductions = $gsisContribution + $philhealthContribution + $pagibigContribution;

        // Calculate withholding tax
        $withholdingTax = $this->calculateWithholdingTax($basicSalary, $totalAllowances, $mandatoryDeductions);

        // Calculate attendance deductions
        $attendanceDeductions = $this->calculateAttendanceDeductions($employee, $period);

        // Calculate other deductions
        $otherDeductions = $this->calculateOtherDeductions($employee, $period);

        // Calculate total deductions
        $totalDeductions = $gsisContribution
                         + $philhealthContribution
                         + $pagibigContribution
                         + $withholdingTax
                         + $attendanceDeductions['late_deductions']
                         + $attendanceDeductions['absent_deductions']
                         + $attendanceDeductions['undertime_deductions']
                         + $otherDeductions;

        // Calculate net pay
        $netPay = max(0, $grossPay - $totalDeductions);

        return [
            'payroll_period_id' => $period->id,
            'basic_salary' => round($basicSalary, 2),
            'hourly_rate' => round($hourlyRate, 2),
            'overtime_pay' => round($overtimeData['total_overtime_pay'], 2),
            'regular_overtime_hours' => $overtimeData['regular_overtime_hours'],
            'regular_overtime_pay' => $overtimeData['regular_overtime_pay'],
            'restday_overtime_hours' => $overtimeData['restday_overtime_hours'],
            'restday_overtime_pay' => $overtimeData['restday_overtime_pay'],
            'holiday_overtime_hours' => $overtimeData['holiday_overtime_hours'],
            'holiday_overtime_pay' => $overtimeData['holiday_overtime_pay'],
            'total_allowances' => round($totalAllowances, 2),
            'holiday_pay' => round($holidayPay, 2),
            'night_differential' => round($nightDifferential, 2),
            'bonuses' => round($bonuses, 2),
            'gross_pay' => round($grossPay, 2),
            'gsis_contribution' => round($gsisContribution, 2),
            'philhealth_contribution' => round($philhealthContribution, 2),
            'pagibig_contribution' => round($pagibigContribution, 2),
            'withholding_tax' => round($withholdingTax, 2),
            'late_deductions' => $attendanceDeductions['late_deductions'],
            'absent_deductions' => $attendanceDeductions['absent_deductions'],
            'undertime_deductions' => $attendanceDeductions['undertime_deductions'],
            'other_deductions' => round($otherDeductions, 2),
            'total_deductions' => round($totalDeductions, 2),
            'net_pay' => round($netPay, 2),
        ];
    }
}
