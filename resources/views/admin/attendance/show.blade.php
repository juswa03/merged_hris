@extends('admin.layouts.app')

@section('title', 'Employee Attendance Details')

@section('content')
<div class="w-full px-6 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Employee Attendance Details"
        description="View detailed attendance information for {{ $employee->first_name }} {{ $employee->last_name }}"
    >
        <x-slot name="actions">
            <x-admin.action-button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('attendance.index') }}'"
            >
                Back to Attendance
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Employee Info Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 mt-8 mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Employee Name -->
            <div>
                <p class="text-sm font-medium text-gray-600">Employee Name</p>
                <p class="text-lg font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
            </div>

            <!-- Employee ID -->
            <div>
                <p class="text-sm font-medium text-gray-600">Employee ID</p>
                <p class="text-lg font-semibold text-gray-900">{{ $employee->employee_code ?? $employee->id }}</p>
            </div>

            <!-- Department -->
            <div>
                <p class="text-sm font-medium text-gray-600">Department</p>
                <p class="text-lg font-semibold text-gray-900">{{ $employee->department->name ?? 'N/A' }}</p>
            </div>

            <!-- Position -->
            <div>
                <p class="text-sm font-medium text-gray-600">Position</p>
                <p class="text-lg font-semibold text-gray-900">{{ $employee->position->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-md border border-blue-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Total Days</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $stats['total_days'] }}</p>
                </div>
                <div class="p-3 bg-blue-200 rounded-full">
                    <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-md border border-green-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">Total Hours</p>
                    <p class="text-3xl font-bold text-green-900">{{ $stats['total_hours'] }}</p>
                </div>
                <div class="p-3 bg-green-200 rounded-full">
                    <i class="fas fa-hourglass text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-md border border-purple-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600">Avg Hours/Day</p>
                    <p class="text-3xl font-bold text-purple-900">{{ $stats['avg_hours_per_day'] }}</p>
                </div>
                <div class="p-3 bg-purple-200 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-md border border-orange-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600">Check-In/Out</p>
                    <p class="text-2xl font-bold text-orange-900">{{ $stats['total_checkins'] }}/{{ $stats['total_checkouts'] }}</p>
                </div>
                <div class="p-3 bg-orange-200 rounded-full">
                    <i class="fas fa-sync-alt text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('admin.attendance.show', $employee->id) }}" class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-600"></i>
                Filter Period
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2 flex-1">
                        <i class="fas fa-search"></i> Apply
                    </button>
                    <a href="{{ route('admin.attendance.show', $employee->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-list mr-2 text-blue-600"></i>
                Attendance Records
                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                    {{ $attendances->count() }} records
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $attendance->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-mono text-gray-700">{{ $attendance->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $attendance->attendanceType->name == 'Check-in' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                <i class="fas mr-1 {{ $attendance->attendanceType->name == 'Check-in' ? 'fa-sign-in-alt' : 'fa-sign-out-alt' }}"></i>
                                {{ $attendance->attendanceType->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $attendance->attendanceSource->name == 'Biometric' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas mr-1 {{ $attendance->attendanceSource->name == 'Biometric' ? 'fa-fingerprint' : 'fa-keyboard' }}"></i>
                                {{ $attendance->attendanceSource->name ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">No attendance records found for this period</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Daily Summary -->
        @if($attendances->count() > 0)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Daily Breakdown</h4>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @php
                    $groupedByDate = $attendances->groupBy(function($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                @endphp

                @foreach($groupedByDate as $date => $records)
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('M d, Y (l)') }}</p>
                        <p class="text-xs text-gray-600">{{ $records->count() }} record{{ $records->count() != 1 ? 's' : '' }}</p>
                    </div>
                    <div>
                        @php
                            $dayCheckins = $records->where('attendanceType.name', 'Check-in')->count();
                            $dayCheckouts = $records->where('attendanceType.name', 'Check-out')->count();
                        @endphp
                        <span class="inline-flex items-center gap-2">
                            <span class="text-green-600 text-sm">
                                <i class="fas fa-sign-in-alt"></i> {{ $dayCheckins }}
                            </span>
                            <span class="text-orange-600 text-sm">
                                <i class="fas fa-sign-out-alt"></i> {{ $dayCheckouts }}
                            </span>
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// You can add additional functionality here if needed
document.addEventListener('DOMContentLoaded', function() {
    // Any client-side logic can go here
});
</script>
@endpush
@endsection
