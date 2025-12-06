<?php

namespace App\Services;

use App\Models\DtrEntry;
use App\Models\PayrollPeriod;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DtrToPayrollService
{
    protected $payrollService;

    public function __construct(PayrollCalculationService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Validate DTR data for a payroll period before generating payroll
     */
    public function validateDtrForPayroll(PayrollPeriod $period): array
    {
        $workDays = $this->getExpectedWorkDays($period);
        
        // Get all active employees
        $employees = Employee::whereHas('jobStatus', function($q) { 
            $q->where('name', 'Active'); 
        })->get();
        
        // Fallback: if no employees found with 'Active' status name, try getting all employees
        // This is useful if the seeder/setup didn't set the status name correctly
        if ($employees->isEmpty()) {
            $employees = Employee::all();
        }
        
        $validation = [
            'total_employees' => $employees->count(),
            'complete_count' => 0,
            'incomplete_count' => 0,
            'missing_count' => 0,
            'incomplete_employees' => [],
            'missing_employees' => [],
            'issues' => [],
        ];

        foreach ($employees as $employee) {
            $dtrCount = DtrEntry::where('employee_id', $employee->id)
                ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
                ->where('is_weekend', false)
                ->count();

            // Check if DTR entries exist for all work days (excluding weekends)
            if ($dtrCount === 0) {
                $validation['missing_count']++;
                $validation['missing_employees'][] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'message' => "No DTR entries found for payroll period",
                ];
            } elseif ($dtrCount < $workDays) {
                $validation['incomplete_count']++;
                $validation['incomplete_employees'][] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'expected' => $workDays,
                    'found' => $dtrCount,
                    'missing' => $workDays - $dtrCount,
                ];
            } else {
                $validation['complete_count']++;
            }

            // Check for data quality issues
            $issues = $this->checkDtrDataQuality($employee->id, $period);
            if (!empty($issues)) {
                $validation['issues'] = array_merge($validation['issues'], $issues);
            }
        }

        return $validation;
    }

    /**
     * Check DTR data quality for an employee in a period
     */
    private function checkDtrDataQuality(int $employeeId, PayrollPeriod $period): array
    {
        $issues = [];

        $dtrEntries = DtrEntry::where('employee_id', $employeeId)
            ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
            ->get();

        foreach ($dtrEntries as $dtr) {
            // Check for invalid time sequences
            if ($dtr->am_arrival && $dtr->am_departure) {
                $arrival = Carbon::parse($dtr->am_arrival);
                $departure = Carbon::parse($dtr->am_departure);
                if ($departure->lt($arrival)) {
                    $issues[] = [
                        'employee_id' => $employeeId,
                        'date' => $dtr->dtr_date,
                        'issue' => 'Invalid AM times: departure before arrival',
                        'severity' => 'critical',
                    ];
                }
            }

            if ($dtr->pm_arrival && $dtr->pm_departure) {
                $arrival = Carbon::parse($dtr->pm_arrival);
                $departure = Carbon::parse($dtr->pm_departure);
                if ($departure->lt($arrival)) {
                    $issues[] = [
                        'employee_id' => $employeeId,
                        'date' => $dtr->dtr_date,
                        'issue' => 'Invalid PM times: departure before arrival',
                        'severity' => 'critical',
                    ];
                }
            }

            // Check for missing critical fields on work days
            if (!$dtr->is_weekend && !$dtr->is_holiday && $dtr->status === 'present') {
                if (!$dtr->am_arrival || !$dtr->am_departure) {
                    $issues[] = [
                        'employee_id' => $employeeId,
                        'date' => $dtr->dtr_date,
                        'issue' => 'Missing AM time records',
                        'severity' => 'warning',
                    ];
                }
            }

            // Check for extreme values
            if ($dtr->total_hours > 16) {
                $issues[] = [
                    'employee_id' => $employeeId,
                    'date' => $dtr->dtr_date,
                    'issue' => "Unusually high hours: {$dtr->total_hours} hours",
                    'severity' => 'warning',
                ];
            }
        }

        return $issues;
    }

    /**
     * Generate payroll for a period (with DTR validation)
     */
    public function generatePayrollFromDtr(PayrollPeriod $period, ?callable $onProgress = null): array
    {
        Log::info("Starting payroll generation for period: {$period->start_date} to {$period->end_date}");

        // Validate DTR data first
        $validation = $this->validateDtrForPayroll($period);

        if ($validation['missing_count'] > 0 || $validation['incomplete_count'] > 0) {
            Log::warning("DTR validation issues found", [
                'missing' => $validation['missing_count'],
                'incomplete' => $validation['incomplete_count'],
            ]);
        }

        $employees = Employee::whereHas('jobStatus', function($q) { $q->where('name', 'Active'); })->get();
        $totalEmployees = $employees->count();
        $generatedPayrolls = [];
        $failedEmployees = [];
        $processedCount = 0;

        foreach ($employees as $employee) {
            $processedCount++;
            
            if ($onProgress) {
                $onProgress($processedCount, $totalEmployees, $employee);
            }

            try {
                $payrollData = $this->payrollService->calculateCompletePayroll($employee, $period);
                
                $payroll = Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'payroll_period_id' => $period->id,
                    ],
                    [
                        'basic_salary' => $payrollData['basic_salary'],
                        'hourly_rate' => $payrollData['hourly_rate'],
                        'overtime_pay' => $payrollData['overtime_pay'],
                        'regular_overtime_hours' => $payrollData['regular_overtime_hours'],
                        'regular_overtime_pay' => $payrollData['regular_overtime_pay'],
                        'restday_overtime_hours' => $payrollData['restday_overtime_hours'],
                        'restday_overtime_pay' => $payrollData['restday_overtime_pay'],
                        'holiday_overtime_hours' => $payrollData['holiday_overtime_hours'],
                        'holiday_overtime_pay' => $payrollData['holiday_overtime_pay'],
                        'total_allowances' => $payrollData['total_allowances'],
                        'holiday_pay' => $payrollData['holiday_pay'],
                        'night_differential' => $payrollData['night_differential'],
                        'bonuses' => $payrollData['bonuses'],
                        'gross_pay' => $payrollData['gross_pay'],
                        'gsis_contribution' => $payrollData['gsis_contribution'],
                        'philhealth_contribution' => $payrollData['philhealth_contribution'],
                        'pagibig_contribution' => $payrollData['pagibig_contribution'],
                        'withholding_tax' => $payrollData['withholding_tax'],
                        'late_deductions' => $payrollData['late_deductions'],
                        'absent_deductions' => $payrollData['absent_deductions'],
                        'undertime_deductions' => $payrollData['undertime_deductions'],
                        'other_deductions' => $payrollData['other_deductions'],
                        'total_deductions' => $payrollData['total_deductions'],
                        'net_pay' => $payrollData['net_pay'],
                        'status' => 'generated',
                    ]
                );

                $generatedPayrolls[] = [
                    'employee_id' => $employee->id,
                    'payroll_id' => $payroll->id,
                    'status' => 'success',
                    'net_pay' => $payrollData['net_pay'],
                ];

                Log::info("Payroll generated for employee {$employee->id}: ₱{$payrollData['net_pay']}");

            } catch (\Exception $e) {
                $failedEmployees[] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'error' => $e->getMessage(),
                ];

                Log::error("Payroll generation failed for employee {$employee->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $result = [
            'period' => [
                'start_date' => $period->start_date,
                'end_date' => $period->end_date,
            ],
            'validation' => $validation,
            'generated_count' => count($generatedPayrolls),
            'failed_count' => count($failedEmployees),
            'generated_payrolls' => $generatedPayrolls,
            'failed_employees' => $failedEmployees,
            'total_gross_pay' => array_sum(array_column($generatedPayrolls, 'net_pay')),
        ];

        Log::info("Payroll generation completed", [
            'generated' => count($generatedPayrolls),
            'failed' => count($failedEmployees),
        ]);

        return $result;
    }

    /**
     * Get summary of DTR data for a payroll period
     */
    public function getDtrSummaryForPayroll(PayrollPeriod $period): array
    {
        $workDays = $this->getExpectedWorkDays($period);
        $employees = Employee::whereHas('jobStatus', function($q) { $q->where('name', 'Active'); })->get();

        $summary = [
            'period' => [
                'start_date' => $period->start_date,
                'end_date' => $period->end_date,
                'expected_work_days' => $workDays,
            ],
            'employees_summary' => [],
            'totals' => [
                'total_employees' => $employees->count(),
                'total_hours_worked' => 0,
                'total_overtime_hours' => 0,
                'total_undertime_minutes' => 0,
                'absent_count' => 0,
                'late_count' => 0,
            ],
        ];

        foreach ($employees as $employee) {
            $dtrEntries = DtrEntry::where('employee_id', $employee->id)
                ->whereBetween('dtr_date', [$period->start_date, $period->end_date])
                ->get();

            $employeeSummary = [
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'total_hours' => 0,
                'total_minutes' => 0,
                'overtime_hours' => 0,
                'undertime_minutes' => 0,
                'absent_days' => 0,
                'late_days' => 0,
                'holiday_days' => 0,
                'weekend_days' => 0,
                'total_days' => $dtrEntries->count(),
                'present_days' => 0,
                'status' => 'complete',
            ];

            $workDaysCount = 0;

            foreach ($dtrEntries as $dtr) {
                $employeeSummary['total_hours'] += $dtr->total_hours;
                $employeeSummary['total_minutes'] += $dtr->total_minutes;
                $employeeSummary['undertime_minutes'] += $dtr->under_time_minutes;

                if (!$dtr->is_weekend) {
                    $workDaysCount++;
                }

                if ($dtr->is_holiday) {
                    $employeeSummary['holiday_days']++;
                } elseif ($dtr->is_weekend) {
                    $employeeSummary['weekend_days']++;
                } elseif ($dtr->status === 'absent') {
                    $employeeSummary['absent_days']++;
                } else {
                    // Assuming if not absent, holiday or weekend, it is present
                    // Or check status explicitly if needed
                    $employeeSummary['present_days']++;
                }

                if ($dtr->status === 'late') {
                    $employeeSummary['late_days']++;
                }
            }

            if ($workDaysCount < $workDays) {
                $employeeSummary['status'] = 'incomplete';
            }

            // Convert minutes to hours for overtime calculation
            $totalMinutes = ($employeeSummary['total_hours'] * 60) + $employeeSummary['total_minutes'];
            if ($totalMinutes > 480) { // 8 hours
                $employeeSummary['overtime_hours'] = round(($totalMinutes - 480) / 60, 2);
            }

            $summary['employees_summary'][] = $employeeSummary;

            // Update totals
            $summary['totals']['total_hours_worked'] += $employeeSummary['total_hours'];
            $summary['totals']['total_overtime_hours'] += $employeeSummary['overtime_hours'];
            $summary['totals']['total_undertime_minutes'] += $employeeSummary['undertime_minutes'];
            $summary['totals']['absent_count'] += $employeeSummary['absent_days'];
            $summary['totals']['late_count'] += $employeeSummary['late_days'];
        }

        return $summary;
    }

    /**
     * Calculate expected work days (excluding weekends and holidays)
     */
    private function getExpectedWorkDays(PayrollPeriod $period): int
    {
        $workDays = 0;
        $current = $period->start_date->copy();

        while ($current <= $period->end_date) {
            if (!$current->isWeekend()) {
                // Check if it's a holiday
                $isHoliday = DB::table('tbl_holidays')
                    ->whereDate('date', $current)
                    ->exists();

                if (!$isHoliday) {
                    $workDays++;
                }
            }
            $current->addDay();
        }

        return $workDays;
    }

    /**
     * Recalculate payroll for an employee after DTR adjustments
     */
    public function recalculatePayrollFromDtr(Employee $employee, PayrollPeriod $period): array
    {
        Log::info("Recalculating payroll for employee {$employee->id} for period {$period->start_date} to {$period->end_date}");

        $issues = $this->checkDtrDataQuality($employee->id, $period);

        if (!empty($issues)) {
            Log::warning("Data quality issues found during recalculation", ['issues' => $issues]);
        }

        $payrollData = $this->payrollService->calculateCompletePayroll($employee, $period);

        $payroll = Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'payroll_period_id' => $period->id,
            ],
            [
                'basic_salary' => $payrollData['basic_salary'],
                'hourly_rate' => $payrollData['hourly_rate'],
                'overtime_pay' => $payrollData['overtime_pay'],
                'total_allowances' => $payrollData['total_allowances'],
                'holiday_pay' => $payrollData['holiday_pay'],
                'night_differential' => $payrollData['night_differential'],
                'gross_pay' => $payrollData['gross_pay'],
                'sss_contribution' => $payrollData['sss_contribution'],
                'philhealth_contribution' => $payrollData['philhealth_contribution'],
                'pagibig_contribution' => $payrollData['pagibig_contribution'],
                'withholding_tax' => $payrollData['withholding_tax'],
                'late_deductions' => $payrollData['late_deductions'],
                'absent_deductions' => $payrollData['absent_deductions'],
                'undertime_deductions' => $payrollData['undertime_deductions'],
                'other_deductions' => $payrollData['other_deductions'],
                'total_deductions' => $payrollData['total_deductions'],
                'net_pay' => $payrollData['net_pay'],
                'status' => 'recalculated',
            ]
        );

        Log::info("Payroll recalculated successfully. New net pay: ₱{$payrollData['net_pay']}");

        return [
            'payroll_id' => $payroll->id,
            'net_pay' => $payrollData['net_pay'],
            'status' => 'recalculated',
            'data_quality_issues' => $issues,
        ];
    }
}
