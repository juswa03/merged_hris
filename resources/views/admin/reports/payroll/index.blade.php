@extends('admin.layouts.app')

@section('title', 'Payroll Reports')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Payroll Reports & Analytics"
        description="Comprehensive payroll reporting and data analysis"
    />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <x-admin.gradient-stat-card 
            title="Current Month Payroll" 
            :value="'₱' . number_format($stats['current_month_payroll'], 2)" 
            icon="fas fa-money-bill-wave" 
            gradientFrom="blue-500" 
            gradientTo="blue-600"
            :description="$stats['current_month_employees'] . ' employees'"
        />

        <x-admin.gradient-stat-card 
            title="Year-to-Date Payroll" 
            :value="'₱' . number_format($stats['total_ytd_payroll'], 2)" 
            icon="fas fa-chart-line" 
            gradientFrom="green-500" 
            gradientTo="green-600"
            :description="now()->format('Y')"
        />

        <x-admin.gradient-stat-card 
            title="Avg. Employee Salary" 
            :value="'₱' . number_format($stats['avg_employee_salary'], 2)" 
            icon="fas fa-calculator" 
            gradientFrom="purple-500" 
            gradientTo="purple-600"
            description="YTD Average"
        />

        <x-admin.gradient-stat-card 
            title="Available Periods" 
            :value="$periods->count()" 
            icon="fas fa-calendar-alt" 
            gradientFrom="yellow-500" 
            gradientTo="yellow-600"
            description="Last 12 periods"
        />
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Monthly Payroll Summary -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-file-invoice-dollar text-blue-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Monthly Payroll Summary</h3>
                        <p class="text-sm text-gray-600">Detailed monthly breakdown</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Comprehensive monthly payroll report with gross pay, deductions, and net pay details for all employees.
                </p>
                <a href="{{ route('admin.payroll.reports.monthly-summary') }}"
                   class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i> View Report
                </a>
            </div>
        </div>

        <!-- Department Breakdown -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-building text-green-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Department Breakdown</h3>
                        <p class="text-sm text-gray-600">Payroll by department</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Analyze payroll costs across departments with employee counts, averages, and totals per department.
                </p>
                <a href="{{ route('admin.payroll.reports.department-breakdown') }}"
                   class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i> View Report
                </a>
            </div>
        </div>

        <!-- Government Contributions -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-landmark text-purple-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Government Contributions</h3>
                        <p class="text-sm text-gray-600">SSS, PhilHealth, Pag-IBIG</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Track mandatory government contributions including SSS, PhilHealth, Pag-IBIG, and withholding tax.
                </p>
                <a href="{{ route('admin.payroll.reports.government-contributions') }}"
                   class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i> View Report
                </a>
            </div>
        </div>

        <!-- YTD Earnings -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-chart-bar text-yellow-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Year-to-Date Earnings</h3>
                        <p class="text-sm text-gray-600">Annual employee earnings</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Year-to-date summary of all employee earnings, deductions, and net pay for tax and compliance purposes.
                </p>
                <a href="{{ route('admin.payroll.reports.ytd-earnings') }}"
                   class="block w-full text-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i> View Report
                </a>
            </div>
        </div>

        <!-- Deductions & Allowances -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-exchange-alt text-red-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Deductions & Allowances</h3>
                        <p class="text-sm text-gray-600">Detailed breakdown</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Comprehensive report of all deductions (late, absent, undertime) and allowances affecting payroll.
                </p>
                <a href="{{ route('admin.payroll.reports.deductions-allowances') }}"
                   class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i> View Report
                </a>
            </div>
        </div>

        <!-- Payroll Analytics (Future) -->
        <div class="bg-white rounded-lg shadow-md border-2 border-dashed border-gray-300">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i class="fas fa-chart-pie text-gray-400 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-500">Payroll Analytics</h3>
                        <p class="text-sm text-gray-400">Coming soon</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">
                    Advanced analytics with charts, trends, and predictive insights for payroll management.
                </p>
                <button disabled
                   class="block w-full text-center px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i> Coming Soon
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Payroll Periods -->
    @if($periods->count() > 0)
    <div class="mt-8 bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recent Payroll Periods</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Period
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date Range
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pay Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($periods as $period)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $period->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $period->formatted_period }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ $period->pay_date ? $period->pay_date->format('M d, Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($period->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $period->status === 'completed' ? 'bg-green-100 text-green-800' :
                                   ($period->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($period->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
