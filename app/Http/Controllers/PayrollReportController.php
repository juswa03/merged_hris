<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollReportController extends Controller
{
    /**
     * Display payroll reports dashboard
     */
    public function index()
    {
        // Get available payroll periods
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->take(12)->get();

        // Current month stats
        $currentMonth = now()->format('Y-m');
        $currentPeriod = PayrollPeriod::whereYear('start_date', now()->year)
            ->whereMonth('start_date', now()->month)
            ->first();

        $stats = [
            'current_month_payroll' => 0,
            'current_month_employees' => 0,
            'total_ytd_payroll' => 0,
            'avg_employee_salary' => 0,
        ];

        if ($currentPeriod) {
            $currentPayrolls = Payroll::where('payroll_period_id', $currentPeriod->id)->get();
            $stats['current_month_payroll'] = $currentPayrolls->sum('net_pay');
            $stats['current_month_employees'] = $currentPayrolls->count();
        }

        // YTD calculations
        $ytdPayrolls = Payroll::whereHas('payrollPeriod', function($q) {
            $q->whereYear('start_date', now()->year);
        })->get();

        $stats['total_ytd_payroll'] = $ytdPayrolls->sum('net_pay');
        $stats['avg_employee_salary'] = $ytdPayrolls->avg('net_pay') ?? 0;

        return view('admin.reports.payroll.index', compact('periods', 'stats'));
    }

    /**
     * Monthly payroll summary report
     */
    public function monthlySummary(Request $request)
    {
        $periodId = $request->get('period_id');
        $period = null;
        $payrolls = collect();

        if ($periodId) {
            $period = PayrollPeriod::findOrFail($periodId);
            $payrolls = Payroll::where('payroll_period_id', $periodId)
                ->with(['employee.department', 'employee.position'])
                ->orderBy('employee_id')
                ->get();
        }

        // Calculate summary
        $summary = [
            'total_employees' => $payrolls->count(),
            'total_gross_pay' => $payrolls->sum('gross_pay'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'total_net_pay' => $payrolls->sum('net_pay'),
            'total_overtime' => $payrolls->sum('overtime_pay'),
            'total_allowances' => $payrolls->sum('total_allowances'),
            'total_sss' => $payrolls->sum('sss_contribution'),
            'total_philhealth' => $payrolls->sum('philhealth_contribution'),
            'total_pagibig' => $payrolls->sum('pagibig_contribution'),
            'total_tax' => $payrolls->sum('withholding_tax'),
            'avg_gross_pay' => $payrolls->avg('gross_pay') ?? 0,
            'avg_net_pay' => $payrolls->avg('net_pay') ?? 0,
        ];

        $periods = PayrollPeriod::orderBy('start_date', 'desc')->get();

        return view('admin.reports.payroll.monthly-summary', compact('period', 'payrolls', 'summary', 'periods'));
    }

    /**
     * Department-wise payroll breakdown
     */
    public function departmentBreakdown(Request $request)
    {
        $periodId = $request->get('period_id');
        $period = null;
        $departmentData = collect();

        if ($periodId) {
            $period = PayrollPeriod::findOrFail($periodId);

            // Get payrolls grouped by department
            $payrolls = Payroll::where('payroll_period_id', $periodId)
                ->with(['employee.department'])
                ->get();

            $departmentData = $payrolls->groupBy('employee.department.name')->map(function($deptPayrolls, $deptName) {
                return [
                    'department' => $deptName ?? 'No Department',
                    'employee_count' => $deptPayrolls->count(),
                    'total_gross' => $deptPayrolls->sum('gross_pay'),
                    'total_deductions' => $deptPayrolls->sum('total_deductions'),
                    'total_net' => $deptPayrolls->sum('net_pay'),
                    'avg_gross' => $deptPayrolls->avg('gross_pay'),
                    'avg_net' => $deptPayrolls->avg('net_pay'),
                    'total_overtime' => $deptPayrolls->sum('overtime_pay'),
                    'total_allowances' => $deptPayrolls->sum('total_allowances'),
                ];
            })->sortByDesc('total_net')->values();
        }

        $periods = PayrollPeriod::orderBy('start_date', 'desc')->get();

        return view('admin.reports.payroll.department-breakdown', compact('period', 'departmentData', 'periods'));
    }

    /**
     * Government contributions report
     */
    public function governmentContributions(Request $request)
    {
        $periodId = $request->get('period_id');
        $period = null;
        $contributions = collect();

        if ($periodId) {
            $period = PayrollPeriod::findOrFail($periodId);

            $contributions = Payroll::where('payroll_period_id', $periodId)
                ->with(['employee'])
                ->orderBy('employee_id')
                ->get();
        }

        // Calculate totals
        $totals = [
            'total_gsis' => $contributions->sum('gsis_contribution'),
            'total_philhealth' => $contributions->sum('philhealth_contribution'),
            'total_pagibig' => $contributions->sum('pagibig_contribution'),
            'total_tax' => $contributions->sum('withholding_tax'),
            'grand_total' => $contributions->sum('gsis_contribution') +
                           $contributions->sum('philhealth_contribution') +
                           $contributions->sum('pagibig_contribution') +
                           $contributions->sum('withholding_tax'),
        ];

        $periods = PayrollPeriod::orderBy('start_date', 'desc')->get();

        return view('admin.reports.payroll.government-contributions', compact('period', 'contributions', 'totals', 'periods'));
    }

    /**
     * Year-to-date earnings report
     */
    public function ytdEarnings(Request $request)
    {
        $year = $request->get('year', now()->year);

        // Get all employees with their YTD data
        $employees = Employee::with(['department', 'position'])
            ->whereHas('payrolls.payrollPeriod', function($q) use ($year) {
                $q->whereYear('start_date', $year);
            })
            ->get();

        $ytdData = $employees->map(function($employee) use ($year) {
            $payrolls = $employee->payrolls()
                ->whereHas('payrollPeriod', function($q) use ($year) {
                    $q->whereYear('start_date', $year);
                })
                ->get();

            return [
                'employee' => $employee,
                'total_gross' => $payrolls->sum('gross_pay'),
                'total_deductions' => $payrolls->sum('total_deductions'),
                'total_net' => $payrolls->sum('net_pay'),
                'total_sss' => $payrolls->sum('sss_contribution'),
                'total_philhealth' => $payrolls->sum('philhealth_contribution'),
                'total_pagibig' => $payrolls->sum('pagibig_contribution'),
                'total_tax' => $payrolls->sum('withholding_tax'),
                'total_overtime' => $payrolls->sum('overtime_pay'),
                'total_allowances' => $payrolls->sum('total_allowances'),
                'payroll_count' => $payrolls->count(),
            ];
        })->sortByDesc('total_net');

        // Grand totals
        $grandTotals = [
            'total_gross' => $ytdData->sum('total_gross'),
            'total_deductions' => $ytdData->sum('total_deductions'),
            'total_net' => $ytdData->sum('total_net'),
            'total_sss' => $ytdData->sum('total_sss'),
            'total_philhealth' => $ytdData->sum('total_philhealth'),
            'total_pagibig' => $ytdData->sum('total_pagibig'),
            'total_tax' => $ytdData->sum('total_tax'),
        ];

        $availableYears = PayrollPeriod::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.reports.payroll.ytd-earnings', compact('ytdData', 'grandTotals', 'year', 'availableYears'));
    }

    /**
     * Deductions and allowances summary
     */
    public function deductionsAllowances(Request $request)
    {
        $periodId = $request->get('period_id');
        $period = null;
        $data = collect();

        if ($periodId) {
            $period = PayrollPeriod::findOrFail($periodId);

            $data = Payroll::where('payroll_period_id', $periodId)
                ->with(['employee'])
                ->orderBy('employee_id')
                ->get();
        }

        // Calculate totals
        $totals = [
            'total_allowances' => $data->sum('total_allowances'),
            'total_deductions' => $data->sum('total_deductions'),
            'total_late' => $data->sum('late_deductions'),
            'total_absent' => $data->sum('absent_deductions'),
            'total_undertime' => $data->sum('undertime_deductions'),
            'total_other_deductions' => $data->sum('other_deductions'),
            'net_effect' => $data->sum('total_allowances') - $data->sum('total_deductions'),
        ];

        $periods = PayrollPeriod::orderBy('start_date', 'desc')->get();

        return view('admin.reports.payroll.deductions-allowances', compact('period', 'data', 'totals', 'periods'));
    }

    /**
     * Export monthly summary to CSV
     */
    public function exportMonthlySummaryCSV(Request $request)
    {
        $periodId = $request->get('period_id');

        if (!$periodId) {
            return back()->with('error', 'Please select a payroll period');
        }

        $period = PayrollPeriod::findOrFail($periodId);
        $payrolls = Payroll::where('payroll_period_id', $periodId)
            ->with(['employee.department', 'employee.position'])
            ->orderBy('employee_id')
            ->get();

        $filename = 'monthly_payroll_summary_' . $period->start_date->format('Y-m') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payrolls, $period) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['Monthly Payroll Summary Report']);
            fputcsv($file, ['Period: ' . $period->formatted_period]);
            fputcsv($file, []);

            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Department',
                'Position',
                'Basic Salary',
                'Overtime Pay',
                'Allowances',
                'Gross Pay',
                'SSS',
                'PhilHealth',
                'Pag-IBIG',
                'Withholding Tax',
                'Other Deductions',
                'Total Deductions',
                'Net Pay'
            ]);

            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $payroll->employee->id,
                    $payroll->employee->full_name,
                    $payroll->employee->department->name ?? 'N/A',
                    $payroll->employee->position->name ?? 'N/A',
                    number_format($payroll->basic_salary, 2),
                    number_format($payroll->overtime_pay, 2),
                    number_format($payroll->total_allowances, 2),
                    number_format($payroll->gross_pay, 2),
                    number_format($payroll->gsis_contribution, 2),
                    number_format($payroll->philhealth_contribution, 2),
                    number_format($payroll->pagibig_contribution, 2),
                    number_format($payroll->withholding_tax, 2),
                    number_format($payroll->other_deductions, 2),
                    number_format($payroll->total_deductions, 2),
                    number_format($payroll->net_pay, 2),
                ]);
            }

            // Totals
            fputcsv($file, []);
            fputcsv($file, [
                '',
                '',
                '',
                'TOTALS:',
                number_format($payrolls->sum('basic_salary'), 2),
                number_format($payrolls->sum('overtime_pay'), 2),
                number_format($payrolls->sum('total_allowances'), 2),
                number_format($payrolls->sum('gross_pay'), 2),
                number_format($payrolls->sum('gsis_contribution'), 2),
                number_format($payrolls->sum('philhealth_contribution'), 2),
                number_format($payrolls->sum('pagibig_contribution'), 2),
                number_format($payrolls->sum('withholding_tax'), 2),
                number_format($payrolls->sum('other_deductions'), 2),
                number_format($payrolls->sum('total_deductions'), 2),
                number_format($payrolls->sum('net_pay'), 2),
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export government contributions to CSV
     */
    public function exportGovernmentContributionsCSV(Request $request)
    {
        $periodId = $request->get('period_id');

        if (!$periodId) {
            return back()->with('error', 'Please select a payroll period');
        }

        $period = PayrollPeriod::findOrFail($periodId);
        $contributions = Payroll::where('payroll_period_id', $periodId)
            ->with(['employee'])
            ->orderBy('employee_id')
            ->get();

        $filename = 'government_contributions_' . $period->start_date->format('Y-m') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($contributions, $period) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Government Contributions Report']);
            fputcsv($file, ['Period: ' . $period->formatted_period]);
            fputcsv($file, []);

            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'GSIS Number',
                'PhilHealth Number',
                'Pag-IBIG Number',
                'GSIS Contribution',
                'PhilHealth Contribution',
                'Pag-IBIG Contribution',
                'Withholding Tax',
                'Total'
            ]);

            foreach ($contributions as $contrib) {
                $total = $contrib->gsis_contribution + $contrib->philhealth_contribution +
                        $contrib->pagibig_contribution + $contrib->withholding_tax;

                fputcsv($file, [
                    $contrib->employee->id,
                    $contrib->employee->full_name,
                    $contrib->employee->gsis_number ?? 'N/A',
                    $contrib->employee->philhealth_number ?? 'N/A',
                    $contrib->employee->pagibig_number ?? 'N/A',
                    number_format($contrib->gsis_contribution, 2),
                    number_format($contrib->philhealth_contribution, 2),
                    number_format($contrib->pagibig_contribution, 2),
                    number_format($contrib->withholding_tax, 2),
                    number_format($total, 2),
                ]);
            }

            // Totals
            fputcsv($file, []);
            fputcsv($file, [
                '',
                '',
                '',
                '',
                'TOTALS:',
                number_format($contributions->sum('gsis_contribution'), 2),
                number_format($contributions->sum('philhealth_contribution'), 2),
                number_format($contributions->sum('pagibig_contribution'), 2),
                number_format($contributions->sum('withholding_tax'), 2),
                number_format($contributions->sum('sss_contribution') + $contributions->sum('philhealth_contribution') +
                             $contributions->sum('pagibig_contribution') + $contributions->sum('withholding_tax'), 2),
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export YTD earnings to CSV
     */
    public function exportYTDEarningsCSV(Request $request)
    {
        $year = $request->get('year', now()->year);

        $employees = Employee::with(['department', 'position'])
            ->whereHas('payrolls.payrollPeriod', function($q) use ($year) {
                $q->whereYear('start_date', $year);
            })
            ->get();

        $filename = 'ytd_earnings_' . $year . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees, $year) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Year-to-Date Earnings Report']);
            fputcsv($file, ['Year: ' . $year]);
            fputcsv($file, []);

            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Department',
                'Position',
                'Total Gross Pay',
                'Total Allowances',
                'Total Overtime',
                'Total Deductions',
                'Total SSS',
                'Total PhilHealth',
                'Total Pag-IBIG',
                'Total Tax',
                'Total Net Pay',
                'Payroll Count'
            ]);

            $grandTotals = [
                'gross' => 0,
                'allowances' => 0,
                'overtime' => 0,
                'deductions' => 0,
                'sss' => 0,
                'philhealth' => 0,
                'pagibig' => 0,
                'tax' => 0,
                'net' => 0,
            ];

            foreach ($employees as $employee) {
                $payrolls = $employee->payrolls()
                    ->whereHas('payrollPeriod', function($q) use ($year) {
                        $q->whereYear('start_date', $year);
                    })
                    ->get();

                $totalGross = $payrolls->sum('gross_pay');
                $totalAllowances = $payrolls->sum('total_allowances');
                $totalOvertime = $payrolls->sum('overtime_pay');
                $totalDeductions = $payrolls->sum('total_deductions');
                $totalSSS = $payrolls->sum('sss_contribution');
                $totalPhilHealth = $payrolls->sum('philhealth_contribution');
                $totalPagIBIG = $payrolls->sum('pagibig_contribution');
                $totalTax = $payrolls->sum('withholding_tax');
                $totalNet = $payrolls->sum('net_pay');

                $grandTotals['gross'] += $totalGross;
                $grandTotals['allowances'] += $totalAllowances;
                $grandTotals['overtime'] += $totalOvertime;
                $grandTotals['deductions'] += $totalDeductions;
                $grandTotals['sss'] += $totalSSS;
                $grandTotals['philhealth'] += $totalPhilHealth;
                $grandTotals['pagibig'] += $totalPagIBIG;
                $grandTotals['tax'] += $totalTax;
                $grandTotals['net'] += $totalNet;

                fputcsv($file, [
                    $employee->id,
                    $employee->full_name,
                    $employee->department->name ?? 'N/A',
                    $employee->position->name ?? 'N/A',
                    number_format($totalGross, 2),
                    number_format($totalAllowances, 2),
                    number_format($totalOvertime, 2),
                    number_format($totalDeductions, 2),
                    number_format($totalSSS, 2),
                    number_format($totalPhilHealth, 2),
                    number_format($totalPagIBIG, 2),
                    number_format($totalTax, 2),
                    number_format($totalNet, 2),
                    $payrolls->count(),
                ]);
            }

            // Grand totals
            fputcsv($file, []);
            fputcsv($file, [
                '',
                '',
                '',
                'GRAND TOTALS:',
                number_format($grandTotals['gross'], 2),
                number_format($grandTotals['allowances'], 2),
                number_format($grandTotals['overtime'], 2),
                number_format($grandTotals['deductions'], 2),
                number_format($grandTotals['sss'], 2),
                number_format($grandTotals['philhealth'], 2),
                number_format($grandTotals['pagibig'], 2),
                number_format($grandTotals['tax'], 2),
                number_format($grandTotals['net'], 2),
                '',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
