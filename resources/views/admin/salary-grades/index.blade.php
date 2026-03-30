@extends('admin.layouts.app')

@section('title', 'Salary Grade Management')

@section('content')
@php $isHR = request()->routeIs('hr.*'); @endphp
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Salary Grade Management"
        description="{{ $isHR ? 'View government salary schedules and employee salary grades' : 'Manage government salary schedules and employee salary grades' }}"
    >
        @if(!$isHR)
        <x-slot name="actions">
            <x-admin.action-button onclick="deleteSchedule()" variant="danger" icon="fas fa-trash-alt">Delete Schedule</x-admin.action-button>
            <x-admin.action-button onclick="updateAllEmployeeSalaries()" variant="success" icon="fas fa-sync">Update All Employee Salaries</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.salary-grades.create') }}" variant="primary" icon="fas fa-plus">Add New Schedule</x-admin.action-button>
        </x-slot>
        @endif
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Total Schedules" :value="$stats['total_schedules']" icon="fas fa-calendar-alt" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Total Grades" :value="$stats['total_grades']" icon="fas fa-layer-group" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Min Salary" :value="'₱' . number_format($stats['min_salary'], 2)" icon="fas fa-arrow-down" gradientFrom="yellow-500" gradientTo="yellow-600"/>
        <x-admin.gradient-stat-card title="Max Salary" :value="'₱' . number_format($stats['max_salary'], 2)" icon="fas fa-arrow-up" gradientFrom="purple-500" gradientTo="purple-600"/>
        <x-admin.gradient-stat-card title="Employees with Grades" :value="$stats['employees_with_grades']" icon="fas fa-users" gradientFrom="indigo-500" gradientTo="indigo-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Schedule Selector -->
    <x-admin.card class="mb-6">
        <form action="{{ $isHR ? route('hr.salary-grades.index') : route('admin.salary-grades.index') }}" method="GET">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Salary Schedule:</label>
                <select name="effective_date" onchange="this.form.submit()" class="block w-full md:w-72 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @foreach($effectiveDates as $date)
                        <option value="{{ $date->effective_date->format('Y-m-d') }}" {{ $selectedDate == $date->effective_date->format('Y-m-d') ? 'selected' : '' }}>
                            {{ $date->effective_date->format('F d, Y') }}@if($date->tranche) - {{ $date->tranche }}@endif
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </x-admin.card>

    <!-- Salary Grades Matrix Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">Grade</th>
                    @for($step = 1; $step <= 8; $step++)
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Step {{ $step }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($salaryGrades as $grade => $steps)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 sticky left-0 bg-white">
                        <x-admin.badge variant="info">SG-{{ $grade }}</x-admin.badge>
                    </td>
                    @for($stepNum = 1; $stepNum <= 8; $stepNum++)
                        @php $stepData = $steps->get($stepNum); @endphp
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 group relative">
                            @if($stepData)
                                @if($isHR)
                                    <span class="font-medium">₱{{ number_format($stepData->amount, 2) }}</span>
                                @else
                                <a href="{{ route('admin.salary-grades.edit', $stepData->id) }}" class="block w-full h-full hover:text-blue-600 transition-colors" title="Click to edit">
                                    <span class="font-medium">₱{{ number_format($stepData->amount, 2) }}</span>
                                    <i class="fas fa-pencil-alt absolute top-1/2 right-2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 text-gray-400 text-xs"></i>
                                </a>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    @endfor
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        @if($isHR)
                        <x-admin.empty-state
                            icon="fas fa-table"
                            title="No salary grades found"
                            message="Contact an administrator to create salary schedules"
                        />
                        @else
                        <x-admin.empty-state
                            icon="fas fa-table"
                            title="No salary grades found"
                            message="Create a new salary schedule to get started"
                            actionText="Add New Schedule"
                            :actionLink="route('admin.salary-grades.create')"
                        />
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($salaryGrades->count() > 0)
        <x-slot name="footer">
            <div class="flex justify-between text-sm text-gray-700">
                <div><span class="font-medium">Total Salary Grades:</span> <span class="font-semibold text-gray-900">{{ $salaryGrades->count() }}</span></div>
                <div><span class="font-medium">Schedule:</span> <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</span></div>
            </div>
        </x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function updateAllEmployeeSalaries() {
    if (!confirm('This will update all employee salaries based on their assigned salary grades. Continue?')) {
        return;
    }

    fetch('{{ route("admin.salary-grades.update-employee-salaries") }}', {
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

    fetch('{{ route("admin.salary-grades.destroy-schedule") }}', {
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
            if (dateSelect.options.length > 1) {
                window.location.href = '{{ route("admin.salary-grades.index") }}';
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
