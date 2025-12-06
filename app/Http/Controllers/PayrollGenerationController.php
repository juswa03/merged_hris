<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\Employee;
use App\Services\DtrToPayrollService;
use App\Exports\GeneralPayrollSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollGenerationController extends Controller
{
    protected $dtrToPayrollService;

    public function __construct(DtrToPayrollService $dtrToPayrollService)
    {
        $this->dtrToPayrollService = $dtrToPayrollService;
    }

    /**
     * Show payroll generation dashboard
     */
    public function index()
    {
        $periods = PayrollPeriod::where('status', '!=', 'completed')
            ->where('start_date', '<=', Carbon::now())
            ->orderBy('start_date', 'desc')
            ->limit(12)
            ->get();
        
        return view('admin.payroll.generation.index', compact('periods'));
    }

    /**
     * Show DTR validation for a specific payroll period
     */
    public function validateDtr(PayrollPeriod $period)
    {
        $validation = $this->dtrToPayrollService->validateDtrForPayroll($period);
        $summary = $this->dtrToPayrollService->getDtrSummaryForPayroll($period);

        return view('admin.payroll.generation.validate-dtr', compact('period', 'validation', 'summary'));
    }

    /**
     * Get DTR summary API endpoint
     */
    public function getDtrSummary(PayrollPeriod $period)
    {
        $summary = $this->dtrToPayrollService->getDtrSummaryForPayroll($period);

        return response()->json($summary);
    }

    /**
     * Generate payroll for a period
     */
    public function generatePayroll(PayrollPeriod $period)
    {
        // Validate DTR first
        $validation = $this->dtrToPayrollService->validateDtrForPayroll($period);

        if ($validation['missing_count'] > 0 || $validation['incomplete_count'] > 0) {
            return redirect()->back()
                ->with('warning', "There are incomplete DTR entries. Please review before proceeding.")
                ->with('validation', $validation);
        }

        // Dispatch background job
        \App\Jobs\GeneratePayrollJob::dispatch($period, auth()->user());

        return redirect()->back()
            ->with('success', "Payroll generation started in the background. Please wait for the notification.");
    }

    /**
     * Show payroll generation results
     */
    public function results()
    {
        $result = session('result');

        return view('admin.payroll.generation.results', compact('result'));
    }

    /**
     * Recalculate payroll for a single employee
     */
    public function recalculateEmployee(Employee $employee, PayrollPeriod $period)
    {
        $result = $this->dtrToPayrollService->recalculatePayrollFromDtr($employee, $period);

        return response()->json([
            'status' => 'success',
            'message' => "Payroll recalculated for {$employee->full_name}",
            'data' => $result,
        ]);
    }

    /**
     * Export payroll data
     */
    public function exportPayroll(PayrollPeriod $period)
    {
        $summary = $this->dtrToPayrollService->getDtrSummaryForPayroll($period);

        return response()->json($summary);
    }

    /**
     * Export General Payroll Sheet
     */
    public function exportGeneralPayrollSheet(PayrollPeriod $period)
    {
        $filename = 'General_Payroll_Sheet_' . $period->start_date->format('F_Y') . '.xlsx';
        return Excel::download(new GeneralPayrollSheetExport($period->id), $filename);
    }
}
