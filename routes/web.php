<?php

use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;


use App\Http\Controllers\Admin\{
    AllowanceController as AdminAllowanceController,
    AttendanceController as AdminAttendanceController,
    BiometricController as AdminBiometricController,
    BiometricLogController as AdminBiometricLogController,
    DashboardController as AdminDashboardController,
    DeductionController as AdminDeductionController,
    DeductionTypeController as AdminDeductionTypeController,
    DepartmentController as AdminDepartmentController,
    DeviceController as AdminDeviceController,
    DtrController as AdminDtrController,
    EmployeeController as AdminEmployeeController,
    HolidayController as AdminHolidayController,
    LeaveController as AdminLeaveController,
    PayrollController as AdminPayrollController,
    PayrollAnalyticsController as AdminPayrollAnalyticsController,
    PayrollGenerationController as AdminPayrollGenerationController,
    PayrollPeriodController as AdminPayrollPeriodController,
    PayrollReportController as AdminPayrollReportController,
    PayrollSettingController as AdminPayrollSettingController,
    PDSController as AdminPDSController,
    PerformanceAnalyticsController as AdminPerformanceAnalyticsController,
    PerformanceCriteriaController as AdminPerformanceCriteriaController,
    PerformanceGoalController as AdminPerformanceGoalController,
    PerformanceReviewController as AdminPerformanceReviewController,
    PositionController as AdminPositionController,
    ProfileController as AdminProfileController,
    ReportController as AdminReportController,
    RoleController as AdminRoleController,
    SalaryGradeController as AdminSalaryGradeController,
    SalaryManagementController as AdminSalaryManagementController,
    SalnController as AdminSalnController,
    TaxReportController as AdminTaxReportController,
    TravelController as AdminTravelController,
    UserController as AdminUserController,
    SettingsController as AdminSettingsController,
    JobStatusController as AdminJobStatusController,
    EmploymentTypeController as AdminEmploymentTypeController,
    WorkScheduleController as AdminWorkScheduleController,
    LeaveBalanceController as AdminLeaveBalanceController,
    AuditLogController as AdminAuditLogController,
    OrgChartController as AdminOrgChartController,
    NotificationController as AdminNotificationController,
    ReportBuilderController as AdminReportBuilderController,
    SystemHealthController as AdminSystemHealthController,
    LoginSessionController as AdminLoginSessionController,
    MaintenanceController as AdminMaintenanceController,
    BackupController as AdminBackupController,
    QueueMonitorController as AdminQueueMonitorController,
};

use App\Http\Controllers\HR\DashboardController as HRDashboardController;


use App\Http\Controllers\Employee\{
    EmployeeController as EmployeeController,
    DtrController as DtrController,
    EmployeeAttendanceController as EmployeeAttendanceController,
    EmployeeAccountController as EmployeeAccountController,
    EmployeeBiometricController as EmployeeBiometricController,
    EmployeeDashboardController as EmployeeDashboardController,
    EmployeeDtrController as EmployeeDtrController,
    EmployeePayrollController as EmployeePayrollController
};

use App\Models\User;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('/register', [LoginController::class, 'register'])->middleware('guest')->name('register.post');

// Protected Routes with Policy-based authorization
Route::middleware(['auth'])->group(function () {
    // Role-based dashboard routing
    Route::get('/', function (Request $request) {
        try {
            $user = auth()->user();
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Eager load the role relationship if not already loaded
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            
            if ($user->isAdmin()) {
                return app(AdminDashboardController::class)->index();
            }

            if ($user->isHR()) {
                return redirect()->route('hr.dashboard');
            }
            
            return app(EmployeeDashboardController::class)->index();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Home route error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred. Please try again.');
        }
    })->name('home');

    // Single dashboard route with role-based redirection
    Route::get('/dashboard', function (Request $request) {
        try {
            $user = auth()->user();
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Eager load the role relationship if not already loaded
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            
            if ($user->isAdmin()) {
                return app(AdminDashboardController::class)->index();
            }

            if ($user->isHR()) {
                return redirect()->route('hr.dashboard');
            }
            
            return app(EmployeeDashboardController::class)->index();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard route error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load dashboard. Please refresh the page.');
        }
    })->name('dashboard');

    // ─── HR Staff Routes ──────────────────────────────────────────────────────
    // All routes under /hr/* are gated by can:access_hr (isAdmin || isHR).
    // These are the CANONICAL routes for the HR portal. They delegate to the
    // same admin controllers but only expose HR-appropriate operations
    // (no system management, no destructive deletes on core records).
    Route::middleware(['can:access_hr', 'throttle:admin'])->prefix('hr')->name('hr.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');

        // ── Employee Management ───────────────────────────────────────────────
        // HR can create and edit employees; only Admin can delete user accounts.
        Route::resource('employees', AdminEmployeeController::class)
            ->except(['destroy'])
            ->names('employees');
        Route::get('/employees/export/all', [AdminEmployeeController::class, 'export'])->name('employees.export');

        // ── Org Structure (read-only for HR) ──────────────────────────────────
        Route::resource('departments', AdminDepartmentController::class)
            ->only(['index', 'show'])
            ->names('departments');
        Route::resource('positions', AdminPositionController::class)
            ->only(['index', 'show'])
            ->names('positions');
        Route::get('/org-chart', [AdminOrgChartController::class, 'index'])->name('org-chart.index');

        // ── Travel Orders (HR manages; no delete) ─────────────────────────────
        Route::prefix('travel')->name('travel.')->group(function () {
            Route::get('/',              [AdminTravelController::class, 'index'])->name('index');
            Route::get('/create',        [AdminTravelController::class, 'create'])->name('create');
            Route::post('/',             [AdminTravelController::class, 'store'])->name('store');
            Route::get('/{travel}',      [AdminTravelController::class, 'show'])->name('show');
            Route::get('/{travel}/edit', [AdminTravelController::class, 'edit'])->name('edit');
            Route::put('/{travel}',      [AdminTravelController::class, 'update'])->name('update');
            Route::post('/{travel}/approve',        [AdminTravelController::class, 'approve'])->name('approve');
            Route::post('/{travel}/reject',         [AdminTravelController::class, 'reject'])->name('reject');
            Route::post('/{travel}/mark-completed', [AdminTravelController::class, 'markCompleted'])->name('mark-completed');
            Route::get('/export',        [AdminTravelController::class, 'export'])->name('export');
        });

        // ── Attendance ────────────────────────────────────────────────────────
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/',                  [AdminAttendanceController::class, 'index'])->name('index');
            Route::get('/create',            [AdminAttendanceController::class, 'create'])->name('create');
            Route::post('/',                 [AdminAttendanceController::class, 'store'])->name('store');
            Route::get('/export',            [AdminAttendanceController::class, 'export'])->name('export');
            Route::get('/reports',           [AdminAttendanceController::class, 'reports'])->name('reports');
            Route::get('/department-summary',[AdminAttendanceController::class, 'departmentSummary'])->name('department-summary');
        });

        // ── DTR ───────────────────────────────────────────────────────────────
        Route::prefix('dtr')->name('dtr.')->group(function () {
            Route::get('/',                                [AdminDtrController::class, 'adminIndex'])->name('index');
            Route::get('/export',                          [AdminDtrController::class, 'export'])->name('export');
            Route::get('/export-cs-form-48/{employee}',   [AdminDtrController::class, 'exportCsForm48'])->name('export-cs-form-48');
            Route::get('/preview-cs-form-48/{employee}',  [AdminDtrController::class, 'previewCsForm48'])->name('preview-cs-form-48');
            Route::get('/{employee}/show',                 [AdminDtrController::class, 'show'])->name('show');
            Route::get('/{id}/edit',                       [AdminDtrController::class, 'edit'])->name('edit');
            Route::put('/{id}',                            [AdminDtrController::class, 'update'])->name('update');
        });

        // ── Holidays (HR manages holidays) ────────────────────────────────────
        Route::prefix('holidays')->name('holidays.')->group(function () {
            Route::get('/',              [AdminHolidayController::class, 'index'])->name('index');
            Route::get('/create',        [AdminHolidayController::class, 'create'])->name('create');
            Route::post('/',             [AdminHolidayController::class, 'store'])->name('store');
            Route::get('/{holiday}/edit',[AdminHolidayController::class, 'edit'])->name('edit');
            Route::put('/{holiday}',     [AdminHolidayController::class, 'update'])->name('update');
        });

        // ── Biometrics ────────────────────────────────────────────────────────
        Route::prefix('biometric')->name('biometric.')->group(function () {
            Route::get('/',                  [AdminBiometricController::class, 'index'])->name('index');
            Route::get('/enrolled',          [AdminBiometricController::class, 'enrolled'])->name('enrolled');
            Route::get('/export-enrollments',[AdminBiometricController::class, 'exportEnrollments'])->name('export-enrollments');
            Route::get('/export-audit-logs', [AdminBiometricController::class, 'exportAuditLogs'])->name('export-audit-logs');
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/unenrolled-employees',      [AdminBiometricController::class, 'getUnenrolledEmployees'])->name('unenrolled-employees');
                Route::post('/start-enrollment/{employee}',  [AdminBiometricController::class, 'startEnrollment'])->name('start-enrollment');
                Route::post('/process-enrollment',       [AdminBiometricController::class, 'processEnrollment'])->name('process-enrollment');
                Route::post('/cancel-enrollment',        [AdminBiometricController::class, 'cancelEnrollment'])->name('cancel-enrollment');
                Route::post('/enrollment-status',        [AdminBiometricController::class, 'getEnrollmentStatus'])->name('enrollment-status');
                Route::delete('/remove-enrollment/{employee}', [AdminBiometricController::class, 'removeEnrollment'])->name('remove-enrollment');
            });
        });

        // ── Leave Management (HR certifies; no final approve/delete) ──────────
        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('/',              [AdminLeaveController::class, 'index'])->name('index');
            Route::get('/create',        [AdminLeaveController::class, 'create'])->name('create');
            Route::post('/',             [AdminLeaveController::class, 'store'])->name('store');
            Route::get('/{leave}',       [AdminLeaveController::class, 'show'])->name('show');
            Route::get('/{leave}/certify',  [AdminLeaveController::class, 'certify'])->name('certify');
            Route::post('/{leave}/certify', [AdminLeaveController::class, 'storeCertification'])->name('store-certification');
            Route::get('/{leave}/edit',  [AdminLeaveController::class, 'edit'])->name('edit');
            Route::put('/{leave}',       [AdminLeaveController::class, 'update'])->name('update');
            Route::get('/{leave}/download-pdf', [AdminLeaveController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/{leave}/view-pdf',     [AdminLeaveController::class, 'viewPdf'])->name('view-pdf');
            Route::get('/export',  [AdminLeaveController::class, 'export'])->name('export');
            Route::get('/report',  [AdminLeaveController::class, 'report'])->name('report');
            // HR does NOT get approve/reject/destroy — those belong to Dept Head / President
        });

        // ── Leave Balances ────────────────────────────────────────────────────
        Route::prefix('leave-balance')->name('leave-balance.')->group(function () {
            Route::get('/',              [AdminLeaveBalanceController::class, 'index'])->name('index');
            Route::get('/{employee}',    [AdminLeaveBalanceController::class, 'show'])->name('show');
            Route::get('/{employee}/adjust',   [AdminLeaveBalanceController::class, 'adjust'])->name('adjust');
            Route::post('/{employee}/adjust',  [AdminLeaveBalanceController::class, 'saveAdjustment'])->name('save-adjustment');
            Route::post('/{employee}/grant-credits', [AdminLeaveBalanceController::class, 'grantCredits'])->name('grant-credits');
        });

        // ── Payroll (HR generates/views; no delete, no settings change) ───────
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::prefix('periods')->name('periods.')->group(function () {
                Route::get('/',         [AdminPayrollPeriodController::class, 'index'])->name('index');
                Route::post('/',        [AdminPayrollPeriodController::class, 'store'])->name('store');
                Route::put('/{period}', [AdminPayrollPeriodController::class, 'update'])->name('update');
                // HR cannot delete payroll periods
            });
            Route::prefix('generation')->name('generation.')->group(function () {
                Route::get('/',                          [AdminPayrollGenerationController::class, 'index'])->name('index');
                Route::get('/{period}/validate-dtr',     [AdminPayrollGenerationController::class, 'validateDtr'])->name('validate-dtr');
                Route::get('/{period}/dtr-summary',      [AdminPayrollGenerationController::class, 'getDtrSummary'])->name('dtr-summary');
                Route::post('/{period}/generate',        [AdminPayrollGenerationController::class, 'generatePayroll'])->name('generate');
                Route::get('/results',                   [AdminPayrollGenerationController::class, 'results'])->name('results');
                Route::post('/{employee}/{period}/recalculate', [AdminPayrollGenerationController::class, 'recalculateEmployee'])->name('recalculate');
                Route::get('/{period}/export',           [AdminPayrollGenerationController::class, 'exportPayroll'])->name('export');
                Route::get('/{period}/export-general-sheet', [AdminPayrollGenerationController::class, 'exportGeneralPayrollSheet'])->name('export-general-sheet');
            });
            Route::get('/',         [AdminPayrollController::class, 'index'])->name('index');
            Route::get('/payslips', [AdminPayrollController::class, 'payslips'])->name('payslips');
            Route::get('/export/data', [AdminPayrollController::class, 'export'])->name('export');
            Route::get('/audit-history', [AdminPayrollController::class, 'auditHistory'])->name('audit-history');
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/',                      [AdminPayrollReportController::class, 'index'])->name('index');
                Route::get('/monthly-summary',       [AdminPayrollReportController::class, 'monthlySummary'])->name('monthly-summary');
                Route::get('/department-breakdown',  [AdminPayrollReportController::class, 'departmentBreakdown'])->name('department-breakdown');
                Route::get('/government-contributions', [AdminPayrollReportController::class, 'governmentContributions'])->name('government-contributions');
                Route::get('/ytd-earnings',          [AdminPayrollReportController::class, 'ytdEarnings'])->name('ytd-earnings');
                Route::get('/deductions-allowances', [AdminPayrollReportController::class, 'deductionsAllowances'])->name('deductions-allowances');
                Route::get('/export/monthly-summary-csv', [AdminPayrollReportController::class, 'exportMonthlySummaryCSV'])->name('export-monthly-summary-csv');
                Route::get('/export/government-contributions-csv', [AdminPayrollReportController::class, 'exportGovernmentContributionsCSV'])->name('export-government-contributions-csv');
                Route::get('/export/ytd-earnings-csv', [AdminPayrollReportController::class, 'exportYTDEarningsCSV'])->name('export-ytd-earnings-csv');
            });
            Route::prefix('tax-reports')->name('tax-reports.')->group(function () {
                Route::get('/',                          [AdminTaxReportController::class, 'index'])->name('index');
                Route::get('/breakdown/{period}',        [AdminTaxReportController::class, 'taxBreakdown'])->name('breakdown');
                Route::get('/employee/{employee}',       [AdminTaxReportController::class, 'employeeTaxDetails'])->name('employee-details');
                Route::get('/employee/{employee}/form-2316', [AdminTaxReportController::class, 'downloadForm2316'])->name('download-form-2316');
                Route::get('/export/{period}',           [AdminTaxReportController::class, 'exportTaxReport'])->name('export');
            });
            // Payroll record access (read + payslip download; no delete)
            Route::get('/{id}',                [AdminPayrollController::class, 'show'])->name('show');
            Route::get('/{id}/payslip',        [AdminPayrollController::class, 'payslip'])->name('payslip');
            Route::get('/{id}/download-payslip',[AdminPayrollController::class, 'downloadPayslip'])->name('download-payslip');
            Route::post('/{id}/email-payslip', [AdminPayrollController::class, 'emailPayslip'])->name('email-payslip');
            // Payroll settings — HR read-only
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [AdminPayrollSettingController::class, 'index'])->name('index');
                // HR cannot update payroll settings (no POST)
            });
        });

        // ── Salary Management (read-only for HR) ──────────────────────────────
        Route::prefix('salaries')->name('salaries.')->group(function () {
            Route::get('/',         [AdminSalaryManagementController::class, 'index'])->name('index');
            Route::get('/reports',  [AdminSalaryManagementController::class, 'reports'])->name('reports');
            Route::get('/{id}',     [AdminSalaryManagementController::class, 'show'])->name('show');
        });
        Route::prefix('salary-grades')->name('salary-grades.')->group(function () {
            Route::get('/', [AdminSalaryGradeController::class, 'index'])->name('index');
            Route::post('/get-salary', [AdminSalaryGradeController::class, 'getSalary'])->name('get-salary');
        });

        // ── Allowances & Deductions (HR assigns; no create/delete of types) ───
        Route::prefix('allowances')->name('allowances.')->group(function () {
            Route::get('/', [AdminAllowanceController::class, 'index'])->name('index');
            Route::get('/export/all', [AdminAllowanceController::class, 'export'])->name('export');
            Route::get('/{id}/assign',   [AdminAllowanceController::class, 'assign'])->name('assign');
            Route::post('/{id}/assign',  [AdminAllowanceController::class, 'storeAssignment'])->name('storeAssignment');
            Route::delete('/{allowanceId}/remove/{employeeId}', [AdminAllowanceController::class, 'removeAssignment'])->name('removeAssignment');
        });
        // Deductions view via payroll settings (read-only access)
        Route::get('/deductions', function() {
            return redirect()->route('hr.payroll.settings.index', ['tab' => 'deductions']);
        })->name('deductions.index');

        // ── Performance Management (HR full access) ───────────────────────────
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/reviews',               [AdminPerformanceReviewController::class, 'index'])->name('reviews.index');
            Route::get('/reviews/export',        [AdminPerformanceReviewController::class, 'export'])->name('reviews.export');
            Route::get('/reviews/create',        [AdminPerformanceReviewController::class, 'create'])->name('reviews.create');
            Route::post('/reviews',              [AdminPerformanceReviewController::class, 'store'])->name('reviews.store');
            Route::get('/reviews/{id}',          [AdminPerformanceReviewController::class, 'show'])->name('reviews.show');
            Route::get('/reviews/{id}/evaluate', [AdminPerformanceReviewController::class, 'evaluate'])->name('reviews.evaluate');
            Route::post('/reviews/{id}/evaluate',[AdminPerformanceReviewController::class, 'storeEvaluation'])->name('reviews.storeEvaluation');
            Route::post('/reviews/{id}/update-status', [AdminPerformanceReviewController::class, 'updateStatus'])->name('reviews.updateStatus');
            Route::delete('/reviews/{id}',       [AdminPerformanceReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::get('/analytics',             [AdminPerformanceAnalyticsController::class, 'analytics'])->name('analytics');
            Route::resource('goals', AdminPerformanceGoalController::class)->names('goals');
            Route::get('/goals/export',                    [AdminPerformanceGoalController::class, 'export'])->name('goals.export');
            Route::post('/goals/{id}/update-progress',     [AdminPerformanceGoalController::class, 'updateProgress'])->name('goals.updateProgress');
            Route::get('/goals/employee/{employeeId}',     [AdminPerformanceGoalController::class, 'getEmployeeGoals'])->name('goals.employee');
            Route::resource('criteria', AdminPerformanceCriteriaController::class)->names('criteria');
            Route::post('/criteria/{id}/toggle-status',    [AdminPerformanceCriteriaController::class, 'toggleStatus'])->name('criteria.toggleStatus');
        });

        // ── PDS (HR verifies personal data sheets) ────────────────────────────
        Route::prefix('pds')->name('pds.')->group(function () {
            Route::get('/',           [AdminPDSController::class, 'index'])->name('index');
            Route::get('/show',       [AdminPDSController::class, 'show'])->name('show');
            Route::get('/{pds}',      [AdminPDSController::class, 'show'])->name('show-detail');
            Route::get('/{pds}/edit', [AdminPDSController::class, 'edit'])->name('edit');
            Route::put('/{pds}',      [AdminPDSController::class, 'update'])->name('update');
            Route::post('/{pds}/mark-under-review', [AdminPDSController::class, 'markUnderReview'])->name('mark-under-review');
            Route::post('/{pds}/verify', [AdminPDSController::class, 'verify'])->name('verify');
            Route::post('/{pds}/reject', [AdminPDSController::class, 'reject'])->name('reject');
            Route::get('/employee/{employeeId}', [AdminPDSController::class, 'getByEmployee'])->name('get-by-employee');
        });

        // ── SALN (HR verifies) ────────────────────────────────────────────────
        Route::prefix('saln')->name('saln.')->group(function () {
            Route::get('/',      [AdminSalnController::class, 'index'])->name('index');
            Route::get('/show',  [AdminSalnController::class, 'show'])->name('show');
            Route::get('/{saln}', [AdminSalnController::class, 'show'])->name('show-detail');
            Route::post('/{saln}/verify', [AdminSalnController::class, 'verify'])->name('verify');
            Route::post('/{saln}/flag',   [AdminSalnController::class, 'flag'])->name('flag');
            Route::get('/user/{userId}',  [AdminSalnController::class, 'getByUser'])->name('get-by-user');
        });

        // ── Report Builder ────────────────────────────────────────────────────
        Route::prefix('report-builder')->name('report-builder.')->group(function () {
            Route::get('/',         [AdminReportBuilderController::class, 'index'])->name('index');
            Route::post('/generate',[AdminReportBuilderController::class, 'generate'])->name('generate');
            Route::get('/export',   [AdminReportBuilderController::class, 'export'])->name('export');
        });

        // ── Audit Logs (HR read-only; cannot delete/clear) ───────────────────
        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/',          [AdminAuditLogController::class, 'index'])->name('index');
            Route::get('/{auditLog}',[AdminAuditLogController::class, 'show'])->name('show');
        });

        // ── Notifications ─────────────────────────────────────────────────────
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',                  [AdminNotificationController::class, 'index'])->name('index');
            Route::post('/',                 [AdminNotificationController::class, 'store'])->name('store');
            Route::post('/{id}/mark-read',   [AdminNotificationController::class, 'markRead'])->name('mark-read');
            Route::post('/mark-all-read',    [AdminNotificationController::class, 'markAllRead'])->name('mark-all-read');
            Route::get('/unread-count',      [AdminNotificationController::class, 'unreadCount'])->name('unread-count');
            Route::get('/recent',            [AdminNotificationController::class, 'recent'])->name('recent');
        });
    }); // end HR routes

    // Admin Routes
    Route::middleware(['can:access_admin', 'throttle:admin'])->group(function () {
        Route::resource('admin/employees', AdminEmployeeController::class)->names('admin.employees');
        Route::get('/admin/employees/export/all', [AdminEmployeeController::class, 'export'])->name('admin.employees.export');
        
        // Department Routes
        Route::resource('admin/departments', AdminDepartmentController::class)->names('admin.departments');
        Route::get('/admin/departments/export/all', [AdminDepartmentController::class, 'export'])->name('admin.departments.export');
        Route::get('/admin/departments-list', [AdminDepartmentController::class, 'list'])->name('admin.departments.list');
        Route::get('/admin/departments-statistics', [AdminDepartmentController::class, 'statistics'])->name('admin.departments.statistics');
        Route::post('/admin/departments-bulk-destroy', [AdminDepartmentController::class, 'bulkDestroy'])->name('admin.departments.bulk-destroy');
        // Position Routes
        Route::resource('admin/positions', AdminPositionController::class)->names('admin.positions');
        Route::get('/admin/positions/export/all', [AdminPositionController::class, 'export'])->name('admin.positions.export');
        Route::post('/admin/positions/{id}/toggle-status', [AdminPositionController::class, 'toggleStatus'])->name('admin.positions.toggle-status');
        Route::get('/admin/positions-list', [AdminPositionController::class, 'list'])->name('admin.positions.list');
        Route::get('/admin/positions-statistics', [AdminPositionController::class, 'statistics'])->name('admin.positions.statistics');
        Route::post('/admin/positions-bulk-destroy', [AdminPositionController::class, 'bulkDestroy'])->name('admin.positions.bulk-destroy');

        // Salary Grade Routes
        Route::prefix('admin/salary-grades')->name('admin.salary-grades.')->group(function () {
            Route::get('/', [AdminSalaryGradeController::class, 'index'])->name('index');
            Route::get('/export/all', [AdminSalaryGradeController::class, 'export'])->name('export');
            Route::get('/create', [AdminSalaryGradeController::class, 'create'])->name('create');
            Route::post('/', [AdminSalaryGradeController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminSalaryGradeController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminSalaryGradeController::class, 'update'])->name('update');
            Route::post('/get-salary', [AdminSalaryGradeController::class, 'getSalary'])->name('get-salary');
            Route::post('/deactivate-schedule', [AdminSalaryGradeController::class, 'deactivateSchedule'])->name('deactivate-schedule');
            Route::post('/destroy-schedule', [AdminSalaryGradeController::class, 'destroySchedule'])->name('destroy-schedule');
            Route::delete('/{id}', [AdminSalaryGradeController::class, 'destroy'])->name('destroy');
            Route::post('/update-employee-salaries', [AdminSalaryGradeController::class, 'updateEmployeeSalaries'])->name('update-employee-salaries');
        });

        // ─── ADMIN-ONLY: System Management (blocked for HR Staff) ────────────
        // These routes require isAdmin() via the 'admin.only' middleware.
        Route::middleware(['admin.only'])->group(function () {

        // User Management Routes
        Route::resource('admin/users', AdminUserController::class)->names('admin.users');
        Route::get('/admin/users/export/all', [AdminUserController::class, 'export'])->name('admin.users.export');
        Route::post('/admin/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::post('/admin/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::get('/admin/users-list', [AdminUserController::class, 'list'])->name('admin.users.list');

        // Role Management Routes
        Route::get('/admin/roles/permissions', [AdminRoleController::class, 'permissions'])->name('admin.roles.permissions');
        Route::post('/admin/roles/permissions', [AdminRoleController::class, 'updatePermissions'])->name('admin.roles.permissions.update');
        Route::resource('admin/roles', AdminRoleController::class)->names('admin.roles');
        Route::get('/admin/roles/export/all', [AdminRoleController::class, 'export'])->name('admin.roles.export');

        // System Settings Routes
        Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
        Route::post('/admin/settings/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('admin.settings.clear-cache');

        }); // end admin.only

        // Performance Management Routes
        Route::prefix('admin/performance')->name('admin.performance.')->group(function () {
            // Performance Reviews
            Route::get('/reviews', [AdminPerformanceReviewController::class, 'index'])->name('reviews.index');
            Route::get('/reviews/export', [AdminPerformanceReviewController::class, 'export'])->name('reviews.export');
            Route::get('/reviews/create', [AdminPerformanceReviewController::class, 'create'])->name('reviews.create');
            Route::post('/reviews', [AdminPerformanceReviewController::class, 'store'])->name('reviews.store');
            Route::get('/reviews/{id}', [AdminPerformanceReviewController::class, 'show'])->name('reviews.show');
            Route::get('/reviews/{id}/evaluate', [AdminPerformanceReviewController::class, 'evaluate'])->name('reviews.evaluate');
            Route::post('/reviews/{id}/evaluate', [AdminPerformanceReviewController::class, 'storeEvaluation'])->name('reviews.storeEvaluation');
            Route::post('/reviews/{id}/update-status', [AdminPerformanceReviewController::class, 'updateStatus'])->name('reviews.updateStatus');
            Route::delete('/reviews/{id}', [AdminPerformanceReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::get('/analytics', [AdminPerformanceAnalyticsController::class, 'analytics'])->name('analytics');
            // Performance Goals
            Route::resource('goals', AdminPerformanceGoalController::class);
            Route::get('/goals/export', [AdminPerformanceGoalController::class, 'export'])->name('goals.export');
            Route::post('/goals/{id}/update-progress', [AdminPerformanceGoalController::class, 'updateProgress'])->name('goals.updateProgress');
            Route::get('/goals/employee/{employeeId}', [AdminPerformanceGoalController::class, 'getEmployeeGoals'])->name('goals.employee');

            // Performance Criteria
            Route::resource('criteria', AdminPerformanceCriteriaController::class);
            Route::post('/criteria/{id}/toggle-status', [AdminPerformanceCriteriaController::class, 'toggleStatus'])->name('criteria.toggleStatus');
        });

        // Attendance Routes
        Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
                Route::get('export', [AdminAttendanceController::class, 'export'])->name('export');
                Route::get('reports', [AdminAttendanceController::class, 'reports'])->name('reports');
                Route::get('department-summary', [AdminAttendanceController::class, 'departmentSummary'])->name('department-summary');
                Route::resource('/', AdminAttendanceController::class);
            });

        
        // DTR Routes - Admin
        Route::prefix('admin/dtr')->name('admin.dtr.')->group(function () {
            Route::get('/', [AdminDtrController::class, 'adminIndex'])->name('index');
            Route::get('/export', [AdminDtrController::class, 'export'])->name('export');
            Route::get('/export-cs-form-48/{employee}', [AdminDtrController::class, 'exportCsForm48'])->name('export-cs-form-48');
            Route::get('/preview-cs-form-48/{employee}', [AdminDtrController::class, 'previewCsForm48'])->name('preview-cs-form-48');
            Route::get('/{employee}/show', [AdminDtrController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AdminDtrController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminDtrController::class, 'update'])->name('update');
        });

        // Deduction Routes
        Route::get('/admin/deductions', function() {
            return redirect()->route('admin.payroll.settings.index', ['tab' => 'deductions']);
        })->name('admin.deductions.index');
        Route::resource('admin/deductions', AdminDeductionController::class)->except(['index'])->names('admin.deductions');
        Route::resource('admin/deduction-types', AdminDeductionTypeController::class)->names('admin.deduction-types');
        Route::get('/admin/deductions/export/all', [AdminDeductionController::class, 'export'])->name('admin.deductions.export');
        Route::get('/admin/deductions/{id}/assign', [AdminDeductionController::class, 'assign'])->name('admin.deductions.assign');
        Route::post('/admin/deductions/{id}/assign', [AdminDeductionController::class, 'storeAssignment'])->name('admin.deductions.storeAssignment');
        Route::delete('/admin/deductions/{deductionId}/remove/{employeeId}', [AdminDeductionController::class, 'removeAssignment'])->name('admin.deductions.removeAssignment');

        // Allowance Routes
        Route::resource('admin/allowances', AdminAllowanceController::class)->names('admin.allowances');
        Route::get('/admin/allowances/export/all', [AdminAllowanceController::class, 'export'])->name('admin.allowances.export');
        Route::get('/admin/allowances/{id}/assign', [AdminAllowanceController::class, 'assign'])->name('admin.allowances.assign');
        Route::post('/admin/allowances/{id}/assign', [AdminAllowanceController::class, 'storeAssignment'])->name('admin.allowances.storeAssignment');
        Route::delete('/admin/allowances/{allowanceId}/remove/{employeeId}', [AdminAllowanceController::class, 'removeAssignment'])->name('admin.allowances.removeAssignment');
        Route::prefix('admin/payrolls')->name('admin.payroll.')->group(function () {
            // Payroll Periods Routes
            Route::prefix('periods')->name('periods.')->group(function () {
                Route::get('/', [AdminPayrollPeriodController::class, 'index'])->name('index');
                Route::post('/', [AdminPayrollPeriodController::class, 'store'])->name('store');
                Route::put('/{period}', [AdminPayrollPeriodController::class, 'update'])->name('update');
                Route::delete('/{period}', [AdminPayrollPeriodController::class, 'destroy'])->name('destroy');
            });

            // DTR to Payroll Generation Routes
            Route::prefix('generation')->name('generation.')->group(function () {
                Route::get('/', [AdminPayrollGenerationController::class, 'index'])->name('index');
                Route::get('/{period}/validate-dtr', [AdminPayrollGenerationController::class, 'validateDtr'])->name('validate-dtr');
                Route::get('/{period}/dtr-summary', [AdminPayrollGenerationController::class, 'getDtrSummary'])->name('dtr-summary');
                Route::post('/{period}/generate', [AdminPayrollGenerationController::class, 'generatePayroll'])->name('generate');
                Route::get('/results', [AdminPayrollGenerationController::class, 'results'])->name('results');
                Route::post('/{employee}/{period}/recalculate', [AdminPayrollGenerationController::class, 'recalculateEmployee'])->name('recalculate');
                Route::get('/{period}/export', [AdminPayrollGenerationController::class, 'exportPayroll'])->name('export');
                Route::get('/{period}/export-general-sheet', [AdminPayrollGenerationController::class, 'exportGeneralPayrollSheet'])->name('export-general-sheet');
            });

            Route::get('/', [AdminPayrollController::class, 'index'])->name('index');
            Route::post('/', [AdminPayrollController::class, 'store'])->name('store');
            Route::post('/generate', [AdminPayrollController::class, 'store'])->name('generate');
            Route::get('/export/data', [AdminPayrollController::class, 'export'])->name('export');

            // Payslips listing page
            Route::get('/payslips', [AdminPayrollController::class, 'payslips'])->name('payslips');

            // Payroll Reports - Must come before /{id} routes
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [AdminPayrollReportController::class, 'index'])->name('index');
                Route::get('/monthly-summary', [AdminPayrollReportController::class, 'monthlySummary'])->name('monthly-summary');
                Route::get('/department-breakdown', [AdminPayrollReportController::class, 'departmentBreakdown'])->name('department-breakdown');
                Route::get('/government-contributions', [AdminPayrollReportController::class, 'governmentContributions'])->name('government-contributions');
                Route::get('/ytd-earnings', [AdminPayrollReportController::class, 'ytdEarnings'])->name('ytd-earnings');
                Route::get('/deductions-allowances', [AdminPayrollReportController::class, 'deductionsAllowances'])->name('deductions-allowances');

                // CSV Exports
                Route::get('/export/monthly-summary-csv', [AdminPayrollReportController::class, 'exportMonthlySummaryCSV'])->name('export-monthly-summary-csv');
                Route::get('/export/government-contributions-csv', [AdminPayrollReportController::class, 'exportGovernmentContributionsCSV'])->name('export-government-contributions-csv');
                Route::get('/export/ytd-earnings-csv', [AdminPayrollReportController::class, 'exportYTDEarningsCSV'])->name('export-ytd-earnings-csv');
            });

            // Audit Trail - Must come before /{id} routes
            Route::get('/audit-history', [AdminPayrollController::class, 'auditHistory'])->name('audit-history');
            Route::get('/audit/export/{period}', [AdminPayrollController::class, 'exportAuditReport'])->name('audit-export');

            // Bulk payment Export - Must come before /{id} routes
            Route::get('/bulk-payment-export/{period}', [AdminPayrollController::class, 'bulkPaymentExport'])->name('bulk-payment-export');
            Route::post('/generate-bulk-payment/{period}', [AdminPayrollController::class, 'generateBulkPaymentFile'])->name('generate-bulk-payment');

            // Tax Reports - Must come before /{id} routes
            Route::prefix('tax-reports')->name('tax-reports.')->group(function () {
                Route::get('/', [AdminTaxReportController::class, 'index'])->name('index');
                Route::get('/breakdown/{period}', [AdminTaxReportController::class, 'taxBreakdown'])->name('breakdown');
                Route::get('/employee/{employee}', [AdminTaxReportController::class, 'employeeTaxDetails'])->name('employee-details');
                Route::get('/employee/{employee}/form-2316', [AdminTaxReportController::class, 'downloadForm2316'])->name('download-form-2316');
                Route::get('/export/{period}', [AdminTaxReportController::class, 'exportTaxReport'])->name('export');
                Route::get('/comparison', [AdminTaxReportController::class, 'comparison'])->name('comparison');
                Route::post('/calculate', [AdminTaxReportController::class, 'calculateTax'])->name('calculate');
                Route::get('/brackets', [AdminTaxReportController::class, 'brackets'])->name('brackets');
            });

            // Analytics Dashboard - Must come before /{id} routes
            Route::prefix('analytics')->name('analytics.')->group(function () {
                Route::get('/', [AdminPayrollAnalyticsController::class, 'index'])->name('index');
                Route::get('/export', [AdminPayrollAnalyticsController::class, 'exportAnalytics'])->name('export');
            });

            // Bulk payslip operations - Must come before /{id} routes
            Route::get('/bulk-download', [AdminPayrollController::class, 'bulkDownload'])->name('bulk-download');

            // These routes with {id} parameter must come AFTER specific routes like /reports
            Route::get('/{id}', [AdminPayrollController::class, 'show'])->name('show');
            Route::get('/{id}/detailed-breakdown', [AdminPayrollController::class, 'detailedBreakdown'])->name('detailed-breakdown');
            Route::put('/{id}', [AdminPayrollController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminPayrollController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/payslip', [AdminPayrollController::class, 'payslip'])->name('payslip');
            Route::get('/{id}/download-payslip', [AdminPayrollController::class, 'downloadPayslip'])->name('download-payslip');
            Route::post('/{id}/email-payslip', [AdminPayrollController::class, 'emailPayslip'])->name('email-payslip');
        });

        //Admin Leave routes
            Route::prefix('admin/leave')->name('admin.leave.')->group(function () {
                Route::get('/', [AdminLeaveController::class, 'index'])->name('index');
                Route::get('/create', [AdminLeaveController::class, 'create'])->name('create');
                Route::post('/', [AdminLeaveController::class, 'store'])->name('store');
                Route::get('/{leave}', [AdminLeaveController::class, 'show'])->name('show');
                Route::get('/{leave}/certify', [AdminLeaveController::class, 'certify'])->name('certify');
                Route::post('/{leave}/certify', [AdminLeaveController::class, 'storeCertification'])->name('store-certification');
                Route::get('/{leave}/edit', [AdminLeaveController::class, 'edit'])->name('edit');
                Route::put('/{leave}', [AdminLeaveController::class, 'update'])->name('update');
                Route::delete('/{leave}', [AdminLeaveController::class, 'destroy'])->name('destroy');
                Route::post('/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('approve');
                Route::post('/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('reject');
                Route::post('/{leave}/cancel', [AdminLeaveController::class, 'cancel'])->name('cancel');
                Route::get('/export', [AdminLeaveController::class, 'export'])->name('export');
                Route::get('/report', [AdminLeaveController::class, 'report'])->name('report');
                Route::get('/{leave}/download-pdf', [AdminLeaveController::class, 'downloadPdf'])->name('download-pdf');
                Route::get('/{leave}/view-pdf', [AdminLeaveController::class, 'viewPdf'])->name('view-pdf');
            });

            // Audit Trail
            Route::prefix('admin/audit-logs')->name('admin.audit-logs.')->group(function () {
                Route::get('/', [AdminAuditLogController::class, 'index'])->name('index');
                Route::get('/{auditLog}', [AdminAuditLogController::class, 'show'])->name('show');
                Route::delete('/{auditLog}', [AdminAuditLogController::class, 'destroy'])->name('destroy');
                Route::post('/clear', [AdminAuditLogController::class, 'clear'])->name('clear');
            });

            // Organization Chart
            Route::get('/admin/org-chart', [AdminOrgChartController::class, 'index'])->name('admin.org-chart.index');

            // Notification Center
            Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
                Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
                Route::post('/', [AdminNotificationController::class, 'store'])->name('store');
                Route::post('/{id}/mark-read', [AdminNotificationController::class, 'markRead'])->name('mark-read');
                Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('mark-all-read');
                Route::delete('/{id}', [AdminNotificationController::class, 'destroy'])->name('destroy');
                Route::get('/unread-count', [AdminNotificationController::class, 'unreadCount'])->name('unread-count');
                Route::get('/recent', [AdminNotificationController::class, 'recent'])->name('recent');
            });

            // Report Builder
            Route::prefix('admin/report-builder')->name('admin.report-builder.')->group(function () {
                Route::get('/', [AdminReportBuilderController::class, 'index'])->name('index');
                Route::post('/generate', [AdminReportBuilderController::class, 'generate'])->name('generate');
                Route::get('/export', [AdminReportBuilderController::class, 'export'])->name('export');
            });

            // ─── ADMIN-ONLY: System Infrastructure ────────────────────────────
            Route::middleware(['admin.only'])->group(function () {

            // System Health Dashboard
            Route::prefix('admin/system-health')->name('admin.system-health.')->group(function () {
                Route::get('/', [AdminSystemHealthController::class, 'index'])->name('index');
                Route::post('/clear-cache', [AdminSystemHealthController::class, 'clearCache'])->name('clear-cache');
                Route::post('/clear-failed', [AdminSystemHealthController::class, 'clearFailedJobs'])->name('clear-failed');
                Route::post('/clear-log', [AdminSystemHealthController::class, 'clearLog'])->name('clear-log');
            });

            // Login Sessions Monitor
            Route::prefix('admin/login-sessions')->name('admin.login-sessions.')->group(function () {
                Route::get('/', [AdminLoginSessionController::class, 'index'])->name('index');
                Route::post('/revoke-all', [AdminLoginSessionController::class, 'revokeAll'])->name('revoke-all');
                Route::post('/{loginSession}/revoke', [AdminLoginSessionController::class, 'revoke'])->name('revoke');
                Route::delete('/{loginSession}', [AdminLoginSessionController::class, 'destroy'])->name('destroy');
            });

            // Maintenance Mode Manager
            Route::prefix('admin/maintenance')->name('admin.maintenance.')->group(function () {
                Route::get('/', [AdminMaintenanceController::class, 'index'])->name('index');
                Route::put('/', [AdminMaintenanceController::class, 'update'])->name('update');
                Route::post('/toggle', [AdminMaintenanceController::class, 'toggle'])->name('toggle');
            });

            // Backup Manager
            Route::prefix('admin/backups')->name('admin.backups.')->group(function () {
                Route::get('/', [AdminBackupController::class, 'index'])->name('index');
                Route::post('/', [AdminBackupController::class, 'create'])->name('create');
                Route::get('/{backup}/download', [AdminBackupController::class, 'download'])->name('download');
                Route::delete('/{backup}', [AdminBackupController::class, 'destroy'])->name('destroy');
            });

            // Queue Worker Monitor
            Route::prefix('admin/queue-monitor')->name('admin.queue-monitor.')->group(function () {
                Route::get('/', [AdminQueueMonitorController::class, 'index'])->name('index');
                Route::get('/stats', [AdminQueueMonitorController::class, 'getStats'])->name('stats');
                Route::post('/retry/{id}', [AdminQueueMonitorController::class, 'retryJob'])->name('retry');
                Route::post('/retry-all', [AdminQueueMonitorController::class, 'retryAll'])->name('retry-all');
                Route::delete('/failed/{id}', [AdminQueueMonitorController::class, 'deleteJob'])->name('delete-failed');
                Route::post('/failed/clear-all', [AdminQueueMonitorController::class, 'clearAll'])->name('clear-all');
            });

        // Job Status Routes
        Route::resource('admin/job-statuses', AdminJobStatusController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names('admin.job-statuses');

        // Employment Type Routes
        Route::resource('admin/employment-types', AdminEmploymentTypeController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names('admin.employment-types');

        // Work Schedule Routes
        Route::prefix('admin/work-schedules')->name('admin.work-schedules.')->group(function () {
            Route::get('/', [AdminWorkScheduleController::class, 'index'])->name('index');
            Route::get('/create', [AdminWorkScheduleController::class, 'create'])->name('create');
            Route::post('/', [AdminWorkScheduleController::class, 'store'])->name('store');
            Route::get('/{workSchedule}/edit', [AdminWorkScheduleController::class, 'edit'])->name('edit');
            Route::put('/{workSchedule}', [AdminWorkScheduleController::class, 'update'])->name('update');
            Route::delete('/{workSchedule}', [AdminWorkScheduleController::class, 'destroy'])->name('destroy');
            Route::post('/{workSchedule}/toggle-status', [AdminWorkScheduleController::class, 'toggleStatus'])->name('toggle-status');
        });

            }); // end admin.only (system infrastructure)

        // Leave Balance Routes
        Route::prefix('admin/leave-balance')->name('admin.leave-balance.')->group(function () {
            Route::get('/', [AdminLeaveBalanceController::class, 'index'])->name('index');
            Route::get('/{employee}', [AdminLeaveBalanceController::class, 'show'])->name('show');
            Route::get('/{employee}/adjust', [AdminLeaveBalanceController::class, 'adjust'])->name('adjust');
            Route::post('/{employee}/adjust', [AdminLeaveBalanceController::class, 'saveAdjustment'])->name('save-adjustment');
            Route::post('/{employee}/grant-credits', [AdminLeaveBalanceController::class, 'grantCredits'])->name('grant-credits');
        });

        // Payroll Settings Routes
        Route::prefix('admin/payroll-settings')->name('admin.payroll.settings.')->group(function () {
            Route::get('/', [AdminPayrollSettingController::class, 'index'])->name('index');
            Route::post('/', [AdminPayrollSettingController::class, 'update'])->name('update');
        });

        // Salary Management Routes
        Route::prefix('admin/salaries')->name('admin.salaries.')->group(function () {
            Route::get('/', [AdminSalaryManagementController::class, 'index'])->name('index');
            Route::get('/reports', [AdminSalaryManagementController::class, 'reports'])->name('reports');
            Route::get('/export/history', [AdminSalaryManagementController::class, 'exportHistory'])->name('export-history');
            Route::get('/bulk-adjust', [AdminSalaryManagementController::class, 'bulkAdjustForm'])->name('bulk-adjust-form');
            Route::post('/bulk-adjust', [AdminSalaryManagementController::class, 'bulkAdjust'])->name('bulk-adjust');
            Route::get('/{id}', [AdminSalaryManagementController::class, 'show'])->name('show');
            Route::get('/{id}/adjust', [AdminSalaryManagementController::class, 'adjustForm'])->name('adjust-form');
            Route::post('/{id}/adjust', [AdminSalaryManagementController::class, 'adjust'])->name('adjust');
        });

        // Holidays Route
        Route::prefix('admin/holidays')->name('admin.holidays.')->group(function () {
            Route::get('/', [AdminHolidayController::class, 'index'])->name('index');
            Route::get('/export', function(Request $request) {
                $filters = $request->only(['year', 'type']);
                return \Maatwebsite\Excel\Facades\Excel::download(new HolidayExport($filters), 'holidays_' . now()->format('Y-m-d_His') . '.xlsx');
            })->name('export');
            Route::get('/create', [AdminHolidayController::class, 'create'])->name('create');
            Route::post('/', [AdminHolidayController::class, 'store'])->name('store');
            Route::get('/{holiday}/edit', [AdminHolidayController::class, 'edit'])->name('edit');
            Route::put('/{holiday}', [AdminHolidayController::class, 'update'])->name('update');
            Route::delete('/{holiday}', [AdminHolidayController::class, 'destroy'])->name('destroy');
        });

        // Personal Data Sheet (PDS) Routes
        Route::prefix('admin/pds')->name('admin.pds.')->group(function () {
            Route::get('/', [AdminPDSController::class, 'index'])->name('index');
            Route::get('/create', [AdminPDSController::class, 'create'])->name('create');
            Route::post('/', [AdminPDSController::class, 'store'])->name('store');
            Route::get('/show', [AdminPDSController::class, 'show'])->name('show');
            Route::get('/{pds}', [AdminPDSController::class, 'show'])->name('show-detail');
            Route::get('/{pds}/edit', [AdminPDSController::class, 'edit'])->name('edit');
            Route::put('/{pds}', [AdminPDSController::class, 'update'])->name('update');
            Route::delete('/{pds}', [AdminPDSController::class, 'destroy'])->name('destroy');
            Route::post('/{pds}/mark-under-review', [AdminPDSController::class, 'markUnderReview'])->name('mark-under-review');
            Route::post('/{pds}/verify', [AdminPDSController::class, 'verify'])->name('verify');
            Route::post('/{pds}/reject', [AdminPDSController::class, 'reject'])->name('reject');
            Route::get('/filter/status', [AdminPDSController::class, 'filterByStatus'])->name('filter-status');
            Route::get('/export', [AdminPDSController::class, 'export'])->name('export');
            Route::get('/employee/{employeeId}', [AdminPDSController::class, 'getByEmployee'])->name('get-by-employee');
        });

        // Statement of Assets, Liabilities and Net Worth (SALN) Routes
        Route::prefix('admin/saln')->name('admin.saln.')->group(function () {
            Route::get('/', [AdminSalnController::class, 'index'])->name('index');
            Route::get('/create', [AdminSalnController::class, 'create'])->name('create');
            Route::post('/', [AdminSalnController::class, 'store'])->name('store');
            Route::get('/show', [AdminSalnController::class, 'show'])->name('show');
            Route::get('/{saln}', [AdminSalnController::class, 'show'])->name('show-detail');
            Route::get('/{saln}/edit', [AdminSalnController::class, 'edit'])->name('edit');
            Route::put('/{saln}', [AdminSalnController::class, 'update'])->name('update');
            Route::delete('/{saln}', [AdminSalnController::class, 'destroy'])->name('destroy');
            Route::post('/{saln}/verify', [AdminSalnController::class, 'verify'])->name('verify');
            Route::post('/{saln}/flag', [AdminSalnController::class, 'flag'])->name('flag');
            Route::get('/filter/status', [AdminSalnController::class, 'filterByStatus'])->name('filter-status');
            Route::get('/export', [AdminSalnController::class, 'export'])->name('export');
            Route::get('/user/{userId}', [AdminSalnController::class, 'getByUser'])->name('get-by-user');
        });

        // Travel Authority Routes
        Route::prefix('admin/travel')->name('admin.travel.')->group(function () {
            Route::get('/', [AdminTravelController::class, 'index'])->name('index');
            Route::get('/create', [AdminTravelController::class, 'create'])->name('create');
            Route::post('/', [AdminTravelController::class, 'store'])->name('store');
            Route::get('/{travel}', [AdminTravelController::class, 'show'])->name('show');
            Route::get('/{travel}/edit', [AdminTravelController::class, 'edit'])->name('edit');
            Route::put('/{travel}', [AdminTravelController::class, 'update'])->name('update');
            Route::delete('/{travel}', [AdminTravelController::class, 'destroy'])->name('destroy');
            Route::post('/{travel}/approve', [AdminTravelController::class, 'approve'])->name('approve');
            Route::post('/{travel}/reject', [AdminTravelController::class, 'reject'])->name('reject');
            Route::post('/{travel}/mark-completed', [AdminTravelController::class, 'markCompleted'])->name('mark-completed');
            Route::get('/filter/status', [AdminTravelController::class, 'filterByStatus'])->name('filter-status');
            Route::get('/export', [AdminTravelController::class, 'export'])->name('export');
            Route::get('/employee/{employeeId}', [AdminTravelController::class, 'getByEmployee'])->name('get-by-employee');
        });

        // Biometric Enrollment Management Routes
        Route::prefix('admin/biometric')->name('admin.biometric.')->group(function () {
            Route::get('/', [AdminBiometricController::class, 'index'])->name('index');
            Route::get('/enrolled', [AdminBiometricController::class, 'enrolled'])->name('enrolled');
            Route::get('/export-enrollments', [AdminBiometricController::class, 'exportEnrollments'])->name('export-enrollments');
            Route::get('/export-audit-logs', [AdminBiometricController::class, 'exportAuditLogs'])->name('export-audit-logs');
            
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/unenrolled-employees', [AdminBiometricController::class, 'getUnenrolledEmployees'])
                    ->name('unenrolled-employees');
                Route::post('/start-enrollment/{employee}', [AdminBiometricController::class, 'startEnrollment'])
                    ->name('start-enrollment');
                Route::post('/process-enrollment', [AdminBiometricController::class, 'processEnrollment'])
                    ->name('process-enrollment');
                Route::post('/cancel-enrollment', [AdminBiometricController::class, 'cancelEnrollment'])
                    ->name('cancel-enrollment');
                Route::post('/enrollment-status', [AdminBiometricController::class, 'getEnrollmentStatus'])
                    ->name('enrollment-status');
                Route::delete('/remove-enrollment/{employee}', [AdminBiometricController::class, 'removeEnrollment'])
                    ->name('remove-enrollment');
                Route::get('/statistics', [AdminBiometricController::class, 'getStatistics'])
                    ->name('statistics');
            });
        });
    });

    // Employee Routes
    Route::prefix('employee')->name('employee.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        
        // Check-in/Check-out
        Route::post('/check-in', [EmployeeDashboardController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [EmployeeDashboardController::class, 'checkOut'])->name('checkout');
        Route::get('/attendance-status', [EmployeeDashboardController::class, 'attendanceStatus'])->name('attendance.status');

        // Profile
        Route::get('/profile', [EmployeeController::class, 'myProfile'])->name('profile');
        Route::put('/profile', [EmployeeController::class, 'updateMyProfile'])->name('profile.update');

        // Attendance & DTR
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [EmployeeAttendanceController::class, 'index'])->name('index');
            Route::get('/history', [EmployeeAttendanceController::class, 'history'])->name('history');
            Route::get('/export', [EmployeeAttendanceController::class, 'export'])->name('export');
            Route::get('/request', [EmployeeAttendanceController::class, 'requestForm'])->name('request');
            Route::post('/request', [EmployeeAttendanceController::class, 'submitRequest'])->name('request.submit');
        });

        // DTR (Daily Time Record)
        Route::prefix('dtr')->name('dtr.')->group(function () {
            Route::get('/', [EmployeeDtrController::class, 'index'])->name('index');
            Route::get('/export', [EmployeeDtrController::class, 'exportPdf'])->name('export');
            Route::get('/export-cs-form-48', [DtrController::class, 'exportMyCsForm48'])->name('export-cs-form-48');
            Route::get('/{month}', [EmployeeDtrController::class, 'show'])->name('show');
        });

        // Biometricmy-attendance
        Route::prefix('biometric')->name('biometric.')->group(function () {
            Route::get('/status', [EmployeeBiometricController::class, 'status'])->name('status');
            Route::post('/enroll-fingerprint', [EmployeeBiometricController::class, 'enrollFingerprint'])->name('enroll');
            Route::post('/test-fingerprint', [EmployeeBiometricController::class, 'testFingerprint'])->name('test');
            Route::post('/request-rfid', [EmployeeBiometricController::class, 'requestRFID'])->name('request-rfid');
            Route::post('/report-lost-rfid', [EmployeeBiometricController::class, 'reportLostRFID'])->name('report-lost-rfid');
            Route::get('/biometric-logs', [EmployeeBiometricController::class, 'logs'])->name('logs');
            Route::get('/biometric-logs/export', [EmployeeBiometricController::class, 'export'])->name('logs.export');
            Route::get('/biometric-logs/details', [EmployeeBiometricController::class, 'showDetails'])->name('logs.details');
        });

        // Payroll
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [EmployeePayrollController::class, 'index'])->name('index');
            Route::get('/history', [EmployeePayrollController::class, 'history'])->name('history');
            Route::get('/export-history', [EmployeePayrollController::class, 'exportHistory'])->name('export-history');
            Route::get('/yearly-summary', [EmployeePayrollController::class, 'getYearlySummary'])->name('yearly-summary');
            Route::get('/payslips', [EmployeePayrollController::class, 'payslips'])->name('payslips');
            Route::get('/payslip/{id}', [EmployeePayrollController::class, 'payslip'])->name('payslip');
            Route::get('/deductions', [EmployeePayrollController::class, 'deductions'])->name('deductions');
            Route::get('/tax-info', [EmployeePayrollController::class, 'taxInfo'])->name('tax-info');

        });

        // Documents
        Route::get('/documents', [EmployeeController::class, 'documents'])->name('documents');
        
        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [EmployeeController::class, 'notifications'])->name('index');
            Route::post('/{id}/mark-read', [EmployeeController::class, 'markNotificationRead'])->name('mark-read');
            Route::post('/mark-all-read', [EmployeeController::class, 'markAllNotificationsRead'])->name('mark-all-read');
        });

        // Account Settings & Management
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [EmployeeAccountController::class, 'index'])->name('index');
            
            // Profile Management
            Route::get('/profile/edit', [EmployeeAccountController::class, 'editProfile'])->name('profile.edit');
            Route::put('/profile', [EmployeeAccountController::class, 'updateProfile'])->name('profile.update');
            
            // Password Management
            Route::get('/password/change', [EmployeeAccountController::class, 'editPassword'])->name('password.edit');
            Route::put('/password', [EmployeeAccountController::class, 'updatePassword'])->name('password.update');
            
            // Email Management
            Route::get('/email/change', [EmployeeAccountController::class, 'editEmail'])->name('email.edit');
            Route::put('/email', [EmployeeAccountController::class, 'updateEmail'])->name('email.update');
            
            // Two-Factor Authentication
            Route::get('/security/two-factor', [EmployeeAccountController::class, 'editTwoFactor'])->name('two-factor.edit');
            Route::post('/security/two-factor/enable', [EmployeeAccountController::class, 'enableTwoFactor'])->name('two-factor.enable');
            Route::get('/security/two-factor/verify', [EmployeeAccountController::class, 'verifyTwoFactor'])->name('twoFactorVerify');
            Route::post('/security/two-factor/verify', [EmployeeAccountController::class, 'verifyTwoFactor'])->name('two-factor.verify');
            Route::post('/security/two-factor/disable', [EmployeeAccountController::class, 'disableTwoFactor'])->name('two-factor.disable');
            
            // Activity & Login History
            Route::get('/activity', [EmployeeAccountController::class, 'activityLog'])->name('activity');
            
            // Sessions & Devices
            Route::get('/sessions', [EmployeeAccountController::class, 'sessions'])->name('sessions');
            Route::delete('/sessions/{sessionId}', [EmployeeAccountController::class, 'logoutSession'])->name('sessions.logout');
            
            // Preferences
            Route::get('/preferences', [EmployeeAccountController::class, 'preferences'])->name('preferences');
            Route::put('/preferences', [EmployeeAccountController::class, 'updatePreferences'])->name('preferences.update');
            
            // Account Deletion
            Route::get('/delete', [EmployeeAccountController::class, 'editDelete'])->name('delete.edit');
            Route::delete('/', [EmployeeAccountController::class, 'deleteAccount'])->name('delete');
        });
    });

    // Legacy routes for backward compatibility
    Route::get('/my-profile', [EmployeeController::class, 'myProfile'])->name('my-profile');
    Route::put('/my-profile', [EmployeeController::class, 'updateMyProfile'])->name('my-profile.update');
    Route::get('/my-attendance', [EmployeeAttendanceController::class, 'index'])->name('my-attendance');
    Route::get('/my-payroll', [EmployeePayrollController::class, 'index'])->name('my-payroll');
    Route::get('/my-dtr', [EmployeeDtrController::class, 'index'])->name('my-dtr');
});

// Debug route
Route::get('/debug-user', function () {
    if (!auth()->check()) {
        return response()->json(['message' => 'No user logged in']);
    }

    $user = auth()->user(); /** @var User $user */

    return response()->json([
        'user_id' => $user->id,
        'role_id' => $user->role_id,
        'role_name' => $user->role->name ?? 'NO ROLE SET',
        'attributes' => $user->getAttributes(),
    ]);
})->middleware('auth');

// Debug route for payroll validation
Route::get('/debug-payroll-validation', function () {
    $activeStatus = \App\Models\JobStatus::where('name', 'Active')->first();
    
    $employeesCount = \App\Models\Employee::count();
    $activeEmployeesCount = \App\Models\Employee::where('job_status_id', $activeStatus?->id)->count();
    $employeesViaRelation = \App\Models\Employee::whereHas('jobStatus', function($q) {
        $q->where('name', 'Active');
    })->count();

    return response()->json([
        'active_status' => $activeStatus,
        'total_employees' => $employeesCount,
        'active_employees_by_id' => $activeEmployeesCount,
        'active_employees_via_relation' => $employeesViaRelation,
        'sample_employee' => \App\Models\Employee::first(),
    ]);
});