<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Employee;
use App\Services\TaxCalculationService;
use Carbon\Carbon;
class TaxReportController extends Controller
{
    //

       protected $taxService;

    public function __construct(TaxCalculationService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * Show tax calculations and reports dashboard
     */
    public function index(Request $request)
    {
        $periods = PayrollPeriod::orderBy('end_date', 'desc')->limit(12)->get();
        $selectedPeriod = $request->period_id 
            ? PayrollPeriod::find($request->period_id) 
            : PayrollPeriod::latest('end_date')->first();

        $taxReport = null;
        if ($selectedPeriod) {
            $taxReport = $this->taxService->generateTaxReport($selectedPeriod);
        }

        $taxBrackets = TaxCalculationService::getTaxBrackets();

        return view('admin.payroll.tax-reports.index', compact(
            'periods',
            'selectedPeriod',
            'taxReport',
            'taxBrackets'
        ));
    }

    /**
     * Show tax breakdown for a specific period
     */
    public function taxBreakdown(PayrollPeriod $period)
    {
        $taxReport = $this->taxService->generateTaxReport($period);
        
        return view('admin.payroll.tax-reports.breakdown', compact('period', 'taxReport'));
    }

    /**
     * Show employee tax details
     */
    public function employeeTaxDetails(Employee $employee, Request $request)
    {
        $year = $request->year ?? now()->year;
        
        $payrolls = Payroll::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function($q) use ($year) {
                $q->whereYear('end_date', $year);
            })
            ->with('payrollPeriod')
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate Form 2316 estimate
        $form2316 = $this->taxService->generateForm2316Estimate($employee->id, $year);

        // Calculate YTD projection
        $currentMonth = now()->month;
        $totalGross = $payrolls->sum('gross_pay');
        $projection = $this->taxService->projectYearEndTax(
            $totalGross / max(1, $payrolls->count()),
            $payrolls->count()
        );

        return view('admin.payroll.tax-reports.employee-details', compact(
            'employee',
            'payrolls',
            'form2316',
            'projection',
            'year'
        ));
    }

    /**
     * Generate and download Form 2316 (Tax Certificate)
     */
    public function downloadForm2316(Employee $employee, Request $request)
    {
        $year = $request->year ?? now()->year;
        $form2316 = $this->taxService->generateForm2316Estimate($employee->id, $year);

        // Generate PDF or return data
        $html = view('admin.payroll.tax-reports.form-2316-pdf', compact('form2316'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"Form-2316-{$employee->id}-{$year}.html\"");
    }

    /**
     * Export tax report as CSV
     */
    public function exportTaxReport(PayrollPeriod $period, Request $request)
    {
        $taxReport = $this->taxService->generateTaxReport($period);

        $csv = "Employee Name,Gross Income,Tax Withheld,Effective Rate (%)\n";
        
        foreach ($taxReport['employees'] as $emp) {
            $csv .= "\"{$emp['employee_name']}\",{$emp['gross_income']},{$emp['tax_withheld']},{$emp['effective_rate']}\n";
        }

        $csv .= "\nSummary\n";
        $csv .= "Total Employees,{$taxReport['payroll_count']}\n";
        $csv .= "Total Gross Income,{$taxReport['total_gross_income']}\n";
        $csv .= "Total Tax Withheld,{$taxReport['total_tax_withheld']}\n";
        $csv .= "Average Tax Rate,{$taxReport['average_tax_rate']}\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"tax-report-{$period->id}-" . now()->format('Y-m-d') . ".csv\"");
    }

    /**
     * Show tax comparison analysis
     */
    public function comparison(Request $request)
    {
        $monthlyGross = $request->gross_amount ?? 30000;

        $comparison = $this->taxService->compareTaxLiability($monthlyGross);
        $taxBrackets = TaxCalculationService::getTaxBrackets();

        return view('admin.payroll.tax-reports.comparison', compact('comparison', 'monthlyGross', 'taxBrackets'));
    }

    /**
     * Get tax calculation API endpoint
     */
    public function calculateTax(Request $request)
    {
        $request->validate([
            'gross_income' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
        ]);

        $taxResult = $this->taxService->calculateMonthlyTax(
            $request->gross_income,
            [],
            0,
            $request->allowances ?? 0
        );

        return response()->json($taxResult);
    }

    /**
     * BIR tax brackets reference
     */
    public function brackets()
    {
        $brackets = TaxCalculationService::getTaxBrackets();
        
        return view('admin.payroll.tax-reports.brackets', compact('brackets'));
    }
}
