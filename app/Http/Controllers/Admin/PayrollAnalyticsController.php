<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // Key metrics for the selected year
        $baseQuery = Payroll::whereHas('payrollPeriod', function ($q) use ($year) {
            $q->whereYear('start_date', $year);
        });

        $keyMetrics = [
            'total_gross_income'  => (clone $baseQuery)->sum('gross_pay'),
            'total_net_income'    => (clone $baseQuery)->sum('net_pay'),
            'total_tax_withheld'  => (clone $baseQuery)->sum('withholding_tax'),
            'total_employees'     => (clone $baseQuery)->distinct('employee_id')->count('employee_id'),
            'total_sss'           => (clone $baseQuery)->sum('gsis_contribution'),
            'total_philhealth'    => (clone $baseQuery)->sum('philhealth_contribution'),
            'total_pagibig'       => (clone $baseQuery)->sum('pagibig_contribution'),
            'total_payrolls'      => (clone $baseQuery)->count(),
            'deduction_rate'      => 0,
            'effective_tax_rate'  => 0,
            'average_salary'      => (clone $baseQuery)->avg('basic_salary') ?? 0,
        ];

        $grossIncome = $keyMetrics['total_gross_income'] ?: 1;
        $keyMetrics['deduction_rate'] = round(
            (($keyMetrics['total_sss'] + $keyMetrics['total_philhealth'] + $keyMetrics['total_pagibig'] + $keyMetrics['total_tax_withheld']) / $grossIncome) * 100,
            2
        );
        $keyMetrics['effective_tax_rate'] = round(($keyMetrics['total_tax_withheld'] / $grossIncome) * 100, 2);

        // Monthly trends
        $monthlyTrends = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthQuery = Payroll::whereHas('payrollPeriod', function ($q) use ($year, $m) {
                $q->whereYear('start_date', $year)->whereMonth('start_date', $m);
            });

            $monthlyTrends[] = [
                'month'        => date('M', mktime(0, 0, 0, $m, 1)),
                'gross_income' => (clone $monthQuery)->sum('gross_pay'),
                'net_income'   => (clone $monthQuery)->sum('net_pay'),
                'tax_withheld' => (clone $monthQuery)->sum('withholding_tax'),
            ];
        }

        // Salary distribution brackets
        $salaryDistribution = [
            ['range' => 'Below ₱20,000', 'min' => 0, 'max' => 19999, 'count' => 0],
            ['range' => '₱20,000 - ₱40,000', 'min' => 20000, 'max' => 39999, 'count' => 0],
            ['range' => '₱40,000 - ₱60,000', 'min' => 40000, 'max' => 59999, 'count' => 0],
            ['range' => '₱60,000 - ₱80,000', 'min' => 60000, 'max' => 79999, 'count' => 0],
            ['range' => 'Above ₱80,000', 'min' => 80000, 'max' => PHP_INT_MAX, 'count' => 0],
        ];

        foreach ($salaryDistribution as &$bracket) {
            $bracket['count'] = (clone $baseQuery)
                ->whereBetween('basic_salary', [$bracket['min'], $bracket['max']])
                ->distinct('employee_id')
                ->count('employee_id');
        }

        // Department breakdown
        $departmentBreakdown = Department::select('tbl_departments.name')
            ->join('tbl_employee', 'tbl_employee.department_id', '=', 'tbl_departments.id')
            ->join('tbl_payrolls', 'tbl_payrolls.employee_id', '=', 'tbl_employee.id')
            ->join('tbl_payroll_periods', 'tbl_payrolls.payroll_period_id', '=', 'tbl_payroll_periods.id')
            ->whereYear('tbl_payroll_periods.start_date', $year)
            ->groupBy('tbl_departments.id', 'tbl_departments.name')
            ->selectRaw('
                tbl_departments.name as department,
                COUNT(DISTINCT tbl_employee.id) as employee_count,
                SUM(tbl_payrolls.gross_pay) as gross_income,
                SUM(tbl_payrolls.net_pay) as net_income,
                SUM(tbl_payrolls.withholding_tax) as tax_withheld,
                AVG(tbl_payrolls.basic_salary) as average_salary
            ')
            ->get()
            ->map(fn($d) => $d->toArray())
            ->toArray();

        // Top earners
        $topEarners = Payroll::select('employee_id')
            ->whereHas('payrollPeriod', fn($q) => $q->whereYear('start_date', $year))
            ->with(['employee.department'])
            ->groupBy('employee_id')
            ->selectRaw('employee_id, SUM(gross_pay) as total_gross, SUM(net_pay) as total_net, COUNT(*) as payroll_count')
            ->orderByDesc('total_gross')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'employee_name' => $p->employee?->full_name ?? 'Unknown',
                    'department'    => $p->employee?->department?->name ?? 'N/A',
                    'total_gross'   => $p->total_gross,
                    'total_net'     => $p->total_net,
                    'payroll_count' => $p->payroll_count,
                ];
            })
            ->toArray();

        // Deductions vs Allowances
        $totalDeductions = (clone $baseQuery)->sum('total_deductions');
        $totalAllowances = (clone $baseQuery)->sum('total_allowances');
        $deductionsAllowances = [
            'total_deductions' => $totalDeductions,
            'allowances'       => $totalAllowances,
            'deductions'       => [
                ['name' => 'SSS/GSIS',         'amount' => $keyMetrics['total_sss']],
                ['name' => 'PhilHealth',        'amount' => $keyMetrics['total_philhealth']],
                ['name' => 'Pag-IBIG',          'amount' => $keyMetrics['total_pagibig']],
                ['name' => 'Withholding Tax',   'amount' => $keyMetrics['total_tax_withheld']],
            ],
        ];

        // Year-over-year comparison
        $prevYear = $year - 1;
        $prevQuery = Payroll::whereHas('payrollPeriod', fn($q) => $q->whereYear('start_date', $prevYear));
        $prevGross = (clone $prevQuery)->sum('gross_pay');
        $prevEmployees = (clone $prevQuery)->distinct('employee_id')->count('employee_id');
        $prevAvgSalary = (clone $prevQuery)->avg('basic_salary') ?? 0;

        $yoyComparison = [
            'gross_income_change'         => $keyMetrics['total_gross_income'] - $prevGross,
            'gross_income_change_percent' => $prevGross > 0
                ? round((($keyMetrics['total_gross_income'] - $prevGross) / $prevGross) * 100, 2)
                : 0,
            'employee_count_change'       => $keyMetrics['total_employees'] - $prevEmployees,
            'average_salary_change'       => $keyMetrics['average_salary'] - $prevAvgSalary,
        ];

        return view('admin.payroll.analytics.dashboard', compact(
            'year',
            'keyMetrics',
            'monthlyTrends',
            'salaryDistribution',
            'departmentBreakdown',
            'topEarners',
            'deductionsAllowances',
            'yoyComparison'
        ));
    }

    public function exportAnalytics(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $payrolls = Payroll::with(['employee.department', 'payrollPeriod'])
            ->whereHas('payrollPeriod', fn($q) => $q->whereYear('start_date', $year))
            ->get();

        $filename = "payroll_analytics_{$year}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payrolls) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee', 'Department', 'Period', 'Gross Pay', 'Net Pay', 'Tax Withheld', 'SSS', 'PhilHealth', 'Pag-IBIG']);

            foreach ($payrolls as $p) {
                fputcsv($handle, [
                    $p->employee?->full_name ?? 'N/A',
                    $p->employee?->department?->name ?? 'N/A',
                    $p->payrollPeriod?->period_display ?? 'N/A',
                    $p->gross_pay,
                    $p->net_pay,
                    $p->withholding_tax,
                    $p->gsis_contribution,
                    $p->philhealth_contribution,
                    $p->pagibig_contribution,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

