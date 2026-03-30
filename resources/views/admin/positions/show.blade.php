@extends('admin.layouts.app')

@section('title', 'Position Details')

@section('content')
@php $isHR = request()->routeIs('hr.*'); @endphp
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ $isHR ? route('hr.positions.index') : route('admin.positions.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $position->name }}</h1>
                    <p class="text-sm text-gray-600 mt-1">Position Details</p>
                </div>
            </div>
            @if(!$isHR)
            <div class="flex gap-2">
                <a href="{{ route('admin.positions.edit', $position->id) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <button onclick="deletePosition()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Position Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Position Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-500">Position Name</label>
                <p class="mt-1 text-lg text-gray-900">{{ $position->name }}</p>
            </div>
            @if($position->title && $position->title !== $position->name)
            <div>
                <label class="text-sm font-medium text-gray-500">Position Title</label>
                <p class="mt-1 text-lg text-gray-900">{{ $position->title }}</p>
            </div>
            @endif
            <div>
                <label class="text-sm font-medium text-gray-500">Level</label>
                <p class="mt-1">
                    @if($position->level)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                            {{ $position->level === 'Executive' ? 'bg-purple-100 text-purple-800' :
                               ($position->level === 'Managerial' ? 'bg-indigo-100 text-indigo-800' :
                               ($position->level === 'Senior Level' ? 'bg-blue-100 text-blue-800' :
                               ($position->level === 'Mid Level' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                            {{ $position->level }}
                        </span>
                    @else
                        <span class="text-gray-500">Not specified</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Salary Grade</label>
                <p class="mt-1 text-lg text-gray-900">
                    @if($position->salary_grade)
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded font-semibold">
                            SG-{{ $position->salary_grade }}
                        </span>
                    @else
                        <span class="text-gray-500">Not set</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Salary Range</label>
                <p class="mt-1 text-lg text-gray-900">
                    @if($position->min_salary || $position->max_salary)
                        ₱{{ number_format($position->min_salary ?? 0, 2) }} - ₱{{ number_format($position->max_salary ?? 0, 2) }}
                    @else
                        <span class="text-gray-500">Not set</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Status</label>
                <p class="mt-1">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $position->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $position->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
            @if($position->description)
            <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-500">Job Description</label>
                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $position->description }}</p>
            </div>
            @endif
            @if($position->requirements)
            <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-500">Qualifications & Requirements</label>
                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $position->requirements }}</p>
            </div>
            @endif
            <div>
                <label class="text-sm font-medium text-gray-500">Created Date</label>
                <p class="mt-1 text-lg text-gray-900">{{ $position->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $stats['total_employees'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Employees</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $stats['active_employees'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-user-check text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg. Salary</p>
                    <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($stats['avg_salary'], 2) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-coins text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Payroll</p>
                    <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($stats['total_salary'], 2) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if($stats['total_employees'] > 0)
    <!-- Salary Analysis -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Salary Analysis</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-500">Expected Range</label>
                <p class="mt-1 text-lg text-gray-900">
                    @if($position->min_salary || $position->max_salary)
                        ₱{{ number_format($position->min_salary ?? 0, 2) }} - ₱{{ number_format($position->max_salary ?? 0, 2) }}
                    @else
                        <span class="text-gray-500">Not set</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Actual Range</label>
                <p class="mt-1 text-lg text-gray-900">
                    ₱{{ number_format($stats['min_actual_salary'], 2) }} - ₱{{ number_format($stats['max_actual_salary'], 2) }}
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Average Salary</label>
                <p class="mt-1 text-lg text-gray-900">₱{{ number_format($stats['avg_salary'], 2) }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Employees List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Employees in this Position</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employment Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hire Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Salary
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($position->employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($employee->photo_url)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($employee->photo_url) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-600 font-medium text-sm">
                                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $employee->contact_number }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $employee->employmentType->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($employee->jobStatus)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $employee->jobStatus->name === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $employee->jobStatus->name }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    N/A
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                            ₱{{ number_format($employee->basic_salary ?? 0, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No employees in this position</p>
                                <p class="text-gray-400 text-sm mt-2">Employees can be assigned to this position from the employee management page</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($position->employees->count() > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between text-sm text-gray-700">
                <div>
                    <span class="font-medium">Total Employees:</span>
                    <span class="ml-2 font-semibold text-gray-900">{{ $stats['total_employees'] }}</span>
                </div>
                <div>
                    <span class="font-medium">Total Monthly Payroll:</span>
                    <span class="ml-2 text-lg font-semibold text-gray-900">₱{{ number_format($stats['total_salary'], 2) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function deletePosition() {
    const employeeCount = {{ $position->employees->count() }};

    if (employeeCount > 0) {
        alert('Cannot delete position with assigned employees. Please reassign employees first.');
        return;
    }

    if (confirm('Are you sure you want to delete this position? This action cannot be undone.')) {
        fetch('/positions/{{ $position->id }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.positions.index") }}';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the position');
        });
    }
}
</script>
@endpush
@endsection
