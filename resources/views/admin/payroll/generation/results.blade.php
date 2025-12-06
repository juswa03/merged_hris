@extends('layouts.app')

@section('title', 'Payroll Generation Results')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Payroll Generation Results"
        description="Review the results of your payroll generation"
    />

    @if(session('success'))
        <x-admin.alert type="success" class="mb-6">
            {{ session('success') }}
        </x-admin.alert>
    @endif

    @if(session('warning'))
        <x-admin.alert type="warning" class="mb-6">
            {{ session('warning') }}
        </x-admin.alert>
    @endif

    @if($result)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-admin.card>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Generated</p>
                    <p class="text-3xl font-bold text-green-600">{{ $result['generated_count'] ?? 0 }}</p>
                </div>
            </x-admin.card>

            <x-admin.card>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Updated</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $result['updated_count'] ?? 0 }}</p>
                </div>
            </x-admin.card>

            <x-admin.card>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Failed</p>
                    <p class="text-3xl font-bold text-red-600">{{ $result['failed_count'] ?? 0 }}</p>
                </div>
            </x-admin.card>

            <x-admin.card>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Total Payroll</p>
                    <p class="text-3xl font-bold text-purple-600">₱{{ number_format($result['total_payroll'] ?? 0, 2) }}</p>
                </div>
            </x-admin.card>
        </div>

        @if(isset($result['details']) && count($result['details']) > 0)
            <x-admin.card title="Detailed Results">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="text-left py-2 px-3">Employee Name</th>
                                <th class="text-right py-2 px-3">Salary Grade</th>
                                <th class="text-right py-2 px-3">Basic Salary</th>
                                <th class="text-right py-2 px-3">Gross Pay</th>
                                <th class="text-right py-2 px-3">Net Pay</th>
                                <th class="text-center py-2 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['details'] as $detail)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 font-medium">{{ $detail['employee_name'] ?? 'Unknown' }}</td>
                                    <td class="text-right py-2 px-3">{{ $detail['salary_grade'] ?? 'N/A' }}</td>
                                    <td class="text-right py-2 px-3">₱{{ number_format($detail['basic_salary'] ?? 0, 2) }}</td>
                                    <td class="text-right py-2 px-3">₱{{ number_format($detail['gross_pay'] ?? 0, 2) }}</td>
                                    <td class="text-right py-2 px-3 font-bold">₱{{ number_format($detail['net_pay'] ?? 0, 2) }}</td>
                                    <td class="text-center py-2 px-3">
                                        @if(($detail['status'] ?? 'success') === 'success')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                                <i class="fas fa-check mr-1"></i> Success
                                            </span>
                                        @else
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                                <i class="fas fa-times mr-1"></i> Failed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        @endif
    @else
        <x-admin.alert type="info">
            No payroll generation results available. Please generate payroll first.
        </x-admin.alert>
    @endif

    <div class="mt-6 flex gap-3">
        <a href="{{ route('payroll.generation.index') }}" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Generation
        </a>
        <a href="{{ route('payroll.index') }}" class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-list mr-2"></i> View All Payrolls
        </a>
    </div>
</div>
@endsection
