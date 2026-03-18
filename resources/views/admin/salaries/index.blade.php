@extends('admin.layouts.app')

@section('title', 'Salary Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Salary Management"
        description="Manage employee salaries, adjustments, and payroll overview"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.salaries.bulk-adjust-form') }}" variant="success" icon="fas fa-users-cog">
                Bulk Adjust
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Total Employees" :value="$stats['total_employees']" icon="fas fa-users" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Monthly Payroll" :value="'₱' . number_format($stats['total_monthly_payroll'], 2)" icon="fas fa-money-bill-wave" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Average Salary" :value="'₱' . number_format($stats['average_salary'], 2)" icon="fas fa-chart-bar" gradientFrom="purple-500" gradientTo="purple-600"/>
        <x-admin.gradient-stat-card title="Highest Salary" :value="'₱' . number_format($stats['highest_salary'], 2)" icon="fas fa-arrow-up" gradientFrom="yellow-500" gradientTo="yellow-600"/>
        <x-admin.gradient-stat-card title="Lowest Salary" :value="'₱' . number_format($stats['lowest_salary'], 2)" icon="fas fa-arrow-down" gradientFrom="red-500" gradientTo="red-600"/>
        <x-admin.gradient-stat-card title="With Grades" :value="$stats['employees_with_grades']" icon="fas fa-graduation-cap" gradientFrom="indigo-500" gradientTo="indigo-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form action="{{ route('admin.salaries.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="relative flex-1 lg:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search employee..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <select name="department_id" class="block w-full lg:w-48 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
            <select name="salary_grade" class="block w-full lg:w-40 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Grades</option>
                @for($i = 1; $i <= 33; $i++)
                    <option value="{{ $i }}" {{ request('salary_grade') == $i ? 'selected' : '' }}>SG-{{ $i }}</option>
                @endfor
            </select>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter">Filter</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.salaries.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Employee Salary Table -->
    <x-admin.card :padding="false" class="mb-6">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Grade</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" src="{{ $employee->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->first_name . ' ' . $employee->last_name) }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                <div class="text-sm text-gray-500">EMP-{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->position->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($employee->salary_grade && $employee->salary_step)
                            <x-admin.badge variant="info">SG-{{ $employee->salary_grade }} Step {{ $employee->salary_step }}</x-admin.badge>
                        @else
                            <span class="text-sm text-gray-400">Not Assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                        ₱{{ number_format($employee->basic_salary, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button :href="route('admin.salaries.show', $employee->id)" variant="info" icon="fas fa-eye" iconOnly size="sm" title="View Details"/>
                            <x-admin.action-button :href="route('admin.salaries.adjust-form', $employee->id)" variant="success" icon="fas fa-edit" iconOnly size="sm" title="Adjust Salary"/>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-admin.empty-state
                            icon="fas fa-users"
                            title="No employees found"
                            message="Try adjusting your filters or search criteria"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($employees->hasPages())
        <x-slot name="footer">{{ $employees->links() }}</x-slot>
        @endif
    </x-admin.card>

    <!-- Department Breakdown -->
    @if($departmentSalaries->count() > 0)
    <x-admin.card title="Department Salary Breakdown" :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salary</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Average Salary</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($departmentSalaries as $deptSalary)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $deptSalary->department->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $deptSalary->employee_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">₱{{ number_format($deptSalary->total_salary, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">₱{{ number_format($deptSalary->total_salary / $deptSalary->employee_count, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-admin.table-wrapper>
    </x-admin.card>
    @endif
</div>
@endsection
