<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollGenerationController;
use App\Http\Controllers\TaxReportController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\DtrController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PayrollAnalyticsController;
use App\Models\User;

use App\Http\Controllers\Employee\{
    EmployeeAttendanceController,
    EmployeeBiometricController,
    EmployeeDashboardController,
    EmployeeDtrController,
    EmployeePayrollController
};

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.post');

// Protected Routes with Policy-based authorization
Route::middleware(['auth'])->group(function () {
    // Role-based dashboard routing
    Route::get('/', function () {
        $user = auth()->user(); /** @var \App\Models\User $user */
        if ($user->isAdmin() || $user->isHR()) {
            return app(DashboardController::class)->index();
        } else {
            return app(EmployeeDashboardController::class)->index();
        }
    })->name('dashboard');

    Route::get('/dashboard', function () {
        $user = auth()->user(); /** @var \App\Models\User $user */
        if ($user->isAdmin() || $user->isHR()) {
            return app(DashboardController::class)->index();
        } else {
            return app(EmployeeDashboardController::class)->index();
        }
    });

    // Admin/HR Routes
    Route::middleware(['can:access_admin'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::get('/employees/export/all', [EmployeeController::class, 'export'])->name('employees.export');

        // Department Routes
        Route::resource('departments', DepartmentController::class);
        Route::get('/departments/export/all', [DepartmentController::class, 'export'])->name('departments.export');
        Route::get('/departments-list', [DepartmentController::class, 'list'])->name('departments.list');
        Route::get('/departments-statistics', [DepartmentController::class, 'statistics'])->name('departments.statistics');
        Route::post('/departments-bulk-destroy', [DepartmentController::class, 'bulkDestroy'])->name('departments.bulk-destroy');

        // Position Routes
        Route::resource('positions', \App\Http\Controllers\PositionController::class);
        Route::get('/positions/export/all', [\App\Http\Controllers\PositionController::class, 'export'])->name('positions.export');
        Route::post('/positions/{id}/toggle-status', [\App\Http\Controllers\PositionController::class, 'toggleStatus'])->name('positions.toggle-status');
        Route::get('/positions-list', [\App\Http\Controllers\PositionController::class, 'list'])->name('positions.list');
        Route::get('/positions-statistics', [\App\Http\Controllers\PositionController::class, 'statistics'])->name('positions.statistics');
        Route::post('/positions-bulk-destroy', [\App\Http\Controllers\PositionController::class, 'bulkDestroy'])->name('positions.bulk-destroy');

        // Salary Grade Routes
        Route::prefix('salary-grades')->name('salary-grades.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SalaryGradeController::class, 'index'])->name('index');
            Route::get('/export/all', [\App\Http\Controllers\SalaryGradeController::class, 'export'])->name('export');
            Route::get('/create', [\App\Http\Controllers\SalaryGradeController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\SalaryGradeController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\SalaryGradeController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\SalaryGradeController::class, 'update'])->name('update');
            Route::post('/get-salary', [\App\Http\Controllers\SalaryGradeController::class, 'getSalary'])->name('get-salary');
            Route::post('/deactivate-schedule', [\App\Http\Controllers\SalaryGradeController::class, 'deactivateSchedule'])->name('deactivate-schedule');
            Route::post('/destroy-schedule', [\App\Http\Controllers\SalaryGradeController::class, 'destroySchedule'])->name('destroy-schedule');
            Route::delete('/{id}', [\App\Http\Controllers\SalaryGradeController::class, 'destroy'])->name('destroy');
            Route::post('/update-employee-salaries', [\App\Http\Controllers\SalaryGradeController::class, 'updateEmployeeSalaries'])->name('update-employee-salaries');
        });

        // User Management Routes
        Route::resource('users', UserController::class);
        Route::get('/users/export/all', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users-list', [UserController::class, 'list'])->name('users.list');

        // Role Management Routes
        Route::resource('roles', RoleController::class);
        Route::get('/roles/export/all', [RoleController::class, 'export'])->name('roles.export');

        // Performance Management Routes
        Route::prefix('performance')->name('performance.')->group(function () {
            // Performance Reviews
            Route::get('/reviews', [\App\Http\Controllers\PerformanceReviewController::class, 'index'])->name('reviews.index');
            Route::get('/reviews/export', [\App\Http\Controllers\PerformanceReviewController::class, 'export'])->name('reviews.export');
            Route::get('/reviews/create', [\App\Http\Controllers\PerformanceReviewController::class, 'create'])->name('reviews.create');
            Route::post('/reviews', [\App\Http\Controllers\PerformanceReviewController::class, 'store'])->name('reviews.store');
            Route::get('/reviews/{id}', [\App\Http\Controllers\PerformanceReviewController::class, 'show'])->name('reviews.show');
            Route::get('/reviews/{id}/evaluate', [\App\Http\Controllers\PerformanceReviewController::class, 'evaluate'])->name('reviews.evaluate');
            Route::post('/reviews/{id}/evaluate', [\App\Http\Controllers\PerformanceReviewController::class, 'storeEvaluation'])->name('reviews.storeEvaluation');
            Route::post('/reviews/{id}/update-status', [\App\Http\Controllers\PerformanceReviewController::class, 'updateStatus'])->name('reviews.updateStatus');
            Route::delete('/reviews/{id}', [\App\Http\Controllers\PerformanceReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::get('/analytics', [\App\Http\Controllers\PerformanceReviewController::class, 'analytics'])->name('analytics');

            // Performance Goals
            Route::resource('goals', \App\Http\Controllers\PerformanceGoalController::class);
            Route::get('/goals/export', [\App\Http\Controllers\PerformanceGoalController::class, 'export'])->name('goals.export');
            Route::post('/goals/{id}/update-progress', [\App\Http\Controllers\PerformanceGoalController::class, 'updateProgress'])->name('goals.updateProgress');
            Route::get('/goals/employee/{employeeId}', [\App\Http\Controllers\PerformanceGoalController::class, 'getEmployeeGoals'])->name('goals.employee');

            // Performance Criteria
            Route::resource('criteria', \App\Http\Controllers\PerformanceCriteriaController::class);
            Route::post('/criteria/{id}/toggle-status', [\App\Http\Controllers\PerformanceCriteriaController::class, 'toggleStatus'])->name('criteria.toggleStatus');
        });

        // Attendance Routes
        Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
        Route::get('/attendance/reports', [AttendanceController::class, 'reports'])->name('attendance.reports');
        Route::get('/attendance/department-summary', [AttendanceController::class, 'departmentSummary'])->name('attendance.department-summary');
        Route::resource('attendance', AttendanceController::class);
        // DTR Routes - Admin
        Route::prefix('dtr')->name('dtr.')->group(function () {
            Route::get('/', [DtrController::class, 'adminIndex'])->name('index');
            Route::get('/export', [DtrController::class, 'export'])->name('export');
            Route::get('/export-cs-form-48/{employee}', [DtrController::class, 'exportCsForm48'])->name('export-cs-form-48');
            Route::get('/preview-cs-form-48/{employee}', [DtrController::class, 'previewCsForm48'])->name('preview-cs-form-48');
            Route::get('/{employee}/show', [DtrController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [DtrController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DtrController::class, 'update'])->name('update');
        });

        // Deduction Routes
        Route::get('/deductions', function() {
            return redirect()->route('payroll.settings.index', ['tab' => 'deductions']);
        })->name('deductions.index');
        Route::resource('deductions', \App\Http\Controllers\DeductionController::class)->except(['index']);
        Route::resource('deduction-types', \App\Http\Controllers\DeductionTypeController::class);
        Route::get('/deductions/export/all', [\App\Http\Controllers\DeductionController::class, 'export'])->name('deductions.export');
        Route::get('/deductions/{id}/assign', [\App\Http\Controllers\DeductionController::class, 'assign'])->name('deductions.assign');
        Route::post('/deductions/{id}/assign', [\App\Http\Controllers\DeductionController::class, 'storeAssignment'])->name('deductions.storeAssignment');
        Route::delete('/deductions/{deductionId}/remove/{employeeId}', [\App\Http\Controllers\DeductionController::class, 'removeAssignment'])->name('deductions.removeAssignment');

        // Allowance Routes
        Route::resource('allowances', \App\Http\Controllers\AllowanceController::class);
        Route::get('/allowances/export/all', [\App\Http\Controllers\AllowanceController::class, 'export'])->name('allowances.export');
        Route::get('/allowances/{id}/assign', [\App\Http\Controllers\AllowanceController::class, 'assign'])->name('allowances.assign');
        Route::post('/allowances/{id}/assign', [\App\Http\Controllers\AllowanceController::class, 'storeAssignment'])->name('allowances.storeAssignment');
        Route::delete('/allowances/{allowanceId}/remove/{employeeId}', [\App\Http\Controllers\AllowanceController::class, 'removeAssignment'])->name('allowances.removeAssignment');
        Route::prefix('payrolls')->name('payroll.')->group(function () {
            // Payroll Periods Routes
            Route::prefix('periods')->name('periods.')->group(function () {
                Route::get('/', [\App\Http\Controllers\PayrollPeriodController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\PayrollPeriodController::class, 'store'])->name('store');
                Route::put('/{period}', [\App\Http\Controllers\PayrollPeriodController::class, 'update'])->name('update');
                Route::delete('/{period}', [\App\Http\Controllers\PayrollPeriodController::class, 'destroy'])->name('destroy');
            });

            // DTR to Payroll Generation Routes
            Route::prefix('generation')->name('generation.')->group(function () {
                Route::get('/', [\App\Http\Controllers\PayrollGenerationController::class, 'index'])->name('index');
                Route::get('/{period}/validate-dtr', [\App\Http\Controllers\PayrollGenerationController::class, 'validateDtr'])->name('validate-dtr');
                Route::get('/{period}/dtr-summary', [\App\Http\Controllers\PayrollGenerationController::class, 'getDtrSummary'])->name('dtr-summary');
                Route::post('/{period}/generate', [\App\Http\Controllers\PayrollGenerationController::class, 'generatePayroll'])->name('generate');
                Route::get('/results', [\App\Http\Controllers\PayrollGenerationController::class, 'results'])->name('results');
                Route::post('/{employee}/{period}/recalculate', [\App\Http\Controllers\PayrollGenerationController::class, 'recalculateEmployee'])->name('recalculate');
                Route::get('/{period}/export', [\App\Http\Controllers\PayrollGenerationController::class, 'exportPayroll'])->name('export');
                Route::get('/{period}/export-general-sheet', [\App\Http\Controllers\PayrollGenerationController::class, 'exportGeneralPayrollSheet'])->name('export-general-sheet');
            });

            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('/', [PayrollController::class, 'store'])->name('store');
            Route::post('/generate', [PayrollController::class, 'store'])->name('generate');
            Route::get('/export/data', [PayrollController::class, 'export'])->name('export');

            // Payslips listing page
            Route::get('/payslips', [PayrollController::class, 'payslips'])->name('payslips');

            // Payroll Reports - Must come before /{id} routes
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [\App\Http\Controllers\PayrollReportController::class, 'index'])->name('index');
                Route::get('/monthly-summary', [\App\Http\Controllers\PayrollReportController::class, 'monthlySummary'])->name('monthly-summary');
                Route::get('/department-breakdown', [\App\Http\Controllers\PayrollReportController::class, 'departmentBreakdown'])->name('department-breakdown');
                Route::get('/government-contributions', [\App\Http\Controllers\PayrollReportController::class, 'governmentContributions'])->name('government-contributions');
                Route::get('/ytd-earnings', [\App\Http\Controllers\PayrollReportController::class, 'ytdEarnings'])->name('ytd-earnings');
                Route::get('/deductions-allowances', [\App\Http\Controllers\PayrollReportController::class, 'deductionsAllowances'])->name('deductions-allowances');

                // CSV Exports
                Route::get('/export/monthly-summary-csv', [\App\Http\Controllers\PayrollReportController::class, 'exportMonthlySummaryCSV'])->name('export-monthly-summary-csv');
                Route::get('/export/government-contributions-csv', [\App\Http\Controllers\PayrollReportController::class, 'exportGovernmentContributionsCSV'])->name('export-government-contributions-csv');
                Route::get('/export/ytd-earnings-csv', [\App\Http\Controllers\PayrollReportController::class, 'exportYTDEarningsCSV'])->name('export-ytd-earnings-csv');
            });

            // Audit Trail - Must come before /{id} routes
            Route::get('/audit-history', [PayrollController::class, 'auditHistory'])->name('audit-history');
            Route::get('/audit/export/{period}', [PayrollController::class, 'exportAuditReport'])->name('audit-export');

            // Bulk payment Export - Must come before /{id} routes
            Route::get('/bulk-payment-export/{period}', [PayrollController::class, 'bulkPaymentExport'])->name('bulk-payment-export');
            Route::post('/generate-bulk-payment/{period}', [PayrollController::class, 'generateBulkPaymentFile'])->name('generate-bulk-payment');

            // Tax Reports - Must come before /{id} routes
            Route::prefix('tax-reports')->name('tax-reports.')->group(function () {
                Route::get('/', [\App\Http\Controllers\TaxReportController::class, 'index'])->name('index');
                Route::get('/breakdown/{period}', [\App\Http\Controllers\TaxReportController::class, 'taxBreakdown'])->name('breakdown');
                Route::get('/employee/{employee}', [\App\Http\Controllers\TaxReportController::class, 'employeeTaxDetails'])->name('employee-details');
                Route::get('/employee/{employee}/form-2316', [\App\Http\Controllers\TaxReportController::class, 'downloadForm2316'])->name('download-form-2316');
                Route::get('/export/{period}', [\App\Http\Controllers\TaxReportController::class, 'exportTaxReport'])->name('export');
                Route::get('/comparison', [\App\Http\Controllers\TaxReportController::class, 'comparison'])->name('comparison');
                Route::post('/calculate', [\App\Http\Controllers\TaxReportController::class, 'calculateTax'])->name('calculate');
                Route::get('/brackets', [\App\Http\Controllers\TaxReportController::class, 'brackets'])->name('brackets');
            });

            // Analytics Dashboard - Must come before /{id} routes
            Route::prefix('analytics')->name('analytics.')->group(function () {
                Route::get('/', [\App\Http\Controllers\PayrollAnalyticsController::class, 'index'])->name('index');
                Route::get('/export', [\App\Http\Controllers\PayrollAnalyticsController::class, 'exportAnalytics'])->name('export');
            });

            // Bulk payslip operations - Must come before /{id} routes
            Route::get('/bulk-download', [PayrollController::class, 'bulkDownload'])->name('bulk-download');

            // These routes with {id} parameter must come AFTER specific routes like /reports
            Route::get('/{id}', [PayrollController::class, 'show'])->name('show');
            Route::get('/{id}/detailed-breakdown', [PayrollController::class, 'detailedBreakdown'])->name('detailed-breakdown');
            Route::put('/{id}', [PayrollController::class, 'update'])->name('update');
            Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/payslip', [PayrollController::class, 'payslip'])->name('payslip');
            Route::get('/{id}/download-payslip', [PayrollController::class, 'downloadPayslip'])->name('download-payslip');
            Route::post('/{id}/email-payslip', [PayrollController::class, 'emailPayslip'])->name('email-payslip');
        });

        // Payroll Settings Routes
        Route::prefix('payroll-settings')->name('payroll.settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PayrollSettingController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\PayrollSettingController::class, 'update'])->name('update');
        });

        // Salary Management Routes
        Route::prefix('salaries')->name('salaries.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SalaryManagementController::class, 'index'])->name('index');
            Route::get('/reports', [\App\Http\Controllers\SalaryManagementController::class, 'reports'])->name('reports');
            Route::get('/export/history', [\App\Http\Controllers\SalaryManagementController::class, 'exportHistory'])->name('export-history');
            Route::get('/bulk-adjust', [\App\Http\Controllers\SalaryManagementController::class, 'bulkAdjustForm'])->name('bulk-adjust-form');
            Route::post('/bulk-adjust', [\App\Http\Controllers\SalaryManagementController::class, 'bulkAdjust'])->name('bulk-adjust');
            Route::get('/{id}', [\App\Http\Controllers\SalaryManagementController::class, 'show'])->name('show');
            Route::get('/{id}/adjust', [\App\Http\Controllers\SalaryManagementController::class, 'adjustForm'])->name('adjust-form');
            Route::post('/{id}/adjust', [\App\Http\Controllers\SalaryManagementController::class, 'adjust'])->name('adjust');
        });

        // Holidays Route
        Route::prefix('holidays')->name('holidays.')->group(function () {
            Route::get('/', [HolidayController::class, 'index'])->name('index');
            Route::get('/export', function(Request $request) {
                $filters = $request->only(['year', 'type']);
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\HolidayExport($filters), 'holidays_' . now()->format('Y-m-d_His') . '.xlsx');
            })->name('export');
        });

        // Biometric Enrollment Management Routes
        Route::prefix('biometric')->name('biometric.')->group(function () {
            Route::get('/', [BiometricController::class, 'index'])->name('index');
            Route::get('/enrolled', [BiometricController::class, 'enrolled'])->name('enrolled');
            Route::get('/export-enrollments', [BiometricController::class, 'exportEnrollments'])->name('export-enrollments');
            Route::get('/export-audit-logs', [BiometricController::class, 'exportAuditLogs'])->name('export-audit-logs');
            
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/unenrolled-employees', [BiometricController::class, 'getUnenrolledEmployees'])
                    ->name('unenrolled-employees');
                Route::post('/start-enrollment/{employee}', [BiometricController::class, 'startEnrollment'])
                    ->name('start-enrollment');
                Route::post('/process-enrollment', [BiometricController::class, 'processEnrollment'])
                    ->name('process-enrollment');
                Route::post('/cancel-enrollment', [BiometricController::class, 'cancelEnrollment'])
                    ->name('cancel-enrollment');
                Route::post('/enrollment-status', [BiometricController::class, 'getEnrollmentStatus'])
                    ->name('enrollment-status');
                Route::delete('/remove-enrollment/{employee}', [BiometricController::class, 'removeEnrollment'])
                    ->name('remove-enrollment');
                Route::get('/statistics', [BiometricController::class, 'getStatistics'])
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