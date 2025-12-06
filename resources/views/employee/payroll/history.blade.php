@extends('employee.layouts.app')


@section('title', 'Payroll History')
@section('subtitle', 'Complete Salary Records & Payment History')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Payslips</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['total_payslips'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Year {{ $year }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Earnings</p>
                    <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($summary['total_earnings']) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Gross Salary</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Deductions</p>
                    <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($summary['total_deductions']) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Taxes & Contributions</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-hand-holding-usd text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Net Income</p>
                    <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($summary['total_net_pay']) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Take Home Pay</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-wallet text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Year Filter -->
                <div class="flex items-center gap-2">
                    <label for="year" class="text-sm font-medium text-gray-700">Year:</label>
                    <select id="year" name="year" onchange="filterHistory()" 
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($availableYears as $availableYear)
                            <option value="{{ $availableYear }}" {{ $year == $availableYear ? 'selected' : '' }}>
                                {{ $availableYear }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div class="flex items-center gap-2">
                    <label for="month" class="text-sm font-medium text-gray-700">Month:</label>
                    <select id="month" name="month" onchange="filterHistory()" 
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $monthNum)
                            <option value="{{ $monthNum }}" {{ $month == $monthNum ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $monthNum)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div class="flex items-center gap-2">
                    <label for="search" class="text-sm font-medium text-gray-700">Search:</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" 
                           placeholder="Pay period or status..." 
                           class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
                    <button onclick="filterHistory()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="exportHistory()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                    <i class="fas fa-download mr-2"></i> Export Data
                </button>
                
                <button onclick="resetFilters()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                    <i class="fas fa-refresh mr-2"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Additional Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t">
            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                <p class="text-lg font-bold text-yellow-700">₱ {{ number_format($summary['yearly_bonus']) }}</p>
                <p class="text-sm text-yellow-600">Yearly Bonuses</p>
            </div>
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <p class="text-lg font-bold text-orange-700">₱ {{ number_format($summary['yearly_overtime']) }}</p>
                <p class="text-sm text-orange-600">Overtime Pay</p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <p class="text-lg font-bold text-blue-700">₱ {{ number_format($summary['average_net_pay']) }}</p>
                <p class="text-sm text-blue-600">Average Monthly Net</p>
            </div>
        </div>
    </div>

    <!-- Payroll History Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Payroll History</h3>
            <p class="text-sm text-gray-600 mt-1">Your complete salary payment records</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pay Period
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Payment Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Basic Salary
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Allowances
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deductions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Net Pay
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payrollHistory as $payroll)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $payroll->pay_period ?? $payroll->created_at->format('F Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($payroll->pay_period_start && $payroll->pay_period_end)
                                    {{ $payroll->pay_period_start->format('M d') }} - {{ $payroll->pay_period_end->format('M d, Y') }}
                                @else
                                    Full Month
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payroll->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱ {{ number_format($payroll->basic_salary, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                ₱ {{ number_format(($payroll->overtime_pay ?? 0) + ($payroll->bonuses ?? 0) + ($payroll->allowances ?? 0), 2) }}
                                @if(($payroll->overtime_pay ?? 0) > 0)
                                    <span class="ml-1 text-xs text-green-600" title="Includes overtime">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                            -₱ {{ number_format($payroll->total_deductions, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">
                                ₱ {{ number_format($payroll->net_pay, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($payroll->payrollPeriod->status === 'finalized')
                                    bg-green-100 text-green-800
                                @elseif($payroll->payrollPeriod->status === 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif($payroll->payrollPeriod->status === 'processed')
                                    bg-blue-100 text-blue-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                @if($payroll->payrollPeriod->status === 'finalized')
                                    <i class="fas fa-check-circle mr-1"></i>
                                @elseif($payroll->payrollPeriod->status === 'pending')
                                    <i class="fas fa-clock mr-1"></i>
                                @elseif($payroll->payrollPeriod->status === 'processed')
                                    <i class="fas fa-cogs mr-1"></i>
                                @endif

                                {{ ucfirst($payroll->payrollPeriod->status ?? 'Unknown') }}
                            </span>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('employee.payroll.payslip', $payroll->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors"
                                   title="View Payslip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="downloadPayslip({{ $payroll->id }})" 
                                        class="text-green-600 hover:text-green-900 transition-colors"
                                        title="Download PDF">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button onclick="emailPayslip({{ $payroll->id }})" 
                                        class="text-purple-600 hover:text-purple-900 transition-colors"
                                        title="Email Payslip">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium mb-2">No payroll records found</p>
                                <p class="text-sm">No payroll history available for the selected filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payrollHistory->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $payrollHistory->links() }}
        </div>
        @endif
    </div>

    <!-- Yearly Comparison Chart (Optional) -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Yearly Earnings Overview</h3>
        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function filterHistory() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const search = document.getElementById('search').value;
    
    const params = new URLSearchParams();
    if (year) params.append('year', year);
    if (month) params.append('month', month);
    if (search) params.append('search', search);
    
    window.location.href = '{{ route("employee.payroll.history") }}?' + params.toString();
}

function resetFilters() {
    window.location.href = '{{ route("employee.payroll.history") }}';
}

function exportHistory() {
    const year = document.getElementById('year').value;
    
    showToast('Preparing export data...', 'info');
    
    // In real implementation, this would call your export endpoint
    // window.open('{{ route("employee.payroll.export-history") }}?year=' + year, '_blank');
    
    setTimeout(() => {
        showToast('Export feature can be implemented with backend export functionality', 'info');
    }, 1000);
}

function downloadPayslip(payslipId) {
    showToast('Downloading payslip...', 'info');
    // window.open('/employee/payroll/payslip/' + payslipId + '/download', '_blank');
}

function emailPayslip(payslipId) {
    showToast('Sending payslip to your email...', 'info');
    // Implement email functionality
}

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

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Net Salary',
                data: @json($yearly_earnings_data['monthly_total_net_pay'] ?? []),
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush