@extends('layouts.app')

@section('title', 'Manual Attendance Entry')

@section('content')
<div class="w-full px-6 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Manual Attendance Entry"
        description="Manually record an attendance entry for corrections or late submissions"
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

    <form method="POST" action="{{ route('attendance.store') }}" class="mt-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-pen-square mr-2 text-blue-600"></i>
                            Entry Details
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Employee Selection -->
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i> Employee *
                            </label>
                            <select name="employee_id" id="employee_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select an employee...</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_code ?? 'ID: ' . $employee->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Attendance Type -->
                        <div>
                            <label for="attendance_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-1"></i> Attendance Type *
                            </label>
                            <select name="attendance_type_id" id="attendance_type_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select attendance type...</option>
                                @foreach($attendanceTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('attendance_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('attendance_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="attendance_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1"></i> Attendance Date *
                            </label>
                            <input type="date" name="attendance_date" id="attendance_date" required
                                   value="{{ old('attendance_date', date('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('attendance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="attendance_time" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hourglass-start mr-1"></i> Attendance Time *
                            </label>
                            <input type="time" name="attendance_time" id="attendance_time" required
                                   value="{{ old('attendance_time', date('H:i')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">24-hour format (HH:mm)</p>
                            @error('attendance_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1"></i> Remarks
                            </label>
                            <textarea name="remarks" id="remarks" rows="4"
                                      placeholder="Optional: Enter reason for manual entry (e.g., late arrival, system error, etc.)"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4 border-t border-gray-200">
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Save Attendance
                            </button>
                            <a href="{{ route('attendance.index') }}"
                               class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 transition font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 sticky top-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Information
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">When to use manual entry?</h4>
                            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                <li>Late attendance records</li>
                                <li>System errors or device failures</li>
                                <li>Attendance corrections</li>
                                <li>Off-site attendance</li>
                                <li>Special circumstances</li>
                            </ul>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <h4 class="font-semibold text-amber-900 mb-2">Important Notes</h4>
                            <ul class="text-sm text-amber-800 space-y-1 list-disc list-inside">
                                <li>Use current date if not specified</li>
                                <li>Provide remarks for audit trail</li>
                                <li>Verify employee before saving</li>
                                <li>Check-in before Check-out</li>
                            </ul>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                <strong>Record saved automatically</strong> with the manual source identifier for tracking purposes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
