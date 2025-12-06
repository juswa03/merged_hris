@extends('layouts.app')
@section('title', 'Assign Allowance')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Assign Allowance: {{ $allowance->name }}</h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('allowances.storeAssignment', $allowance->id) }}" method="POST">
                    @csrf
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="text-sm font-semibold mb-2">Allowance: {{ $allowance->name }}</h3>
                        <p class="text-sm">Amount: ₱{{ number_format($allowance->amount, 2) }} | Type: {{ ucfirst($allowance->type) }}</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Employees *</label>
                        <div class="border rounded-lg p-4 max-h-96 overflow-y-auto">
                            <label class="flex items-center mb-3 text-sm font-medium text-blue-600 cursor-pointer">
                                <input type="checkbox" id="select-all" class="rounded mr-2">Select All
                            </label>
                            <div class="space-y-2">
                                @foreach($employees as $employee)
                                @php $isAssigned = in_array($employee->id, $assignedEmployeeIds); @endphp
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded {{ $isAssigned ? 'bg-gray-100' : '' }}">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                                           class="employee-checkbox rounded mr-3" {{ $isAssigned ? 'disabled' : '' }}>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ $employee->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $employee->department->name ?? 'No Dept' }}</p>
                                    </div>
                                    @if($isAssigned)<span class="text-xs text-green-600">Assigned</span>@endif
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-2">Effective From *</label>
                            <input type="date" id="effective_from" name="effective_from" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required
                                   class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label for="effective_to" class="block text-sm font-medium text-gray-700 mb-2">Effective To</label>
                            <input type="date" id="effective_to" name="effective_to" value="{{ old('effective_to') }}"
                                   class="w-full border rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Leave empty for ongoing</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('allowances.show', $allowance->id) }}" class="bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            <i class="fas fa-user-plus mr-2"></i>Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Tips</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p><i class="fas fa-info-circle text-blue-500 mr-2"></i>Select employees to assign</p>
                    <p><i class="fas fa-calendar text-purple-500 mr-2"></i>Set effective dates</p>
                    <p><i class="fas fa-check text-green-500 mr-2"></i>Already assigned are marked</p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.employee-checkbox:not([disabled])').forEach(cb => cb.checked = this.checked);
});
document.querySelectorAll('.employee-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const all = document.querySelectorAll('.employee-checkbox:not([disabled])');
        const checked = document.querySelectorAll('.employee-checkbox:not([disabled]):checked');
        document.getElementById('select-all').checked = all.length === checked.length;
    });
});
</script>
@endpush
@endsection
