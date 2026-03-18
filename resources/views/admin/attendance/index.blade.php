@extends('admin.layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Attendance Management"
        description="View and manage employee attendance records"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.attendance.create') }}" variant="primary" icon="fas fa-plus">Manual Entry</x-admin.action-button>
            <x-admin.action-button variant="success" icon="fas fa-download" onclick="exportAttendance()">Export</x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
        <x-admin.gradient-stat-card
            title="Total Records"
            :value="$stats['total_records']"
            icon="fas fa-list-alt"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Check-Ins"
            :value="$stats['total_checkins']"
            icon="fas fa-sign-in-alt"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Check-Outs"
            :value="$stats['total_checkouts']"
            icon="fas fa-sign-out-alt"
            gradientFrom="orange-500"
            gradientTo="orange-600"
        />
        <x-admin.gradient-stat-card
            title="Biometric"
            :value="$stats['biometric_count']"
            icon="fas fa-fingerprint"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
        <x-admin.gradient-stat-card
            title="Unique Employees"
            :value="$stats['unique_employees']"
            icon="fas fa-users"
            gradientFrom="indigo-500"
            gradientTo="indigo-600"
        />
    </div>

    @if(session('success'))
    <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
    <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $startDate->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $endDate->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Employee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                    <select name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="attendance_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        @foreach($attendanceTypes as $type)
                            <option value="{{ $type->id }}" {{ request('attendance_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-admin.action-button type="submit" variant="primary" icon="fas fa-search">Apply Filters</x-admin.action-button>
                <x-admin.action-button href="{{ route('admin.attendance.index') }}" variant="secondary" icon="fas fa-redo">Reset</x-admin.action-button>
            </div>
        </form>
    </x-admin.card>

    <!-- Attendance Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Employee</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Source</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.attendance.show', $attendance->employee_id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $attendance->employee->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">{{ $attendance->created_at->format('M d, Y') }}</p>
                                <p class="text-gray-600">{{ $attendance->created_at->format('H:i:s') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $typeName = $attendance->attendanceType->name ?? 'N/A';
                                $typeVariant = match(true) {
                                    str_contains(strtolower($typeName), 'in')  => 'success',
                                    str_contains(strtolower($typeName), 'out') => 'warning',
                                    default => 'default'
                                };
                            @endphp
                            <x-admin.badge :variant="$typeVariant">{{ $typeName }}</x-admin.badge>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $sourceName = $attendance->attendanceSource->name ?? 'N/A';
                                $sourceVariant = strtolower($sourceName) === 'biometric' ? 'info' : 'default';
                            @endphp
                            <x-admin.badge :variant="$sourceVariant">{{ $sourceName }}</x-admin.badge>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <x-admin.action-button
                                variant="danger"
                                icon="fas fa-trash-alt"
                                :iconOnly="true"
                                size="sm"
                                title="Delete"
                                onclick="deleteAttendance({{ $attendance->id }})"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <x-admin.empty-state
                                icon="fas fa-calendar-times"
                                title="No attendance records found"
                                message="Try adjusting your filters or date range"
                            />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
        </x-admin.table-wrapper>

        @if($attendances->hasPages())
        <x-slot name="footer">
            {{ $attendances->links() }}
        </x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function deleteAttendance(id) {
    if (!confirm('Are you sure you want to delete this attendance record?')) {
        return;
    }

    fetch(`/admin/attendance/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the record.');
    });
}

function exportAttendance() {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const employeeId = document.querySelector('select[name="employee_id"]').value;
    const departmentId = document.querySelector('select[name="department_id"]').value;

    let url = '/admin/attendance/export?start_date=' + startDate + '&end_date=' + endDate;
    if (employeeId) url += '&employee_id=' + employeeId;
    if (departmentId) url += '&department_id=' + departmentId;

    window.location.href = url;
}
</script>
@endpush
@endsection
