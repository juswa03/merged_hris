@extends('layouts.app')

@section('title', 'Year-to-Date Earnings Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('payroll.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Year-to-Date Earnings Report</h1>
                <p class="text-sm text-gray-600 mt-1">Annual employee earnings summary</p>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <form action="{{ route('payroll.reports.ytd-earnings') }}" method="GET">
                <select name="year" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('payroll.reports.export-ytd-earnings-csv', ['year' => $year]) }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-download mr-2"></i> Export to CSV
            </a>
        </div>
    </div>

    <!-- Grand Totals -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Gross Pay</div>
            <div class="text-2xl font-semibold text-gray-900 mt-2">₱{{ number_format($grandTotals['total_gross'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Deductions</div>
            <div class="text-2xl font-semibold text-red-600 mt-2">₱{{ number_format($grandTotals['total_deductions'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Net Pay</div>
            <div class="text-2xl font-semibold text-green-600 mt-2">₱{{ number_format($grandTotals['total_net'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Total Tax</div>
            <div class="text-2xl font-semibold text-purple-600 mt-2">₱{{ number_format($grandTotals['total_tax'], 2) }}</div>
        </div>
    </div>

    <!-- YTD Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Employee YTD Earnings - {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Pay</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Pay</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Periods</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($ytdData as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $data['employee']->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $data['employee']->position->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $data['employee']->department->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">₱{{ number_format($data['total_gross'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-red-600">₱{{ number_format($data['total_deductions'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">₱{{ number_format($data['total_net'], 2) }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-900">{{ $data['payroll_count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
