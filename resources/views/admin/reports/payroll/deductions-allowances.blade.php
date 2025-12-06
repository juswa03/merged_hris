@extends('layouts.app')

@section('title', 'Deductions & Allowances Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('payroll.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Deductions & Allowances Report</h1>
                <p class="text-sm text-gray-600 mt-1">Detailed breakdown of deductions and allowances</p>
            </div>
        </div>

        <form action="{{ route('payroll.reports.deductions-allowances') }}" method="GET">
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
    </div>

    @if($period && $data->count() > 0)
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Allowances</div>
            <div class="text-2xl font-semibold text-green-600 mt-2">₱{{ number_format($totals['total_allowances'], 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">Added to payroll</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Deductions</div>
            <div class="text-2xl font-semibold text-red-600 mt-2">₱{{ number_format($totals['total_deductions'], 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">Deducted from payroll</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Net Effect</div>
            <div class="text-2xl font-semibold {{ $totals['net_effect'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                ₱{{ number_format($totals['net_effect'], 2) }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Allowances - Deductions</div>
        </div>
    </div>

    <!-- Deductions Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Deduction Types</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Late Deductions</span>
                    <span class="text-sm font-medium text-red-600">₱{{ number_format($totals['total_late'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Absent Deductions</span>
                    <span class="text-sm font-medium text-red-600">₱{{ number_format($totals['total_absent'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Undertime Deductions</span>
                    <span class="text-sm font-medium text-red-600">₱{{ number_format($totals['total_undertime'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Other Deductions</span>
                    <span class="text-sm font-medium text-red-600">₱{{ number_format($totals['total_other_deductions'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-sm font-bold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-red-600">₱{{ number_format($totals['total_deductions'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Impact Analysis</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Employees with Allowances</span>
                    <span class="text-sm font-medium text-gray-900">{{ $data->where('total_allowances', '>', 0)->count() }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Employees with Deductions</span>
                    <span class="text-sm font-medium text-gray-900">{{ $data->where('total_deductions', '>', 0)->count() }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Avg. Allowance/Employee</span>
                    <span class="text-sm font-medium text-green-600">₱{{ number_format($data->avg('total_allowances'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-sm text-gray-600">Avg. Deduction/Employee</span>
                    <span class="text-sm font-medium text-red-600">₱{{ number_format($data->avg('total_deductions'), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Employee Details - {{ $period->formatted_period }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Allowances</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Late</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Absent</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Undertime</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Other</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Deductions</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Effect</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data as $record)
                    @php
                        $netEffect = $record->total_allowances - $record->total_deductions;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $record->employee->full_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-green-600">₱{{ number_format($record->total_allowances, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($record->late_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($record->absent_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($record->undertime_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($record->other_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-red-600">₱{{ number_format($record->total_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-semibold {{ $netEffect >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ₱{{ number_format($netEffect, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-exchange-alt text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Data Available</h3>
        <p class="text-gray-500">Please select a payroll period to view deductions and allowances.</p>
    </div>
    @endif
</div>
@endsection
