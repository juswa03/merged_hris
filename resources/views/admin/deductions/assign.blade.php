@extends('layouts.app')

@section('title', 'Assign Deduction to Employees')

@section('content')

<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Assign Deduction: {{ $deduction->name }}</h1>
        <p class="mt-2 text-sm text-gray-600">Select employees and set assignment details</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Assignment Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('deductions.storeAssignment', $deduction->id) }}" method="POST">
                    @csrf

                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Deduction Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-blue-600">Name:</p>
                                <p class="font-medium text-blue-900">{{ $deduction->name }}</p>
                            </div>
                            <div>
                                <p class="text-blue-600">Default Amount:</p>
                                <p class="font-medium text-blue-900">₱{{ number_format($deduction->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Employees *</label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto">
                            <div class="mb-3">
                                <label class="flex items-center text-sm font-medium text-blue-600 cursor-pointer">
                                    <input type="checkbox" id="select-all" class="rounded mr-2">
                                    Select All Employees
                                </label>
                            </div>
                            <div class="space-y-2">
                                @foreach($employees as $employee)
                                @php
                                    $isAssigned = in_array($employee->id, $assignedEmployeeIds);
                                @endphp
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded {{ $isAssigned ? 'bg-gray-100' : '' }}">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                                           class="employee-checkbox rounded mr-3" {{ $isAssigned ? 'disabled' : '' }}>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $employee->department->name ?? 'No Department' }}</p>
                                    </div>
                                    @if($isAssigned)
                                        <span class="text-xs text-green-600 font-medium">Already Assigned</span>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @error('employee_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="custom_amount" class="block text-sm font-medium text-gray-700 mb-2">Custom Amount (Optional)</label>
                        <input type="number" id="custom_amount" name="custom_amount" value="{{ old('custom_amount') }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use default amount (₱{{ number_format($deduction->amount, 2) }})</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-2">Effective From *</label>
                            <input type="date" id="effective_from" name="effective_from" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('effective_from') border-red-500 @enderror">
                            @error('effective_from')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="effective_to" class="block text-sm font-medium text-gray-700 mb-2">Effective To (Optional)</label>
                            <input type="date" id="effective_to" name="effective_to" value="{{ old('effective_to') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('effective_to') border-red-500 @enderror">
                            @error('effective_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Leave empty for ongoing deduction</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('deductions.show', $deduction->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Assign Deduction
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Tips</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                        <p>Select one or more employees to assign this deduction</p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2 mt-1"></i>
                        <p>Set a custom amount or use the default amount for all selected employees</p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-calendar text-purple-500 mr-2 mt-1"></i>
                        <p>Set effective dates to control when the deduction applies</p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <p>Already assigned employees are marked and cannot be selected again</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                    <p class="text-sm text-yellow-700">
                        Deductions will be automatically applied during payroll calculation for the specified period.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select All functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.employee-checkbox:not([disabled])');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Update Select All when individual checkboxes change
document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.employee-checkbox:not([disabled])');
        const checkedCheckboxes = document.querySelectorAll('.employee-checkbox:not([disabled]):checked');
        document.getElementById('select-all').checked = allCheckboxes.length === checkedCheckboxes.length;
    });
});
</script>
@endpush

@endsection
