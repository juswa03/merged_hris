@extends('admin.layouts.app')

@section('title', 'Salary Reports')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Salary Reports"
        description="Comprehensive salary analysis and distribution reports"
    >
        <x-slot name="actions">
            <x-admin.action-button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('salaries.index') }}'"
            >
                Back to Salaries
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Salary Range Distribution -->
    <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Salary Range Distribution
            </h3>
            <p class="text-sm text-gray-600 mt-1">Employee count by salary bracket</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $totalEmployees = array_sum(array_values($salaryRanges));
                    $maxCount = max(array_values($salaryRanges));
                @endphp

                @foreach($salaryRanges as $range => $count)
                    @php
                        $percentage = $totalEmployees > 0 ? ($count / $totalEmployees) * 100 : 0;
                        $barWidth = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">₱{{ $range }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                                <span class="text-xs text-gray-500">({{ number_format($percentage, 1) }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $barWidth }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($totalEmployees > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">Total Employees</p>
                            <p class="text-xl font-bold text-gray-900">{{ $totalEmployees }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">Below ₱25K</p>
                            <p class="text-xl font-bold text-orange-600">{{ $salaryRanges['0-15000'] + $salaryRanges['15001-25000'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">₱25K - ₱60K</p>
                            <p class="text-xl font-bold text-blue-600">{{ $salaryRanges['25001-40000'] + $salaryRanges['40001-60000'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">Above ₱60K</p>
                            <p class="text-xl font-bold text-green-600">{{ $salaryRanges['60001-100000'] + $salaryRanges['100001+'] }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alert Cards for Issues -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Below Minimum Salary -->
        <div class="bg-white rounded-xl shadow-md border-l-4 {{ $belowMinimum->count() > 0 ? 'border-red-500' : 'border-green-500' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">Below Position Minimum</h4>
                    <div class="p-3 {{ $belowMinimum->count() > 0 ? 'bg-red-100' : 'bg-green-100' }} rounded-full">
                        <i class="fas {{ $belowMinimum->count() > 0 ? 'fa-exclamation-triangle text-red-600' : 'fa-check-circle text-green-600' }} text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold {{ $belowMinimum->count() > 0 ? 'text-red-600' : 'text-green-600' }} mb-2">
                    {{ $belowMinimum->count() }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ $belowMinimum->count() > 0 ? 'Employees earning below their position minimum' : 'All employees meet position minimum' }}
                </p>
            </div>
        </div>

        <!-- Above Maximum Salary -->
        <div class="bg-white rounded-xl shadow-md border-l-4 {{ $aboveMaximum->count() > 0 ? 'border-orange-500' : 'border-green-500' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">Above Position Maximum</h4>
                    <div class="p-3 {{ $aboveMaximum->count() > 0 ? 'bg-orange-100' : 'bg-green-100' }} rounded-full">
                        <i class="fas {{ $aboveMaximum->count() > 0 ? 'fa-arrow-up text-orange-600' : 'fa-check-circle text-green-600' }} text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold {{ $aboveMaximum->count() > 0 ? 'text-orange-600' : 'text-green-600' }} mb-2">
                    {{ $aboveMaximum->count() }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ $aboveMaximum->count() > 0 ? 'Employees earning above their position maximum' : 'All employees within position maximum' }}
                </p>
            </div>
        </div>

        <!-- Salary Grade Mismatches -->
        <div class="bg-white rounded-xl shadow-md border-l-4 {{ $gradeMismatches->count() > 0 ? 'border-yellow-500' : 'border-green-500' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">Grade Mismatches</h4>
                    <div class="p-3 {{ $gradeMismatches->count() > 0 ? 'bg-yellow-100' : 'bg-green-100' }} rounded-full">
                        <i class="fas {{ $gradeMismatches->count() > 0 ? 'fa-exclamation-circle text-yellow-600' : 'fa-check-circle text-green-600' }} text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold {{ $gradeMismatches->count() > 0 ? 'text-yellow-600' : 'text-green-600' }} mb-2">
                    {{ $gradeMismatches->count() }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ $gradeMismatches->count() > 0 ? 'Employees with salary grade mismatches' : 'All salary grades match' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Employees Below Minimum -->
    @if($belowMinimum->count() > 0)
        <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
            <div class="p-6 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-900 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Employees Below Position Minimum ({{ $belowMinimum->count() }})
                </h3>
                <p class="text-sm text-red-700 mt-1">These employees are earning below their position's minimum salary</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position Min</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($belowMinimum as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $employee->employee_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->position->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                    ₱{{ number_format($employee->basic_salary, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($employee->position->min_salary ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                    -₱{{ number_format(($employee->position->min_salary ?? 0) - $employee->basic_salary, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.salaries.adjust-form', $employee->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-edit mr-1"></i> Adjust
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Employees Above Maximum -->
    @if($aboveMaximum->count() > 0)
        <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
            <div class="p-6 border-b border-gray-200 bg-orange-50">
                <h3 class="text-lg font-semibold text-orange-900 flex items-center">
                    <i class="fas fa-arrow-up mr-2"></i>
                    Employees Above Position Maximum ({{ $aboveMaximum->count() }})
                </h3>
                <p class="text-sm text-orange-700 mt-1">These employees are earning above their position's maximum salary</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position Max</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($aboveMaximum as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $employee->employee_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->position->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">
                                    ₱{{ number_format($employee->basic_salary, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($employee->position->max_salary ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">
                                    +₱{{ number_format($employee->basic_salary - ($employee->position->max_salary ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.salaries.show', $employee->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Salary Grade Mismatches -->
    @if($gradeMismatches->count() > 0)
        <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
            <div class="p-6 border-b border-gray-200 bg-yellow-50">
                <h3 class="text-lg font-semibold text-yellow-900 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Salary Grade Mismatches ({{ $gradeMismatches->count() }})
                </h3>
                <p class="text-sm text-yellow-700 mt-1">These employees' salaries don't match their assigned salary grade</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade/Step</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($gradeMismatches as $employee)
                            @php
                                $expectedSalary = App\Models\SalaryGrade::getSalary($employee->salary_grade, $employee->salary_step);
                                $difference = $employee->basic_salary - $expectedSalary;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $employee->employee_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Grade {{ $employee->salary_grade }} / Step {{ $employee->salary_step }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-600">
                                    ₱{{ number_format($employee->basic_salary, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($expectedSalary, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $difference > 0 ? 'text-orange-600' : 'text-red-600' }}">
                                    {{ $difference > 0 ? '+' : '' }}₱{{ number_format($difference, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.salaries.adjust-form', $employee->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-sync mr-1"></i> Sync
                                    </a>
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
