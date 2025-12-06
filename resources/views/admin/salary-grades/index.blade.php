@extends('layouts.app')

@section('title', 'Salary Grade Management')

@section('content')
<div class="w-full px-6 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Salary Grade Management"
        description="Manage government salary schedules and employee salary grades"
    >
    </x-admin.page-header>

    <div class="mt-8">
        <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Schedules</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $stats['total_schedules'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Grades</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $stats['total_grades'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-layer-group text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Min Salary</p>
                    <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($stats['min_salary'], 2) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-arrow-down text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Max Salary</p>
                    <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($stats['max_salary'], 2) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-arrow-up text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Employees with Grades</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $stats['employees_with_grades'] }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <i class="fas fa-users text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Filters & Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <!-- Schedule Selector -->
            <form action="{{ route('salary-grades.index') }}" method="GET" class="inline">
                <select name="effective_date" onchange="this.form.submit()" class="block w-full md:w-64 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @foreach($effectiveDates as $date)
                        <option value="{{ $date->effective_date->format('Y-m-d') }}" {{ $selectedDate == $date->effective_date->format('Y-m-d') ? 'selected' : '' }}>
                            {{ $date->effective_date->format('F d, Y') }} @if($date->tranche) - {{ $date->tranche }} @endif
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="flex gap-3 w-full md:w-auto">
            <button onclick="deleteSchedule()" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center justify-center">
                <i class="fas fa-trash-alt mr-2"></i> Delete Schedule
            </button>
            <button onclick="updateAllEmployeeSalaries()" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center justify-center">
                <i class="fas fa-sync mr-2"></i> Update All Employee Salaries
            </button>
            <a href="{{ route('salary-grades.create') }}" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i> Add New Schedule
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

    <!-- Salary Grades Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">
                            Grade
                        </th>
                        @for($step = 1; $step <= 8; $step++)
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Step {{ $step }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($salaryGrades as $grade => $steps)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 sticky left-0 bg-white">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                SG-{{ $grade }}
                            </span>
                        </td>
                        @for($stepNum = 1; $stepNum <= 8; $stepNum++)
                            @php
                                $stepData = $steps->get($stepNum);
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 group relative">
                                @if($stepData)
                                    <a href="{{ route('salary-grades.edit', $stepData->id) }}" class="block w-full h-full hover:text-blue-600 transition-colors" title="Click to edit">
                                        <span class="font-medium">₱{{ number_format($stepData->amount, 2) }}</span>
                                        <i class="fas fa-pencil-alt absolute top-1/2 right-2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 text-gray-400 text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        @endfor
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-table text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No salary grades found for this schedule</p>
                                <p class="text-gray-400 text-sm mt-2">Create a new salary schedule to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salaryGrades->count() > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between text-sm text-gray-700">
                <div>
                    <span class="font-medium">Total Salary Grades:</span>
                    <span class="ml-2 font-semibold text-gray-900">{{ $salaryGrades->count() }}</span>
                </div>
                <div>
                    <span class="font-medium">Schedule:</span>
                    <span class="ml-2 font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateAllEmployeeSalaries() {
    if (!confirm('This will update all employee salaries based on their assigned salary grades. Continue?')) {
        return;
    }

    fetch('{{ route("salary-grades.update-employee-salaries") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating employee salaries');
    });
}

function deleteSchedule() {
    const dateSelect = document.querySelector('select[name="effective_date"]');
    const selectedDate = dateSelect.value;
    const selectedText = dateSelect.options[dateSelect.selectedIndex].text;

    if (!confirm(`Are you sure you want to delete the entire salary schedule for ${selectedText}? This action cannot be undone.`)) {
        return;
    }

    fetch('{{ route("salary-grades.destroy-schedule") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ effective_date: selectedDate })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // If there are other options, select the first one, otherwise reload
            if (dateSelect.options.length > 1) {
                window.location.href = '{{ route("salary-grades.index") }}';
            } else {
                location.reload();
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the schedule');
    });
}
</script>
@endpush
@endsection
