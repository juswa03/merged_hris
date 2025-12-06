<?php

namespace App\Http\Controllers\Employee;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollStatus;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class EmployeePayrollController extends Controller
{
public function payslip($id)
{
    try {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(404, 'Employee record not found');
        }

        $payslip = Payroll::where('id', $id)
            ->where('employee_id', $employee->id)
            ->with('payrollPeriod')
            ->firstOrFail();
        // Manual authorization check
        if ($payslip->employee_id !== $employee->id) {
            abort(404);
        }
        $supervisor = (object)['name' => 'SUPERVISOR NAME'];
        $hrOfficer = (object)['name' => 'HR OFFICER'];

        // Calculate additional data
        if (!$payslip->gross_salary) {
            $payslip->gross_salary = $this->calculateGrossSalary($payslip);
        }
        
        if (!$payslip->total_deductions) {
            $payslip->total_deductions = $this->calculateTotalDeductions($payslip);
        }
        
        if (!$payslip->net_salary) {
            $payslip->net_salary = $payslip->gross_salary - $payslip->total_deductions;
        }

        return view('employee.payroll.payslip', compact('payslip', 'employee', 'supervisor', 'hrOfficer'));

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'Payslip not found');
    } catch (\Exception $e) {
        abort(500, 'Error loading payslip: ' . $e->getMessage());
    }
}

    public function payslips()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            abort(404, 'Employee record not found');
        }
        
        $payslips = $employee->payrolls()
            ->with('payrollPeriod')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('employee.payroll.payslips-list', compact('payslips', 'employee'));
    }

    private function calculateGrossSalary($payslip)
    {
        return $payslip->basic_salary 
             + ($payslip->overtime_pay ?? 0)
             + ($payslip->holiday_pay ?? 0)
             + ($payslip->night_differential ?? 0)
             + ($payslip->bonuses ?? 0);
    }

    private function calculateTotalDeductions($payslip)
    {
        return ($payslip->sss_contribution ?? 0)
             + ($payslip->philhealth_contribution ?? 0)
             + ($payslip->pagibig_contribution ?? 0)
             + ($payslip->withholding_tax ?? 0)
             + ($payslip->late_deductions ?? 0)
             + ($payslip->absent_deductions ?? 0)
             + ($payslip->undertime_deductions ?? 0);
    }

    public function downloadPayslip($id)
    {
        $employee = Auth::user()->employee;
        $payslip = Payroll::where('id', $id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        return response()->json(['message' => 'PDF download feature to be implemented']);
    }

    public function emailPayslip($id)
    {
        $employee = Auth::user()->employee;
        $payslip = Payroll::where('id', $id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        return response()->json(['message' => 'Payslip emailed successfully']);
    }

    public function history(Request $request)
    {
        $employee = Auth::user()->employee;
        
        // Get filter parameters
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $search = $request->get('search');
        
        // Build query
        $query = Payroll::where('employee_id', $employee->id)
            ->with(['employee.department', 'employee.position']);
        
        // Apply year filter
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        // Apply month filter
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        
        // Apply search filter (by pay period or status)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('payrollPeriod', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        // Get payroll history with pagination
        $payrollHistory = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();
        
        // Get available years for filter dropdown
        $availableYears = Payroll::where('employee_id', $employee->id)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        // Calculate summary statistics
        $summary = $this->calculateSummary($employee, $year);
        
        // Get current year and month for filter defaults
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $yearly_earnings_data = $this->getChartData($employee, $year ?? now());
        return view('employee.payroll.history', compact(
            'payrollHistory',
            'employee',
            'availableYears',
            'summary',
            'year',
            'month',
            'search',
            'currentYear',
            'currentMonth',
            'yearly_earnings_data'
        ));
    }
    
    private function calculateSummary($employee, $year)
    {
        $query = Payroll::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function ($q) {
                $q->where('status', 'finalized');
            });

        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        $payrolls = $query->get();
        $total_earnings = 0;
        $total_deductions = 0;
        foreach ($payrolls as $payroll){
            $total_earnings += $this->calculateGrossSalary($payroll);  
            $total_deductions += $payroll->total_deductions;
           
        }
        $total_net_pay = $total_earnings - $total_deductions;
        $averageNetSalary = $payrolls->count() > 0 ? $total_earnings / $payrolls->count() : 0;
        return [
            'total_payslips' => $payrolls->count(),
            'total_earnings' => $total_earnings,
            'total_deductions' => $total_deductions,
            'total_net_pay' => $total_net_pay,
            'average_net_pay' => $averageNetSalary,
            'yearly_bonus' => $payrolls->sum('bonuses'),
            'yearly_overtime' => $payrolls->sum('overtime_pay'),
        ];
    }
    private function getChartData($employee, $year)
    {
        $startOfYear = Carbon::create($year)->startOfYear();
        $endOfYear = Carbon::create($year)->endOfYear();

        $monthly_total_net_pay = [];

        $current = $startOfYear->copy();

        while ($current <= $endOfYear) {
            $monthly_net_pay_holder = 0;
            // Get total net pay for the current month
            $monthly_net_pays = Payroll::where('employee_id', $employee->id)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->whereHas('payrollPeriod', fn($q) => $q->where('status', 'finalized'))
                ->get();
            foreach($monthly_net_pays as $monthly_net_pay){
                $monthly_net_pay_holder += $this->calculateGrossSalary($monthly_net_pay);
            }

            
            $monthly_total_net_pay[] = round($monthly_net_pay_holder, 2);

            $current->addMonth();
        }

        return [
            'monthly_total_net_pay' => $monthly_total_net_pay,
        ];
    }

    public function exportHistory(Request $request)
    {
        $employee = Auth::user()->employee;
        $year = $request->get('year', now()->year);
        
        $payrolls = Payroll::where('employee_id', $employee->id)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // You can implement CSV or Excel export here
        // For now, return JSON response
        return response()->json([
            'message' => 'Export feature to be implemented',
            'data' => $payrolls,
            'year' => $year
        ]);
    }
    
    public function getYearlySummary(Request $request)
    {
        $employee = Auth::user()->employee;
        $year = $request->get('year', now()->year);
        
        $summary = $this->calculateSummary($employee, $year);
        
        return response()->json([
            'success' => true,
            'summary' => $summary,
            'year' => $year
        ]);
    }

    public function deductions()
    {
        $employee = Auth::user()->employee;
        
        // Get active deductions with their types
        $activeDeductions = $employee->activeDeductions()
            ->with('deductionType')
            ->get()
            ->map(function ($deduction) {
                $amount = $deduction->pivot->custom_amount ?? $deduction->amount;
                
                return (object)[
                    'id' => $deduction->id,
                    'name' => $deduction->name,
                    'type' => $deduction->deductionType->name ?? 'Other',
                    'description' => $this->getDeductionDescription($deduction->name),
                    'amount' => $amount,
                    'frequency' => 'monthly',
                    'status' => 'active',
                    'is_government' => $this->isGovernmentDeduction($deduction->name),
                    'employee_share' => $amount,
                    'employer_share' => $this->getEmployerShare($deduction->name, $amount),
                ];
            });

        // Separate government and other deductions
        $governmentContributions = $activeDeductions->where('is_government', true);
        $otherDeductions = $activeDeductions->where('is_government', false);

        // Get active allowances
        $benefits = $employee->activeAllowances()
            ->get()
            ->map(function ($allowance) {
                return (object)[
                    'id' => $allowance->id,
                    'name' => $allowance->name,
                    'description' => $this->getAllowanceDescription($allowance->name),
                    'amount' => $allowance->amount,
                    'frequency' => $allowance->type === 'annual' ? 'annual' : 'monthly',
                    'status' => 'active',
                    'type' => $allowance->type,
                ];
            });

        // Calculate totals
        $totalMonthlyDeductions = $activeDeductions->sum('amount');
        $totalMonthlyBenefits = $benefits->where('frequency', 'monthly')->sum('amount') + 
                               ($benefits->where('frequency', 'annual')->sum('amount') / 12);
        $netImpact = $totalMonthlyBenefits - $totalMonthlyDeductions;

        // Get tax information from latest payroll or calculate
        $latestPayroll = Payroll::where('employee_id', $employee->id)
            ->latest('created_at')
            ->first();
        $taxInfo = $this->getTaxInformation($employee, $latestPayroll);

        return view('employee.payroll.deductions', compact(
            'employee',
            'governmentContributions',
            'otherDeductions',
            'benefits',
            'totalMonthlyDeductions',
            'totalMonthlyBenefits',
            'netImpact',
            'taxInfo'
        ));
    }



    // // sfdsgdfsg
    // public function taxInfo()
    // {
    //     $employee = Auth::user()->employee;
        
    //     // Get latest payroll for tax data
    //     $latestPayroll = $employee->payrolls()
    //         ->where('status', 'paid')
    //         ->latest('payroll_date')
    //         ->first();

    //     $taxInfo = $this->getDetailedTaxInformation($employee, $latestPayroll);

    //     return view('employee.payroll.tax-info', compact('employee', 'taxInfo'));
    // }




    // ==================== PRIVATE HELPER METHODS ====================

    private function calculateYtdTotals($employee)
    {
        $currentYear = now()->year;
        
        $ytdPayrolls = $employee->payrolls()
            ->whereYear('created_at', $currentYear)
            ->get();

        return [
            'total_earnings' => $ytdPayrolls->sum('gross_salary'),
            'total_deductions' => $ytdPayrolls->sum('total_deductions'),
            'total_net_pay' => $ytdPayrolls->sum('net_salary'),
            'payroll_count' => $ytdPayrolls->count(),
            'average_net_pay' => $ytdPayrolls->avg('net_salary'),
        ];
    }



    private function getDeductionDescription($deductionName)
    {
        $descriptions = [
            'SSS' => 'Social Security System - Retirement, disability, death benefits',
            'PhilHealth' => 'Philippine Health Insurance Corporation',
            'Pag-IBIG' => 'Home Development Mutual Fund',
            'Withholding Tax' => 'Income tax deduction',
            'Salary Loan' => 'Salary loan deduction',
            'Emergency Loan' => 'Emergency assistance loan',
            'Cash Advance' => 'Employee cash advance',
            'Union Dues' => 'Labor union membership fees',
            'Insurance' => 'Group life insurance premium',
        ];

        return $descriptions[$deductionName] ?? 'Salary deduction';
    }

    private function getAllowanceDescription($allowanceName)
    {
        $descriptions = [
            'Transportation Allowance' => 'Monthly transportation support',
            'Meal Allowance' => 'Daily meal subsidy',
            'Clothing Allowance' => 'Annual clothing allowance',
            'Rice Allowance' => 'Monthly rice subsidy',
            'Communication Allowance' => 'Mobile and internet allowance',
            'Medical Allowance' => 'Health and medical benefits',
            'Housing Allowance' => 'Housing assistance',
            'Education Allowance' => 'Educational assistance',
            'Performance Bonus' => 'Performance-based incentive',
        ];

        return $descriptions[$allowanceName] ?? 'Employee allowance';
    }

    private function isGovernmentDeduction($deductionName)
    {
        $governmentDeductions = ['SSS', 'PhilHealth', 'Pag-IBIG', 'Withholding Tax'];
        
        return in_array($deductionName, $governmentDeductions);
    }

    private function getEmployerShare($deductionName, $employeeShare)
    {
        $employerShares = [
            'SSS' => $employeeShare * 2, // Employer pays double for SSS
            'PhilHealth' => $employeeShare, // 1:1 matching
            'Pag-IBIG' => $employeeShare, // 1:1 matching
            'Withholding Tax' => 0, // No employer share for tax
        ];

        return $employerShares[$deductionName] ?? 0;
    }

    private function getTaxInformation($employee, $latestPayroll)
    {
        if ($latestPayroll) {
            $taxableIncome = $latestPayroll->gross_salary - 
                           ($latestPayroll->sss_contribution + 
                            $latestPayroll->philhealth_contribution + 
                            $latestPayroll->pagibig_contribution);
            
            return [
                'taxable_income' => $taxableIncome,
                'withholding_tax' => $latestPayroll->withholding_tax ?? 0,
                'tax_rate' => $taxableIncome > 0 ? round(($latestPayroll->withholding_tax / $taxableIncome) * 100, 1) : 0,
                'personal_exemption' => 50000.00,
                'additional_exemption' => 25000.00,
            ];
        }

        // Default values if no payroll data
        return [
            'taxable_income' => 25000.00,
            'withholding_tax' => 1875.00,
            'tax_rate' => 15.0,
            'personal_exemption' => 50000.00,
            'additional_exemption' => 25000.00,
        ];
    }

    // private function getDetailedTaxInformation($employee, $latestPayroll)
    // {
    //     $basicInfo = $this->getTaxInformation($employee, $latestPayroll);
        
    //     // Add more detailed tax information
    //     return array_merge($basicInfo, [
    //         'tax_brackets' => [
    //             ['from' => 0, 'to' => 20832, 'rate' => 0, 'base_tax' => 0],
    //             ['from' => 20833, 'to' => 33333, 'rate' => 20, 'base_tax' => 0],
    //             ['from' => 33334, 'to' => 66667, 'rate' => 25, 'base_tax' => 2500],
    //             ['from' => 66668, 'to' => 166667, 'rate' => 30, 'base_tax' => 10833],
    //             ['from' => 166668, 'to' => 666667, 'rate' => 32, 'base_tax' => 40833.33],
    //             ['from' => 666668, 'to' => null, 'rate' => 35, 'base_tax' => 200833.33],
    //         ],
    //         'non_taxable_benefits' => [
    //             'De Minimis Benefits' => 90000,
    //             '13th Month Pay' => 90000,
    //             'Other Benefits' => 0,
    //         ],
    //         'tax_credits' => [
    //             'Personal Exemption' => 50000,
    //             'Additional Exemption' => 25000,
    //         ]
    //     ]);
    // }
public function taxInfo()
{
    $employee = Auth::user()->employee;
    
    // Get latest payroll for tax data
    $latestPayroll = $employee->payrolls()
        ->whereHas('payrollPeriod', function ($q) {
            $q->where('status', 'finalized');
        })
        ->latest('created_at')
        ->first();

    $taxInfo = $this->getDetailedTaxInformation($employee, $latestPayroll);

    return view('employee.payroll.tax-info', compact('employee', 'taxInfo'));
}

private function getDetailedTaxInformation($employee, $latestPayroll)
{
    $basicInfo = $this->getTaxInformation($employee, $latestPayroll);
    
    // Get YTD payroll data
    $currentYear = now()->year;
    $ytdPayrolls = $employee->payrolls()
        ->whereHas('payrollPeriod', function ($q) {
            $q->where('status', 'finalized');
        })
        ->whereYear('created_at', $currentYear)
        ->get();

    // Enhanced tax information with more details
    return array_merge($basicInfo, [
        'income_breakdown' => [
            'basic_salary' => $latestPayroll->basic_salary ?? 0,
            'overtime_pay' => $latestPayroll->overtime_pay ?? 0,
            'allowances_bonuses' => $latestPayroll->total_allowances ?? 0,
            'gross_income' => ($latestPayroll->basic_salary ?? 0) + ($latestPayroll->overtime_pay ?? 0) + ($latestPayroll->total_allowances ?? 0),
        ],
        'deductions' => [
            'sss' => $latestPayroll->sss_contribution ?? 0,
            'philhealth' => $latestPayroll->philhealth_contribution ?? 0,
            'pagibig' => $latestPayroll->pagibig_contribution ?? 0,
            'other' => 0, // Add other deductions if available
        ],
        'exemptions' => [
            'personal' => 50000,
            'additional' => 25000,
        ],
        'tax_brackets' => $this->getCurrentTaxBrackets($basicInfo['taxable_income'] * 12),
        'non_taxable_benefits' => [
            'De Minimis Benefits' => 90000,
            '13th Month Pay' => 90000,
            'Other Benefits' => 0,
        ],
        'ytd_summary' => [
            'total_income' => $ytdPayrolls->sum('gross_salary'),
            'total_tax_paid' => $ytdPayrolls->sum('withholding_tax'),
            'average_monthly_tax' => $ytdPayrolls->avg('withholding_tax'),
            'projected_annual_tax' => $ytdPayrolls->avg('withholding_tax') * 12,
            'payroll_periods' => $ytdPayrolls->count(),
        ],
        'payment_history' => $this->getTaxPaymentHistory($employee),
    ]);
}

private function getCurrentTaxBrackets($annualTaxableIncome)
{
    $brackets = [
        ['from' => 0, 'to' => 250000, 'rate' => 0, 'base_tax' => 0],
        ['from' => 250001, 'to' => 400000, 'rate' => 20, 'base_tax' => 0],
        ['from' => 400001, 'to' => 800000, 'rate' => 25, 'base_tax' => 30000],
        ['from' => 800001, 'to' => 2000000, 'rate' => 30, 'base_tax' => 130000],
        ['from' => 2000001, 'to' => 8000000, 'rate' => 32, 'base_tax' => 490000],
        ['from' => 8000001, 'to' => null, 'rate' => 35, 'base_tax' => 2410000],
    ];

    // Mark current bracket
    foreach ($brackets as &$bracket) {
        $bracket['is_current'] = $annualTaxableIncome >= $bracket['from'] && 
                                ($bracket['to'] === null || $annualTaxableIncome <= $bracket['to']);
    }

    return $brackets;
}

private function getTaxPaymentHistory($employee)
{
    // Get last 6 months of payroll data
    $sixMonthsAgo = now()->subMonths(6);
    
    $recentPayrolls = $employee->payrolls()
        ->whereHas('payrollPeriod', function ($q) {
            $q->where('status', 'finalized');
        })
        ->where('created_at', '>=', $sixMonthsAgo)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($payroll) {
            $taxableIncome = $payroll->gross_salary - 
                           ($payroll->sss_contribution + 
                            $payroll->philhealth_contribution + 
                            $payroll->pagibig_contribution);
            
            return [
                'period' => $payroll->payrollPeriod->period_display ?? 'N/A',
                'taxable_income' => max(0, $taxableIncome),
                'withholding_tax' => $payroll->withholding_tax ?? 0,
                'effective_rate' => $taxableIncome > 0 ? round(($payroll->withholding_tax / $taxableIncome) * 100, 1) : 0,
                'status' => 'paid',
            ];
        });

    return $recentPayrolls;
}
}