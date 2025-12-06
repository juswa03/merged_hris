<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Department;
use App\Models\PayrollAudit;
use App\Services\PayrollCalculationService;
use App\Services\PayrollAuditService;
use App\Services\BulkPaymentExportService;
use App\Mail\PayslipEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $payrollCalculationService;
    protected $auditService;

    public function __construct(PayrollCalculationService $payrollCalculationService, PayrollAuditService $auditService)
    {
        $this->payrollCalculationService = $payrollCalculationService;
        $this->auditService = $auditService;
    }

    public function index(Request $request)
    {
        $query = Payroll::with(['employee', 'payrollPeriod'])->latest();
        
        // Apply filters
        if ($request->has('period_id') && $request->period_id) {
            $query->where('payroll_period_id', $request->period_id);
        }
        
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $payrolls = $query->paginate(20);
        
        // Get statistics with a single, optimized query
        $stats = Payroll::join('tbl_payroll_periods', 'tbl_payrolls.payroll_period_id', '=', 'tbl_payroll_periods.id')
            ->selectRaw("
                SUM(net_pay) as total_payroll,
                COUNT(CASE WHEN tbl_payroll_periods.status = 'paid' THEN 1 END) as employees_paid,
                COUNT(CASE WHEN tbl_payroll_periods.status = 'draft' THEN 1 END) as pending_payrolls
            ")
            ->first();

        $totalEmployees = Employee::whereHas('jobStatus', function($q) { $q->where('name', 'Active'); })->count();
        
        // Get current payroll period (most recent)
        $currentPeriod = PayrollPeriod::where('end_date', '>=', now())->orderBy('start_date', 'asc')->first();
        
        $payrollPeriods = PayrollPeriod::orderBy('end_date', 'desc')->get();
        $employees = Employee::whereHas('jobStatus', function ($query) {
            $query->where('name', 'Active');
        })->get();
        $departments = Department::all();

        // Extract totals from stats for backward compatibility with views
        $totalPayroll = $stats->total_payroll ?? 0;
        $employeesPaid = $stats->employees_paid ?? 0;
        $pendingPayrolls = $stats->pending_payrolls ?? 0;
        
        return view('admin.payroll.index', compact(
            'payrolls',
            'payrollPeriods',
            'employees',
            'departments',
            'stats',
            'totalEmployees',
            'totalPayroll',
            'employeesPaid',
            'pendingPayrolls',
            'currentPeriod'
        ));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'payroll_period_id' => 'required|exists:tbl_payroll_periods,id',
            'employee_type' => 'nullable|in:all,regular,contractual',
            'department_id' => 'nullable|exists:tbl_departments,id',
            'include_overtime' => 'nullable|boolean'
        ]);
        
        try {
            DB::beginTransaction();
            
            $payrollPeriod = PayrollPeriod::findOrFail($request->payroll_period_id);

            // Get employees based on filters
            $employeeQuery = Employee::whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            });

            if ($request->employee_type && $request->employee_type !== 'all') {
                $employeeQuery->whereHas('employmentType', function($q) use ($request) {
                    $q->where('name', $request->employee_type);
                });
            }

            if ($request->department_id) {
                $employeeQuery->where('department_id', $request->department_id);
            }

            $employees = $employeeQuery->get();
            
            $generatedCount = 0;
            $updatedCount = 0;
            
            foreach ($employees as $employee) {
                // Check if payroll already exists for this period
                $existingPayroll = Payroll::where('employee_id', $employee->id)
                    ->where('payroll_period_id', $payrollPeriod->id)
                    ->first();
                
                $payrollData = $this->calculatePayroll($employee, $payrollPeriod, $request->include_overtime);
                $payrollData['status'] = 'generated';
                
                if ($existingPayroll) {
                    // Update existing payroll
                    $existingPayroll->update($payrollData);
                    $updatedCount++;
                } else {
                    // Create new payroll
                    Payroll::create(array_merge(['employee_id' => $employee->id], $payrollData));
                    $generatedCount++;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Payroll generated successfully. Generated: {$generatedCount}, Updated: {$updatedCount}",
                'generated' => $generatedCount,
                'updated' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error generating payroll: ' . $e->getMessage()
            ]);
        }
    }
    
    public function show($id)
    {
        $payroll = Payroll::with(['employee', 'payrollPeriod'])->findOrFail($id);
        return response()->json($payroll);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'basic_salary' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'total_allowances' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:draft,processed,paid'
        ]);
        
        try {
            $payroll = Payroll::findOrFail($id);
            
            $updateData = $request->only([
                'basic_salary', 'overtime_pay', 'total_allowances', 
                'total_deductions', 'status'
            ]);
            
            // Recalculate net pay if any amount changes
            if ($request->hasAny(['basic_salary', 'overtime_pay', 'total_allowances', 'total_deductions'])) {
                $grossPay = ($request->basic_salary ?? $payroll->basic_salary) + 
                           ($request->overtime_pay ?? $payroll->overtime_pay) + 
                           ($request->total_allowances ?? $payroll->total_allowances);
                
                $netPay = $grossPay - ($request->total_deductions ?? $payroll->total_deductions);
                $updateData['net_pay'] = max(0, $netPay);
            }
            
            $payroll->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Payroll updated successfully.',
                'data' => $payroll
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payroll: ' . $e->getMessage()
            ]);
        }
    }
    
    public function destroy($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            $payroll->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Payroll entry deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payroll entry: ' . $e->getMessage()
            ]);
        }
    }
    
    public function export(Request $request)
    {
        // Collect filters from request
        $filters = [];

        if ($request->has('period_id') && $request->period_id) {
            $filters['period_id'] = $request->period_id;
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $filters['employee_id'] = $request->employee_id;
        }

        if ($request->has('status') && $request->status) {
            $filters['status'] = $request->status;
        }

        if ($request->has('department_id') && $request->department_id) {
            $filters['department_id'] = $request->department_id;
        }

        // Generate filename with timestamp
        $timestamp = now()->format('Y-m-d_His');
        $filename = "payroll_export_{$timestamp}.xlsx";

        // Export to Excel using the PayrollExport class
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PayrollExport($filters),
            $filename
        );
    }
    
    // public function payslip($id)
    // {
    //     $payroll = Payroll::with(['employee', 'payrollPeriod'])->findOrFail($id);
        
    //     // This would typically generate a PDF payslip
    //     // For now, return a JSON response
    //     return response()->json([
    //         'payslip_data' => $payroll,
    //         'company_info' => [
    //             'name' => 'BIPSU',
    //             'address' => 'Biliran, Philippines'
    //         ]
    //     ]);
    // }
    
    private function calculatePayroll(Employee $employee, PayrollPeriod $period, $includeOvertime = true)
    {
        // Use the comprehensive PayrollCalculationService
        return $this->payrollCalculationService->calculateCompletePayroll($employee, $period, $includeOvertime);
    }
    public function payslips(Request $request)
    {
        $query = Payroll::with(['employee', 'payrollPeriod'])
            ->latest();
        
        // Apply filters
        if ($request->filled('year')) {
            $query->whereHas('payrollPeriod', function($q) use ($request) {
                $q->whereYear('start_date', $request->year);
            });
        }

        if ($request->filled('month')) {
            $query->whereHas('payrollPeriod', function($q) use ($request) {
                $q->whereMonth('start_date', $request->month);
            });
        }

        if ($request->has('period_id') && $request->period_id) {
            $query->where('payroll_period_id', $request->period_id);
        }
        
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        $payrolls = $query->paginate(12);
        
        $payrollPeriods = PayrollPeriod::orderBy('end_date', 'desc')->get();
        $employees = Employee::whereHas('jobStatus', function($q) { $q->where('name', 'Active'); })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        $departments = Department::orderBy('name')->get();
        
        // Get available years
        $years = PayrollPeriod::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.payroll.payslip', compact(
            'payrolls',
            'payrollPeriods',
            'employees',
            'departments',
            'years'
        ));
    }

    /**
     * Generate individual payslip (PDF/View)
     */
    public function payslip(Request $request, $id)
    {
        $payroll = Payroll::with(['employee.position', 'employee.department', 'payrollPeriod'])->findOrFail($id);

        if ($request->expectsJson()) {
            $payslipData = $payroll->toArray();
            
            // Manually inject the formatted period name since it's an accessor
            if ($payroll->payrollPeriod) {
                $payslipData['payroll_period']['period_name'] = $payroll->payrollPeriod->formatted_period;
            }

            // Ensure employee_id is present (fallback to id if employee_id column is missing/null)
            if ($payroll->employee) {
                $payslipData['employee']['employee_id'] = $payroll->employee->employee_id ?? str_pad($payroll->employee->id, 6, '0', STR_PAD_LEFT);
            }

            return response()->json([
                'payslip_data' => $payslipData,
                'company_info' => [
                    'name' => 'Biliran Province State University',
                    'address' => 'Naval, Biliran',
                    'contact' => '(053) 500-9045'
                ],
                'breakdown' => [
                    'gross_pay' => $payroll->gross_pay,
                    'gsis' => $payroll->gsis_contribution,
                    'philhealth' => $payroll->philhealth_contribution,
                    'pagibig' => $payroll->pagibig_contribution,
                    'tax' => $payroll->withholding_tax,
                    'other_deductions' => $payroll->other_deductions
                ]
            ]);
        }

        return view('admin.payroll.payslip-view', compact('payroll'));
    }

    /**
     * Download payslip as PDF
     */
    public function downloadPayslip($id)
    {
        $payroll = Payroll::with(['employee.position', 'employee.department', 'payrollPeriod'])->findOrFail($id);

        // Prepare data to match Employee view expectations
        if (!$payroll->gross_salary) {
            $payroll->gross_salary = $payroll->gross_pay ?? ($payroll->basic_salary 
                + ($payroll->overtime_pay ?? 0)
                + ($payroll->holiday_pay ?? 0)
                + ($payroll->night_differential ?? 0)
                + ($payroll->bonuses ?? 0));
        }
        
        if (!$payroll->net_salary) {
            $payroll->net_salary = $payroll->net_pay ?? ($payroll->gross_salary - $payroll->total_deductions);
        }

        // Mock supervisor and HR officer for now (or fetch from settings/config if available)
        $supervisor = (object)['name' => 'SUPERVISOR NAME'];
        $hrOfficer = (object)['name' => 'HR OFFICER'];
        $employee = $payroll->employee;

        // Load the PDF view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payroll.payslip-pdf', compact('payroll', 'employee', 'supervisor', 'hrOfficer'));

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $employeeName = strtoupper($payroll->employee?->last_name ?? 'EMPLOYEE');
        $periodName = $payroll->payrollPeriod ? $payroll->payrollPeriod->period_name : 'N-A';
        $filename = "Payslip_{$employeeName}_{$periodName}.pdf";

        // Download the PDF
        return $pdf->download($filename);
    }

    /**
     * Email payslip to employee
     */
    public function emailPayslip(Request $request, $id)
    {
        try {
            $payroll = Payroll::with(['employee.user', 'employee.position', 'employee.department', 'payrollPeriod'])->findOrFail($id);
            
            $email = $payroll->employee->user->email ?? null;

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'No email address linked to this employee.'
                ], 400);
            }

            // Prepare data for PDF (same as downloadPayslip)
            if (!$payroll->gross_salary) {
                $payroll->gross_salary = $payroll->gross_pay ?? ($payroll->basic_salary 
                    + ($payroll->overtime_pay ?? 0)
                    + ($payroll->holiday_pay ?? 0)
                    + ($payroll->night_differential ?? 0)
                    + ($payroll->bonuses ?? 0));
            }
            
            if (!$payroll->net_salary) {
                $payroll->net_salary = $payroll->net_pay ?? ($payroll->gross_salary - $payroll->total_deductions);
            }

            $supervisor = (object)['name' => 'SUPERVISOR NAME'];
            $hrOfficer = (object)['name' => 'HR OFFICER'];
            $employee = $payroll->employee;

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payroll.payslip-pdf', compact('payroll', 'employee', 'supervisor', 'hrOfficer'));
            $pdf->setPaper('A4', 'portrait');
            
            $employeeName = strtoupper($payroll->employee?->last_name ?? 'EMPLOYEE');
            $periodName = $payroll->payrollPeriod ? $payroll->payrollPeriod->period_name : 'N-A';
            $filename = "Payslip_{$employeeName}_{$periodName}.pdf";

            // Send Email
            Mail::to($email)->send(new PayslipEmail($payroll, $pdf->output(), $filename));

            return response()->json([
                'success' => true,
                'message' => 'Payslip successfully emailed to ' . $email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk download payslips
     */
    public function bulkDownload(Request $request)
    {
        $query = Payroll::with(['employee.position', 'payrollPeriod']);

        // Apply filters
        if ($request->has('period_id') && $request->period_id) {
            $query->where('payroll_period_id', $request->period_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $payrolls = $query->get();

        if ($payrolls->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No payslips found matching the criteria.'
            ], 404);
        }

        // Create temporary directory for PDFs
        $tempDir = storage_path('app/temp/payslips_' . time());
        \Illuminate\Support\Facades\File::makeDirectory($tempDir, 0755, true);

        try {
            // Generate individual PDFs
            foreach ($payrolls as $payroll) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payroll.payslip-view', compact('payroll'));
                $pdf->setPaper('A4', 'portrait');

                $employeeName = strtoupper($payroll->employee?->last_name ?? 'EMPLOYEE');
                $filename = "Payslip_{$employeeName}.pdf";
                $pdf->save($tempDir . '/' . $filename);
            }

            // Create ZIP archive
            $zipFilename = 'Payslips_' . now()->format('Y-m-d_His') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFilename);

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                $files = \Illuminate\Support\Facades\File::files($tempDir);
                foreach ($files as $file) {
                    $zip->addFile($file->getRealPath(), $file->getFilename());
                }
                $zip->close();
            }

            // Clean up temporary PDFs
            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);

            // Download and delete ZIP
            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Clean up on error
            if (\Illuminate\Support\Facades\File::exists($tempDir)) {
                \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error generating payslips: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show detailed payroll breakdown with DTR integration
     */
    public function detailedBreakdown($id)
    {
        $payroll = Payroll::with(['employee.department', 'employee.position', 'payrollPeriod'])
            ->findOrFail($id);

        // Get DTR entries for this payroll period
        $dtrEntries = \App\Models\DtrEntry::where('employee_id', $payroll->employee_id)
            ->whereBetween('dtr_date', [$payroll->payrollPeriod->start_date, $payroll->payrollPeriod->end_date])
            ->orderBy('dtr_date')
            ->get();

        // Calculate DTR statistics
        $dtrStats = [
            'total_days' => $dtrEntries->count(),
            'present_days' => $dtrEntries->whereIn('status', ['present', 'late', 'undertime'])->count(),
            'absent_days' => $dtrEntries->where('status', 'absent')->count(),
            'late_days' => $dtrEntries->where('status', 'late')->count(),
            'undertime_days' => $dtrEntries->where('status', 'undertime')->count(),
            'weekend_days' => $dtrEntries->where('is_weekend', true)->count(),
            'holiday_days' => $dtrEntries->where('is_holiday', true)->count(),
            'total_hours_worked' => $dtrEntries->sum(function($dtr) {
                return $dtr->total_hours + ($dtr->total_minutes / 60);
            }),
            'total_undertime_hours' => $dtrEntries->sum('under_time_minutes') / 60,
            'overtime_days' => $dtrEntries->filter(function($dtr) {
                return ($dtr->total_hours + ($dtr->total_minutes / 60)) > 8;
            })->count(),
        ];

        return view('admin.payroll.detailed-breakdown', compact('payroll', 'dtrEntries', 'dtrStats'));
    }

    /**
     * Show audit history
     */
    public function auditHistory(Request $request)
    {
        $query = PayrollAudit::query()->with(['payroll.employee', 'user']);

        // Filter by employee
        if ($request->employee_id) {
            $query->whereHas('payroll', function($q) {
                $q->where('employee_id', request('employee_id'));
            });
        }

        // Filter by action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->date_range && $request->date_range !== 'all') {
            $since = match($request->date_range) {
                'today' => now()->startOfDay(),
                'week' => now()->subDays(7),
                'month' => now()->subDays(30),
                'quarter' => now()->subDays(90),
                default => null,
            };

            if ($since) {
                $query->where('created_at', '>=', $since);
            }
        }

        $auditTrail = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::whereHas('jobStatus', function($q) {
            $q->where('name', 'Active');
        })->orderBy('first_name')->get();

        return view('admin.payroll.audit-history', compact('auditTrail', 'employees'));
    }

    /**
     * Export audit report
     */
    public function exportAuditReport(PayrollPeriod $period)
    {
        $csv = $this->auditService->exportAuditReport($period, 'csv');
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"payroll-audit-{$period->id}-" . now()->format('Y-m-d') . ".csv\"");
    }

    /**
     * Show bulk payment export options
     */
    public function bulkPaymentExport(PayrollPeriod $period)
    {
        $exportService = new BulkPaymentExportService();
        $summary = $exportService->generateSummaryReport($period);

        return view('admin.payroll.bulk-payment-export', compact('period', 'summary'));
    }

    /**
     * Generate bulk payment file
     */
    public function generateBulkPaymentFile(PayrollPeriod $period, Request $request)
    {
        $request->validate([
            'export_format' => 'required|in:csv,ach,bpi,bdo,pnb,metrobank',
        ]);

        $exportService = new BulkPaymentExportService();
        $exportFormat = $request->export_format;

        if ($exportFormat === 'ach') {
            $content = $exportService->generateACHFormat($period);
            $filename = "payroll-ach-{$period->id}-" . now()->format('Y-m-d') . ".ach";
            $contentType = 'text/plain';
        } elseif (in_array($exportFormat, ['bpi', 'bdo', 'pnb', 'metrobank'])) {
            $content = $exportService->generateBankSpecificFormat($period, strtoupper($exportFormat));
            $filename = "payroll-{$exportFormat}-{$period->id}-" . now()->format('Y-m-d') . ".csv";
            $contentType = 'text/csv';
        } else {
            $content = $exportService->generateCSVFormat($period);
            $filename = "payroll-export-{$period->id}-" . now()->format('Y-m-d') . ".csv";
            $contentType = 'text/csv';
        }

        return response($content)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

}