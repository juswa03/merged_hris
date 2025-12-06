@extends('employee.layouts.app')

@section('title', 'My Payslips')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Payslips</h1>
        <p class="text-gray-600">View your salary history and download payslips</p>
    </div>

    <!-- Payslips List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Payslip History</h2>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pay Period
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Basic Salary
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Deductions
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Net Pay
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pay Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payslips as $payslip)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            @if($payslip->payrollPeriod && $payslip->payrollPeriod->start_date)
                                {{ \Carbon\Carbon::parse($payslip->payrollPeriod->start_date)->format('F Y') }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            @if($payslip->payrollPeriod && $payslip->payrollPeriod->start_date && $payslip->payrollPeriod->end_date)
                                {{ \Carbon\Carbon::parse($payslip->payrollPeriod->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($payslip->payrollPeriod->end_date)->format('M d, Y') }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">₱ {{ number_format($payslip->basic_salary, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-red-600">₱ {{ number_format($payslip->total_deductions, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-green-600">₱ {{ number_format($payslip->net_salary, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($payslip->created_at)->format('M d, Y') }}

                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('employee.payroll.payslip', ['id' => $payslip->id]) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                            <i class="fas fa-eye mr-2"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No payslips found for your account.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($payslips->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payslips->links() }}
        </div>
        @endif
    </div>

    <!-- Information Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
            <div>
                <h3 class="text-sm font-semibold text-blue-800">Payslip Information</h3>
                <p class="text-sm text-blue-600 mt-1">
                    Payslips are typically available 2-3 days after payday. 
                    Contact HR if you have any questions about your payslip.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection