@extends('admin.layouts.app')

@section('title', 'Department Payroll Breakdown')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.payroll.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Department Payroll Breakdown</h1>
                <p class="text-sm text-gray-600 mt-1">Payroll costs by department</p>
            </div>
        </div>

        <!-- Period Selector -->
        <form action="{{ route('admin.payroll.reports.department-breakdown') }}" method="GET">
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

    @if($period && $departmentData->count() > 0)
    <!-- Department Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($departmentData as $dept)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $dept['department'] }}</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-sm text-gray-600">Employees</span>
                    <span class="text-lg font-semibold text-gray-900">{{ $dept['employee_count'] }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-xs text-gray-600">Total Gross</span>
                    <span class="text-sm font-medium text-gray-900">₱{{ number_format($dept['total_gross'], 2) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-xs text-gray-600">Total Deductions</span>
                    <span class="text-sm font-medium text-red-600">₱{{ number_format($dept['total_deductions'], 2) }}</span>
                </div>

                <div class="flex justify-between pt-2 border-t border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Total Net Pay</span>
                    <span class="text-lg font-bold text-green-600">₱{{ number_format($dept['total_net'], 2) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-xs text-gray-600">Average Net Pay</span>
                    <span class="text-sm text-gray-900">₱{{ number_format($dept['avg_net'], 2) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-gray-200">
                    <div>
                        <span class="text-xs text-gray-600 block">Overtime</span>
                        <span class="text-sm font-medium text-gray-900">₱{{ number_format($dept['total_overtime'], 2) }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-600 block">Allowances</span>
                        <span class="text-sm font-medium text-gray-900">₱{{ number_format($dept['total_allowances'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary Table -->
    <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Department Summary - {{ $period->formatted_period }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Employees</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Gross</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Net</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg. Net</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departmentData as $dept)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $dept['department'] }}</td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ $dept['employee_count'] }}</td>
                        <td class="px-6 py-4 text-right text-gray-900">₱{{ number_format($dept['total_gross'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-red-600">₱{{ number_format($dept['total_deductions'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">₱{{ number_format($dept['total_net'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-gray-900">₱{{ number_format($dept['avg_net'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr class="font-bold">
                        <td class="px-6 py-3">GRAND TOTAL</td>
                        <td class="px-6 py-3 text-center">{{ $departmentData->sum('employee_count') }}</td>
                        <td class="px-6 py-3 text-right">₱{{ number_format($departmentData->sum('total_gross'), 2) }}</td>
                        <td class="px-6 py-3 text-right text-red-600">₱{{ number_format($departmentData->sum('total_deductions'), 2) }}</td>
                        <td class="px-6 py-3 text-right text-green-600">₱{{ number_format($departmentData->sum('total_net'), 2) }}</td>
                        <td class="px-6 py-3 text-right">₱{{ number_format($departmentData->avg('avg_net'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-building text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Data Available</h3>
        <p class="text-gray-500">Please select a payroll period to view department breakdown.</p>
    </div>
    @endif
</div>
@endsection
