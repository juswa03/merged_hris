@extends('layouts.app')

@section('title', 'Payslips')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Payslips"
        description="View and manage employee payslips for processed payments"
    >
        <x-slot name="actions">
            <x-admin.action-button
                variant="primary"
                icon="fas fa-download"
                onclick="bulkDownloadPayslips()"
            >
                Bulk Download
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-600"></i>
                Filter Payslips
            </h3>
            <form method="GET" action="{{ route('payroll.payslips') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Year Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <select name="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Period Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Specific Period
                    </label>
                    <select name="period_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Periods</option>
                        @foreach($payrollPeriods as $period)
                            <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                {{ $period->formatted_period }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i> Department
                    </label>
                    <select name="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i> Employee
                    </label>
                    <select name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->last_name }}, {{ $employee->first_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="md:col-span-3 lg:col-span-5 flex justify-end space-x-3 mt-2">
                    <a href="{{ route('payroll.payslips') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-all duration-200 border border-gray-300 flex items-center">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center">
                        <i class="fas fa-search mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payslips Grid -->
    @if($payrolls->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($payrolls as $payroll)
                <div class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <!-- Header with Gradient -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between text-white">
                            <div class="flex-1">
                                <h4 class="text-lg font-bold">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</h4>
                                <p class="text-sm text-white/90 mt-1">
                                    <i class="fas fa-id-badge mr-1"></i> {{ $payroll->employee->employee_id ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="bg-white/20 px-3 py-1 rounded-lg">
                                <i class="fas fa-file-invoice-dollar text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Payslip Details -->
                    <div class="p-6">
                        <!-- Period Info -->
                        <div class="mb-4">
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                                <span class="font-semibold">{{ $payroll->payrollPeriod->period_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="far fa-calendar mr-2"></i>
                                @if($payroll->payrollPeriod)
                                    {{ $payroll->payrollPeriod->start_date->format('M d, Y') }} - {{ $payroll->payrollPeriod->end_date->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>

                        <!-- Department & Position -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Department</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payroll->employee->department->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Position</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payroll->employee->position->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Net Pay -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 mb-4">
                            <p class="text-xs text-green-700 font-medium mb-1">Net Pay</p>
                            <p class="text-2xl font-bold text-green-800">�{{ number_format($payroll->net_pay, 2) }}</p>
                        </div>

                        <!-- Status Badge -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Paid
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button
                                onclick="viewPayslip({{ $payroll->id }})"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                            >
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                            <button
                                onclick="downloadPayslip({{ $payroll->id }})"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                            >
                                <i class="fas fa-download mr-1"></i> PDF
                            </button>
                            <button
                                onclick="emailPayslip({{ $payroll->id }})"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                            >
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
            {{ $payrolls->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-file-invoice text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Payslips Found</h3>
            <p class="text-gray-600 mb-4">
                There are no paid payrolls matching your current filters.
            </p>
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Go to Payroll Management
            </a>
        </div>
    @endif
</div>

<!-- View Payslip Modal -->
<div id="viewPayslipModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Payslip Details
            </h3>
            <button onclick="closeViewPayslipModal()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div id="payslipContent" class="p-6">
            <!-- Payslip content will be loaded here -->
            <div class="flex items-center justify-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewPayslip(payrollId) {
    document.getElementById('viewPayslipModal').classList.remove('hidden');

    fetch(`/payrolls/${payrollId}/payslip`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            const payslip = data.payslip_data;
            const company = data.company_info;
            const breakdown = data.breakdown;

            const content = `
                <div class="bg-white">
                    <!-- Company Header -->
                    <div class="text-center mb-6 pb-6 border-b-2 border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">${company.name}</h2>
                        <p class="text-sm text-gray-600">${company.address}</p>
                        <p class="text-sm text-gray-600">${company.contact}</p>
                        <h3 class="text-xl font-semibold text-blue-700 mt-4">PAYSLIP</h3>
                    </div>

                    <!-- Employee Information -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Employee Name</p>
                            <p class="text-base font-semibold text-gray-900">${payslip.employee.first_name} ${payslip.employee.last_name}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Employee ID</p>
                            <p class="text-base font-semibold text-gray-900">${payslip.employee.employee_id || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Department</p>
                            <p class="text-base font-semibold text-gray-900">${payslip.employee.department?.name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Position</p>
                            <p class="text-base font-semibold text-gray-900">${payslip.employee.position?.name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Pay Period</p>
                            <p class="text-base font-semibold text-gray-900">${payslip.payroll_period?.period_name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Pay Date</p>
                            <p class="text-base font-semibold text-gray-900">${new Date(payslip.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>

                    <!-- Earnings & Deductions -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Earnings -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <h4 class="text-sm font-bold text-green-800 mb-3 flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i> EARNINGS
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Basic Salary</span>
                                    <span class="font-semibold text-gray-900">�${Number(payslip.basic_salary).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Overtime Pay</span>
                                    <span class="font-semibold text-gray-900">�${Number(payslip.overtime_pay || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Allowances</span>
                                    <span class="font-semibold text-gray-900">�${Number(payslip.total_allowances || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm pt-2 border-t border-green-200">
                                    <span class="font-bold text-green-800">Gross Pay</span>
                                    <span class="font-bold text-green-800">�${Number(breakdown.gross_pay).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div class="bg-red-50 rounded-lg p-4">
                            <h4 class="text-sm font-bold text-red-800 mb-3 flex items-center">
                                <i class="fas fa-minus-circle mr-2"></i> DEDUCTIONS
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">GSIS</span>
                                    <span class="font-semibold text-gray-900">${Number(breakdown.gsis).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">PhilHealth</span>
                                    <span class="font-semibold text-gray-900">�${Number(breakdown.philhealth).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Pag-IBIG</span>
                                    <span class="font-semibold text-gray-900">�${Number(breakdown.pagibig).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Withholding Tax</span>
                                    <span class="font-semibold text-gray-900">�${Number(breakdown.tax).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Other Deductions</span>
                                    <span class="font-semibold text-gray-900">�${Number(breakdown.other_deductions).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between text-sm pt-2 border-t border-red-200">
                                    <span class="font-bold text-red-800">Total Deductions</span>
                                    <span class="font-bold text-red-800">�${Number(payslip.total_deductions).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Net Pay -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-center">
                        <p class="text-white/90 text-sm font-medium mb-2">NET PAY</p>
                        <p class="text-4xl font-bold text-white">�${Number(payslip.net_pay).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
                        <p>This is a computer-generated payslip. No signature required.</p>
                        <p class="mt-1">Generated on ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
            `;

            document.getElementById('payslipContent').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('payslipContent').innerHTML = `
                <div class="text-center py-12 text-red-600">
                    <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                    <p>Error loading payslip details</p>
                </div>
            `;
        });
}

function closeViewPayslipModal() {
    document.getElementById('viewPayslipModal').classList.add('hidden');
}

function downloadPayslip(payrollId) {
    // Direct navigation to trigger download
    window.location.href = `/payrolls/${payrollId}/download-payslip`;
}

function emailPayslip(payrollId) {
    if (!confirm('Send payslip via email to employee?')) return;

    fetch(`/payrolls/${payrollId}/email-payslip`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Error sending email');
    });
}

function bulkDownloadPayslips() {
    const urlParams = new URLSearchParams(window.location.search);
    const params = new URLSearchParams({
        period_id: urlParams.get('period_id') || '',
        department_id: urlParams.get('department_id') || ''
    });

    // Direct navigation to trigger download
    window.location.href = '/payrolls/bulk-download?' + params.toString();
}
</script>
@endpush
@endsection
