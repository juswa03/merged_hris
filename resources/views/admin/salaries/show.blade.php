@extends('admin.layouts.app')

@section('title', 'Employee Salary Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.salaries.index') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Salary Management
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Salary Details</h1>
            <p class="mt-2 text-sm text-gray-600">View and manage employee salary information</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.salaries.adjust-form', $employee->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center">
                <i class="fas fa-edit mr-2"></i> Adjust Salary
            </a>
            <a href="{{ route('admin.salaries.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Employee Information Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8 text-center">
                    <img class="h-24 w-24 rounded-full mx-auto border-4 border-white shadow-lg"
                         src="{{ $employee->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->first_name . ' ' . $employee->last_name) }}"
                         alt="">
                    <h3 class="mt-4 text-xl font-semibold text-white">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                    <p class="text-blue-100">EMP-{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Department</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $employee->department->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Position</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $employee->position->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Hire Date</p>
                        <p class="text-sm text-gray-900 font-medium">
                            {{ $employee->hire_date ? $employee->hire_date->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Salary Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Basic Salary Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-blue-600">Current Basic Salary</p>
                            <p class="text-3xl font-bold text-blue-900 mt-2">₱{{ number_format($breakdown['basic_salary'], 2) }}</p>
                            <p class="text-sm text-blue-700 mt-1">Per Month</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Salary Grade</p>
                                @if($salaryGradeInfo)
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            SG-{{ $salaryGradeInfo['grade'] }} Step {{ $salaryGradeInfo['step'] }}
                                        </span>
                                        @if($salaryGradeInfo['matches'])
                                            <span class="ml-2 text-green-600" title="Salary matches grade">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @else
                                            <span class="ml-2 text-yellow-600" title="Salary doesn't match grade (Expected: ₱{{ number_format($salaryGradeInfo['expected_salary'], 2) }})">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </span>
                                        @endif
                                    </div>
                                    @if(!$salaryGradeInfo['matches'])
                                        <p class="text-xs text-yellow-700 mt-1">
                                            Expected: ₱{{ number_format($salaryGradeInfo['expected_salary'], 2) }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400 mt-1">Not assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Breakdown Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Salary Breakdown</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Basic Salary -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Basic Salary</p>
                                <p class="text-xs text-gray-500">Monthly base salary</p>
                            </div>
                            <p class="text-lg font-semibold text-gray-900">₱{{ number_format($breakdown['basic_salary'], 2) }}</p>
                        </div>

                        <!-- Allowances -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Total Allowances</p>
                                <p class="text-xs text-gray-500">
                                    @if($employee->activeAllowances->count() > 0)
                                        {{ $employee->activeAllowances->count() }} active allowance(s)
                                    @else
                                        No active allowances
                                    @endif
                                </p>
                            </div>
                            <p class="text-lg font-semibold text-green-600">+₱{{ number_format($breakdown['allowances'], 2) }}</p>
                        </div>

                        <!-- Gross Salary -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 bg-green-50 -mx-6 px-6 py-3">
                            <div>
                                <p class="text-sm font-medium text-green-700">Gross Salary</p>
                                <p class="text-xs text-green-600">Basic + Allowances</p>
                            </div>
                            <p class="text-xl font-bold text-green-700">₱{{ number_format($breakdown['gross_salary'], 2) }}</p>
                        </div>

                        <!-- Deductions -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Total Deductions</p>
                                <p class="text-xs text-gray-500">
                                    @if($employee->activeDeductions->count() > 0)
                                        {{ $employee->activeDeductions->count() }} active deduction(s)
                                    @else
                                        No active deductions
                                    @endif
                                </p>
                            </div>
                            <p class="text-lg font-semibold text-red-600">-₱{{ number_format($breakdown['deductions'], 2) }}</p>
                        </div>

                        <!-- Net Salary -->
                        <div class="flex justify-between items-center bg-blue-600 -mx-6 px-6 py-4 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-blue-100">Net Salary</p>
                                <p class="text-xs text-blue-200">Take-home pay</p>
                            </div>
                            <p class="text-2xl font-bold text-white">₱{{ number_format($breakdown['net_salary'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Allowances -->
            @if($employee->activeAllowances->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Active Allowances</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($employee->activeAllowances as $allowance)
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $allowance->name }}</p>
                                @if($allowance->description)
                                    <p class="text-xs text-gray-500">{{ $allowance->description }}</p>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-green-700">+₱{{ number_format($allowance->amount, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Active Deductions -->
            @if($employee->activeDeductions->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Active Deductions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($employee->activeDeductions as $deduction)
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $deduction->name }}</p>
                                @if($deduction->description)
                                    <p class="text-xs text-gray-500">{{ $deduction->description }}</p>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-red-700">
                                -₱{{ number_format($deduction->pivot->custom_amount ?? $deduction->amount, 2) }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
