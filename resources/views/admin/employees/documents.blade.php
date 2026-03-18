@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Document Exports</h1>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- HR & Administration Documents -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Employee Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.employees.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Employee Directory</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Export all employee details and information</p>
                        </a>
                        
                        <a href="{{ route('admin.departments.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Department Report</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Department structure and employee distribution</p>
                        </a>
                        
                        <a href="{{ route('admin.positions.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Position Assignments</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">All job positions and employee assignments</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compensation & Salary Documents -->
        <div class="col-md-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> Compensation Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.salary-grades.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Salary Grade Schedule</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">All salary grades and steps with amounts</p>
                        </a>
                        
                        <a href="{{ route('admin.salaries.export-history') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Salary Adjustment History</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Track all salary changes and adjustments</p>
                        </a>
                        
                        <a href="{{ route('admin.allowances.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Allowances Master</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">All allowance types and configurations</p>
                        </a>
                        
                        <a href="{{ route('admin.deductions.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Deductions Master</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">All deduction types and configurations</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll & Attendance Documents -->
        <div class="col-md-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> Payroll & Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.payroll.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Payroll Records</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Complete payroll data with all deductions and allowances</p>
                        </a>
                        
                        <a href="{{ route('admin.dtr.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Daily Time Records</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">DTR entries and attendance records</p>
                        </a>
                        
                        <a href="{{ route('admin.attendance.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Attendance Summary</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Attendance statistics and summaries</p>
                        </a>
                        
                        <a href="{{ route('admin.holidays.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Holiday Calendar</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Official holidays and special non-working days</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance & System Documents -->
        <div class="col-md-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> Performance & System
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.performance.reviews.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Performance Reviews</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Employee performance evaluation records</p>
                        </a>
                        
                        <a href="{{ route('admin.performance.goals.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Performance Goals</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Employee goal tracking and progress</p>
                        </a>
                        
                        <a href="{{ route('admin.biometric.export-enrollments') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Biometric Enrollments</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">Fingerprint enrollment status and details</p>
                        </a>
                        
                        <a href="{{ route('admin.biometric.export-audit-logs') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">Biometric Audit Logs</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">System audit and activity logs</p>
                        </a>

                        <a href="{{ route('admin.users.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">User Accounts</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">System user accounts and roles</p>
                        </a>

                        <a href="{{ route('admin.roles.export') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1">System Roles</h6>
                                <span class="badge bg-success">Excel</span>
                            </div>
                            <p class="mb-0 text-muted small">System roles and permissions matrix</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Export Information</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle"></i>
                        All documents are exported in Excel (.xlsx) format for easy viewing and data manipulation. 
                        Files are generated with professional formatting and current timestamps.
                    </p>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ \App\Models\Employee::count() }}</h4>
                                <small class="text-muted">Total Employees</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ \App\Models\Department::count() }}</h4>
                                <small class="text-muted">Departments</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ \App\Models\Payroll::count() }}</h4>
                                <small class="text-muted">Payroll Records</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ \App\Models\User::count() }}</h4>
                                <small class="text-muted">System Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: box-shadow 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }
</style>
@endpush
@endsection
