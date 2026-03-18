@extends('admin.layouts.app')

@section('title', 'Government Contributions Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.payroll.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Government Contributions Report</h1>
                <p class="text-sm text-gray-600 mt-1">SSS, PhilHealth, Pag-IBIG & Withholding Tax</p>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <form action="{{ route('admin.payroll.reports.government-contributions') }}" method="GET">
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
            <a href="{{ route('admin.payroll.reports.export-government-contributions-csv', ['period_id' => $period->id]) }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-download mr-2"></i> Export to CSV
            </a>
            @endif
        </div>
    </div>

    @if($period && $contributions->count() > 0)
    <!-- Totals Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total GSIS</div>
            <div class="text-2xl font-semibold text-blue-600 mt-2">₱{{ number_format($totals['total_gsis'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total PhilHealth</div>
            <div class="text-2xl font-semibold text-green-600 mt-2">₱{{ number_format($totals['total_philhealth'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Pag-IBIG</div>
            <div class="text-2xl font-semibold text-yellow-600 mt-2">₱{{ number_format($totals['total_pagibig'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Withholding Tax</div>
            <div class="text-2xl font-semibold text-purple-600 mt-2">₱{{ number_format($totals['total_tax'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Grand Total</div>
            <div class="text-2xl font-semibold text-gray-900 mt-2">₱{{ number_format($totals['grand_total'], 2) }}</div>
        </div>
    </div>

    <!-- Contributions Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Employee Contributions - {{ $period->formatted_period }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">GSIS</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">PhilHealth</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pag-IBIG</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">W/Tax</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contributions as $contrib)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $contrib->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $contrib->employee->employee_number ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($contrib->gsis_contribution, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($contrib->philhealth_contribution, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($contrib->pagibig_contribution, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($contrib->withholding_tax, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                            ₱{{ number_format($contrib->gsis_contribution + $contrib->philhealth_contribution + $contrib->pagibig_contribution + $contrib->withholding_tax, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr class="font-bold">
                        <td class="px-4 py-3">TOTALS</td>
                        <td class="px-4 py-3 text-right text-blue-600">₱{{ number_format($totals['total_sss'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">₱{{ number_format($totals['total_philhealth'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-yellow-600">₱{{ number_format($totals['total_pagibig'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-purple-600">₱{{ number_format($totals['total_tax'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900">₱{{ number_format($totals['grand_total'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-landmark text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Data Available</h3>
        <p class="text-gray-500">Please select a payroll period to view government contributions.</p>
    </div>
    @endif
</div>
@endsection
