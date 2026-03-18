@extends('admin.layouts.app')

@section('title', 'Payroll Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Payroll Management"
        description="Manage employee payrolls, view history, and generate reports."
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.payroll.generation.index') }}" variant="success" icon="fas fa-plus-circle">Generate Payroll</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.payroll.export') }}" variant="primary" icon="fas fa-file-export">Export Data</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.payroll.audit-history') }}" variant="secondary" icon="fas fa-history">Audit Log</x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Total Payroll (This Period)" :value="'₱' . number_format($totalPayroll, 2)" icon="fas fa-money-bill-wave" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Employees Paid" :value="$employeesPaid . ' / ' . $totalEmployees" icon="fas fa-users" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Pending Payrolls" :value="$pendingPayrolls" icon="fas fa-clock" gradientFrom="orange-500" gradientTo="orange-600"/>
        <x-admin.gradient-stat-card title="Current Period" :value="$currentPeriod ? $currentPeriod->period_name : 'No Active Period'" icon="fas fa-calendar-alt" gradientFrom="purple-500" gradientTo="purple-600"/>
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.payroll.index') }}" class="flex flex-col lg:flex-row gap-3">
            <select name="period_id" class="block w-full lg:w-64 border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 sm:text-sm">
                <option value="">All Periods</option>
                @foreach($payrollPeriods as $period)
                    <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                        {{ $period->period_name }} ({{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }})
                    </option>
                @endforeach
            </select>
            <select name="employee_id" class="block w-full lg:w-52 border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 sm:text-sm">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                @endforeach
            </select>
            <select name="status" class="block w-full lg:w-40 border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 sm:text-sm">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>Generated</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter">Filter</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.payroll.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Payroll Table -->
    <x-admin.card title="Payroll Records" :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payrolls as $payroll)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $payroll->employee->photo_url ?? asset('images/icons/user-icon.webp') }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $payroll->employee->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $payroll->employee->employee_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $payroll->payrollPeriod->period_name }}</div>
                        <div class="text-xs text-gray-500">{{ $payroll->payrollPeriod->start_date->format('M d') }} - {{ $payroll->payrollPeriod->end_date->format('M d') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">₱{{ number_format($payroll->net_pay, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($payroll->status === 'paid')
                            <x-admin.badge variant="success">Paid</x-admin.badge>
                        @elseif($payroll->status === 'generated')
                            <x-admin.badge variant="info">Generated</x-admin.badge>
                        @else
                            <x-admin.badge variant="default">Draft</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button :href="route('admin.payroll.detailed-breakdown', $payroll->id)" variant="info" icon="fas fa-eye" iconOnly size="sm" title="View Details"/>
                            <x-admin.action-button :href="route('admin.payroll.download-payslip', $payroll->id)" variant="success" icon="fas fa-download" iconOnly size="sm" title="Download Payslip"/>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <x-admin.empty-state
                            icon="fas fa-file-invoice-dollar"
                            title="No Payroll Records Found"
                            message="No payroll records match your current filters."
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($payrolls->hasPages())
        <x-slot name="footer">{{ $payrolls->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>
@endsection
