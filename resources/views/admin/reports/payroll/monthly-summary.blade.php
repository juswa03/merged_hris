@extends('admin.layouts.app')

@section('title', 'Monthly Payroll Summary')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.payroll.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Monthly Payroll Summary</h1>
                <p class="text-sm text-gray-600 mt-1">Comprehensive monthly payroll breakdown</p>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <form action="{{ route('admin.payroll.reports.monthly-summary') }}" method="GET" class="flex gap-3">
                <select name="period_id" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Select Payroll Period --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->formatted_period }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if($period)
            <a href="{{ route('admin.payroll.reports.export-monthly-summary-csv', ['period_id' => $period->id]) }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-download mr-2"></i> Export to CSV
            </a>
            @endif
        </div>
    </div>

    @if($period)
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Employees</div>
            <div class="text-3xl font-semibold text-gray-900 mt-2">{{ $summary['total_employees'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Gross Pay</div>
            <div class="text-2xl font-semibold text-gray-900 mt-2">₱{{ number_format($summary['total_gross_pay'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Deductions</div>
            <div class="text-2xl font-semibold text-red-600 mt-2">₱{{ number_format($summary['total_deductions'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Net Pay</div>
            <div class="text-2xl font-semibold text-green-600 mt-2">₱{{ number_format($summary['total_net_pay'], 2) }}</div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Additional Pay</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Overtime Pay</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['total_overtime'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Allowances</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['total_allowances'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Government Contributions</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">SSS</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['total_sss'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">PhilHealth</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['total_philhealth'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Pag-IBIG</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['total_pagibig'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Averages</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg. Gross Pay</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['avg_gross_pay'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg. Net Pay</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['avg_net_pay'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Withholding Tax</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($summary['total_tax'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Payroll Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Employee Payroll Details</h2>
            <span class="text-sm text-gray-600">Period: {{ $period->formatted_period }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic Salary</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Overtime</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Allowances</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Pay</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payrolls as $payroll)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $payroll->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $payroll->employee->position->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $payroll->employee->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">
                            ₱{{ number_format($payroll->basic_salary, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">
                            ₱{{ number_format($payroll->overtime_pay, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">
                            ₱{{ number_format($payroll->total_allowances, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                            ₱{{ number_format($payroll->gross_pay, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-red-600">
                            ₱{{ number_format($payroll->total_deductions, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-green-600">
                            ₱{{ number_format($payroll->net_pay, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            No payroll data found for this period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($payrolls->count() > 0)
                <tfoot class="bg-gray-100">
                    <tr class="font-bold">
                        <td colspan="2" class="px-4 py-3 text-sm text-gray-900">TOTALS</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">₱{{ number_format($payrolls->sum('basic_salary'), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">₱{{ number_format($payrolls->sum('overtime_pay'), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">₱{{ number_format($payrolls->sum('total_allowances'), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900">₱{{ number_format($payrolls->sum('gross_pay'), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-red-600">₱{{ number_format($payrolls->sum('total_deductions'), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-green-600">₱{{ number_format($payrolls->sum('net_pay'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @else
    <!-- No Period Selected -->
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-calendar-alt text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Period Selected</h3>
        <p class="text-gray-500">Please select a payroll period from the dropdown above to view the report.</p>
    </div>
    @endif
</div>
@endsection
