@extends('admin.layouts.app')

@section('title', 'Edit DTR Entry')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit DTR Entry</h1>
                <p class="mt-2 text-sm text-gray-600">Manually correct or adjust DTR record</p>
            </div>
            <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Employee Information Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Employee Name</p>
                <p class="text-base font-medium text-gray-900">{{ $dtrEntry->employee->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Department</p>
                <p class="text-base font-medium text-gray-900">{{ $dtrEntry->employee->department->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">DTR Date</p>
                <p class="text-base font-medium text-gray-900">
                    {{ \Carbon\Carbon::parse($dtrEntry->dtr_date)->format('F d, Y (l)') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.dtr.update', $dtrEntry->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Morning Session -->
                <div class="border-r pr-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-sun text-yellow-500 mr-2"></i>
                        Morning Session (AM)
                    </h4>

                    <!-- AM Arrival -->
                    <div class="mb-4">
                        <label for="am_arrival" class="block text-sm font-medium text-gray-700 mb-2">
                            Arrival Time
                        </label>
                        <input type="text" id="am_arrival" name="am_arrival"
                               value="{{ $dtrEntry->am_arrival ? \Carbon\Carbon::parse($dtrEntry->am_arrival)->format('h:i A') : '' }}"
                               placeholder="08:00 AM"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('am_arrival') border-red-500 @enderror">
                        @error('am_arrival')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Expected: 08:00 AM</p>
                    </div>

                    <!-- AM Departure -->
                    <div class="mb-4">
                        <label for="am_departure" class="block text-sm font-medium text-gray-700 mb-2">
                            Departure Time
                        </label>
                        <input type="text" id="am_departure" name="am_departure"
                               value="{{ $dtrEntry->am_departure ? \Carbon\Carbon::parse($dtrEntry->am_departure)->format('h:i A') : '' }}"
                               placeholder="12:00 PM"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('am_departure') border-red-500 @enderror">
                        @error('am_departure')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Expected: 12:00 PM</p>
                    </div>
                </div>

                <!-- Afternoon Session -->
                <div class="pl-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-cloud-sun text-orange-500 mr-2"></i>
                        Afternoon Session (PM)
                    </h4>

                    <!-- PM Arrival -->
                    <div class="mb-4">
                        <label for="pm_arrival" class="block text-sm font-medium text-gray-700 mb-2">
                            Arrival Time
                        </label>
                        <input type="text" id="pm_arrival" name="pm_arrival"
                               value="{{ $dtrEntry->pm_arrival ? \Carbon\Carbon::parse($dtrEntry->pm_arrival)->format('h:i A') : '' }}"
                               placeholder="01:00 PM"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pm_arrival') border-red-500 @enderror">
                        @error('pm_arrival')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Expected: 01:00 PM</p>
                    </div>

                    <!-- PM Departure -->
                    <div class="mb-4">
                        <label for="pm_departure" class="block text-sm font-medium text-gray-700 mb-2">
                            Departure Time
                        </label>
                        <input type="text" id="pm_departure" name="pm_departure"
                               value="{{ $dtrEntry->pm_departure ? \Carbon\Carbon::parse($dtrEntry->pm_departure)->format('h:i A') : '' }}"
                               placeholder="05:00 PM"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pm_departure') border-red-500 @enderror">
                        @error('pm_departure')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Expected: 05:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <div class="mt-6 pt-6 border-t">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                    Remarks / Notes
                </label>
                <textarea id="remarks" name="remarks" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('remarks') border-red-500 @enderror"
                          placeholder="Add any notes or remarks about this DTR entry (e.g., Official Business, Half Day, etc.)">{{ $dtrEntry->remarks }}</textarea>
                @error('remarks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Values Info -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h5 class="text-sm font-semibold text-blue-900 mb-2">Current Calculated Values:</h5>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-blue-600">Total Hours:</p>
                        <p class="font-medium text-blue-900">{{ $dtrEntry->total_hours }}h {{ $dtrEntry->total_minutes }}m</p>
                    </div>
                    <div>
                        <p class="text-blue-600">Undertime:</p>
                        <p class="font-medium text-blue-900">
                            {{ floor($dtrEntry->under_time_minutes / 60) }}h {{ $dtrEntry->under_time_minutes % 60 }}m
                        </p>
                    </div>
                    <div>
                        <p class="text-blue-600">Status:</p>
                        <p class="font-medium text-blue-900">{{ $dtrEntry->status ?? 'Present' }}</p>
                    </div>
                </div>
                <p class="text-xs text-blue-700 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    These values will be automatically recalculated when you save the changes.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Warning Notice -->
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                <p class="mt-2 text-sm text-yellow-700">
                    Manually editing DTR entries should only be done for corrections or legitimate adjustments.
                    All changes are logged and can be audited. Please ensure you have proper authorization before making changes.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Validate time inputs
document.querySelectorAll('input[type="time"]').forEach(input => {
    input.addEventListener('change', function() {
        const value = this.value;
        if (value) {
            const [hours, minutes] = value.split(':');
            if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                alert('Please enter a valid time.');
                this.value = '';
            }
        }
    });
});

// Warn before leaving with unsaved changes
let formChanged = false;
document.querySelector('form').addEventListener('change', () => {
    formChanged = true;
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.querySelector('form').addEventListener('submit', () => {
    formChanged = false;
});
</script>
@endpush
