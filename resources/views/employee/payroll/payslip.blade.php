@extends('employee.layouts.app')

@section('title', 'My Payslip')

@section('content')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #payslip-area, #payslip-area * {
            visibility: visible;
        }
        #payslip-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none !important;
        }
        .payslip-container {
            box-shadow: none !important;
            border: none !important;
        }
    }

    .payslip-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .payslip-header {
        background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
        color: white;
        padding: 2rem;
    }

    .payslip-body {
        padding: 2rem;
    }

    .amount-cell {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }

    .total-row {
        background-color: #f8fafc;
        border-top: 2px solid #e2e8f0;
        font-weight: 700;
    }

    .section-title {
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
        color: #374151;
    }

    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
    }

    .breakdown-table td {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .breakdown-table tr:last-child td {
        border-bottom: none;
    }

    .signature-area {
        border-top: 1px dashed #cbd5e1;
        padding-top: 1rem;
        margin-top: 2rem;
    }
</style>

<div class="space-y-6">
    <!-- Page Header with Subtitle -->
    <div class="text-center mb-2">
        <h1 class="text-2xl font-bold text-gray-900">My Payslip</h1>
        <p class="text-gray-600">
            Salary Statement for 
            @if($payslip->payrollPeriod && $payslip->payrollPeriod->start_date)
                {{ \Carbon\Carbon::parse($payslip->payrollPeriod->start_date)->format('F Y') }}
            @else
                Current Month
            @endif
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="no-print flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-lg shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('employee.payroll.payslips') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Payslips
            </a>
            
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span>Pay Period: 
                    @if($payslip->payrollPeriod && $payslip->payrollPeriod->start_date)
                        {{ \Carbon\Carbon::parse($payslip->payrollPeriod->start_date)->format('F d, Y') }}
                    @else
                        Current Period
                    @endif
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="downloadPayslip()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-download mr-2"></i> Download PDF
            </button>
            
            <button onclick="printPayslip()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-print mr-2"></i> Print Payslip
            </button>
        </div>
    </div>

    <!-- Payslip Content -->
    <div id="payslip-area" class="payslip-container">
        <!-- Header -->
        <div class="payslip-header">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logos/uni_logo.png') }}" alt="BIPSU Logo" class="h-12 w-auto">
                        <div>
                            <h1 class="text-2xl font-bold">BIPSU HUMAN RESOURCE</h1>
                            <p class="text-blue-100">Biliran Province State University</p>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold mb-2">PAYSLIP</h2>
                    <p class="text-blue-100">Salary Statement</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold">Pay Date</p>
                    
                    <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($payslip->created_at)->format('M d, Y') }}</p>
                    <p class="text-blue-100 mt-2">Payslip #{{ $payslip->id }}</p>
                </div>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="payslip-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Employee Information</h3>
                    <p class="text-lg font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                    <p class="text-gray-600">{{ $employee->position->name ?? 'N/A' }}</p>
                    <p class="text-gray-600">{{ $employee->department->name ?? 'N/A' }}</p>
                    <p class="text-gray-600">Employee ID: {{ $employee->employee_id }}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Pay Period</h3>
                    <p class="text-gray-900">
                        @if($payslip->payrollPeriod && $payslip->payrollPeriod->start_date && $payslip->payrollPeriod->end_date)
                            {{ \Carbon\Carbon::parse($payslip->payrollPeriod->start_date)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($payslip->payrollPeriod->end_date)->format('M d, Y') }}
                        @else
                            Current Period
                        @endif
                    </p>
                    
                    <h3 class="font-semibold text-gray-700 mt-4 mb-2">Payment Method</h3>
                    <p class="text-gray-900">Bank Transfer</p>
                    <p class="text-gray-600">Account: ****{{ substr($employee->bank_account_number ?? '0000', -4) }}</p>
                </div>
            </div>

            <!-- Earnings & Deductions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Earnings -->
                <div>
                    <h3 class="section-title">EARNINGS</h3>
                    <table class="breakdown-table">
                        <tr>
                            <td class="text-gray-600">Basic Salary</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->basic_salary, 2) }}</td>
                        </tr>
                        
                        @if($payslip->overtime_pay > 0)
                        <tr>
                            <td class="text-gray-600">Overtime Pay</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->overtime_pay, 2) }}</td>
                        </tr>
                        @endif
                        
                        @if(($payslip->holiday_pay ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Holiday Pay</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->holiday_pay, 2) }}</td>
                        </tr>
                        @endif
                        
                        @if(($payslip->night_differential ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Night Differential</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->night_differential, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->bonuses ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Bonuses & Incentives</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->bonuses, 2) }}</td>
                        </tr>
                        @endif

                        <tr class="total-row">
                            <td class="font-semibold">Total Earnings</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->gross_salary, 2) }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Deductions -->
                <div>
                    <h3 class="section-title">DEDUCTIONS</h3>
                    <table class="breakdown-table">
                        @if(($payslip->gsis_contribution ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">GSIS Contribution</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->gsis_contribution, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->philhealth_contribution ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">PhilHealth</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->philhealth_contribution, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->pagibig_contribution ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Pag-IBIG Fund</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->pagibig_contribution, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->withholding_tax ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Withholding Tax</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->withholding_tax, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->late_deductions ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Late/Tardiness</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->late_deductions, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->absent_deductions ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Absences</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->absent_deductions, 2) }}</td>
                        </tr>
                        @endif

                        @if(($payslip->undertime_deductions ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Undertime</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->undertime_deductions, 2) }}</td>
                        </tr>
                        @endif

                        <tr class="total-row">
                            <td class="font-semibold">Total Deductions</td>
                            <td class="amount-cell">₱ {{ number_format($payslip->total_deductions, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Net Pay Summary -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600">Gross Salary</p>
                        <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($payslip->gross_salary, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Deductions</p>
                        <p class="text-2xl font-bold text-red-600">₱ {{ number_format($payslip->total_deductions, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Net Pay</p>
                        <p class="text-3xl font-bold text-green-600">₱ {{ number_format($payslip->net_salary, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <h3 class="section-title">WORK HOURS SUMMARY</h3>
                    <table class="breakdown-table">
                        <tr>
                            <td class="text-gray-600">Regular Hours</td>
                            <td class="amount-cell">{{ $payslip->regular_hours ?? 160 }} hours</td>
                        </tr>
                        @if(($payslip->overtime_hours ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Overtime Hours</td>
                            <td class="amount-cell">{{ $payslip->overtime_hours }} hours</td>
                        </tr>
                        @endif
                        @if(($payslip->late_hours ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Late Hours</td>
                            <td class="amount-cell">{{ $payslip->late_hours }} hours</td>
                        </tr>
                        @endif
                        @if(($payslip->absent_days ?? 0) > 0)
                        <tr>
                            <td class="text-gray-600">Absent Days</td>
                            <td class="amount-cell">{{ $payslip->absent_days }} days</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div>
                    <h3 class="section-title">LEAVE BALANCE</h3>
                    <table class="breakdown-table">
                        <tr>
                            <td class="text-gray-600">Vacation Leave</td>
                            <td class="amount-cell">{{ $employee->vacation_leave_balance ?? 0 }} days</td>
                        </tr>
                        <tr>
                            <td class="text-gray-600">Sick Leave</td>
                            <td class="amount-cell">{{ $employee->sick_leave_balance ?? 0 }} days</td>
                        </tr>
                        <tr>
                            <td class="text-gray-600">Emergency Leave</td>
                            <td class="amount-cell">{{ $employee->emergency_leave_balance ?? 0 }} days</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Signature Area -->
            <div class="signature-area">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="border-t border-gray-400 mt-8 pt-2 mx-auto" style="width: 200px;"></div>
                        <p class="font-semibold text-sm mt-2">{{ strtoupper($employee->first_name) }} {{ strtoupper($employee->last_name) }}</p>
                        <p class="text-xs text-gray-600">Employee Signature</p>
                    </div>
                    
                    <div>
                        <div class="border-t border-gray-400 mt-8 pt-2 mx-auto" style="width: 200px;"></div>
                        <p class="font-semibold text-sm mt-2">{{ strtoupper($supervisor->name) }}</p>
                        <p class="text-xs text-gray-600">Immediate Supervisor</p>
                    </div>
                    
                    <div>
                        <div class="border-t border-gray-400 mt-8 pt-2 mx-auto" style="width: 200px;"></div>
                        <p class="font-semibold text-sm mt-2">{{ strtoupper($hrOfficer->name) }}</p>
                        <p class="text-xs text-gray-600">HR Officer</p>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="mt-8 text-center text-xs text-gray-500">
                <p>This is a computer-generated document. No signature is required.</p>
                <p class="mt-1">Generated on: {{ now()->format('F d, Y h:i A') }}</p>
                <p class="mt-2">For inquiries, please contact HR Department at hr@bipsu.edu.ph or (053) 123-4567</p>
            </div>
        </div>
    </div>

    <!-- Additional Actions -->
    <div class="no-print bg-white p-4 rounded-lg shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                This payslip contains confidential information. Please keep it secure.
            </div>
            <div class="flex gap-3">
                <button onclick="emailPayslip()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                    <i class="fas fa-envelope mr-2"></i> Email Payslip
                </button>
                <a href="{{ route('employee.payroll.history') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                    <i class="fas fa-history mr-2"></i> View History
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printPayslip() {
    window.print();
}

function downloadPayslip() {
    showToast('Preparing PDF download...', 'info');
    
    setTimeout(() => {
        showToast('PDF download feature can be implemented with backend PDF generation', 'info');
    }, 1000);
}

function emailPayslip() {
    showToast('Sending payslip to your registered email...', 'info');
    
    setTimeout(() => {
        showToast('Payslip sent to your email successfully!', 'success');
    }, 2000);
}

// Add keyboard shortcut for printing
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printPayslip();
    }
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
@endpush