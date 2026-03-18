@extends('admin.layouts.app')

@section('title', 'Edit Salary Grade')

@section('content')
<div class="w-full px-6 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Edit Salary Grade"
        description="Update salary amount for specific grade and step"
    >
        <x-slot name="actions">
            <x-admin.action-button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('salary-grades.index') }}'"
            >
                Back to Salary Grades
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form id="editSalaryGradeForm">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-edit mr-2 text-blue-600"></i>
                            Salary Grade Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Current Information (Read-only) -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Current Information</h4>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Grade</p>
                                    <p class="text-lg font-bold text-gray-900">Grade {{ $salaryGrade->grade }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Step</p>
                                    <p class="text-lg font-bold text-gray-900">Step {{ $salaryGrade->step }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Effective Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($salaryGrade->effective_date)->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Tranche</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $salaryGrade->tranche ?? 'N/A' }}</p>
                                </div>
                            </div>

                            @if($salaryGrade->remarks)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Remarks</p>
                                    <p class="text-sm text-gray-700">{{ $salaryGrade->remarks }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Editable Fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-money-bill-wave mr-1"></i> Salary Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500">₱</span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" required
                                       value="{{ old('amount', $salaryGrade->amount) }}"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                                       oninput="updatePreview()">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter the new salary amount for this grade and step</p>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label class="text-sm font-medium text-gray-700">
                                    <i class="fas fa-toggle-on mr-1"></i> Active Status
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Inactive grades won't be available for employee assignment</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $salaryGrade->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Change Impact -->
                        @php
                            $affectedEmployees = \App\Models\Employee::where('salary_grade', $salaryGrade->grade)
                                                                     ->where('salary_step', $salaryGrade->step)
                                                                     ->count();
                        @endphp

                        @if($affectedEmployees > 0)
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-yellow-800 mb-1">Impact Notice</h4>
                                        <p class="text-sm text-yellow-700">
                                            This grade/step is currently assigned to <strong>{{ $affectedEmployees }}</strong> {{ Str::plural('employee', $affectedEmployees) }}.
                                            Changing this amount will affect their salary calculations.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg font-semibold">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                            <button type="button" onclick="deleteSalaryGrade()"
                                    class="flex-1 bg-red-100 text-red-700 py-3 rounded-lg hover:bg-red-200 transition-all duration-200 font-semibold">
                                <i class="fas fa-trash-alt mr-2"></i> Delete
                            </button>
                            <button type="button" onclick="window.location.href='{{ route('admin.salary-grades.index') }}'"
                                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 transition-all duration-200 font-semibold">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 sticky top-6">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-purple-700">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Current vs New -->
                    <div class="space-y-3">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase mb-1">Current Amount</p>
                            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($salaryGrade->amount, 2) }}</p>
                        </div>

                        <div class="text-center">
                            <i class="fas fa-arrow-down text-2xl text-gray-400"></i>
                        </div>

                        <div class="text-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                            <p class="text-xs text-green-700 font-medium uppercase mb-1">New Amount</p>
                            <p class="text-3xl font-bold text-green-800" id="preview_amount">₱{{ number_format($salaryGrade->amount, 2) }}</p>
                        </div>

                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-blue-700 mb-1">Change</p>
                            <p class="text-lg font-semibold" id="preview_change">₱0.00 (0%)</p>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="pt-4 border-t border-gray-200 space-y-3">
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <span class="text-sm text-gray-700">Affected Employees</span>
                            <span class="text-lg font-bold text-purple-600">{{ $affectedEmployees }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm text-gray-700">Status</span>
                            <span class="text-sm font-bold" id="preview_status">
                                {{ $salaryGrade->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-xs text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Changes will take effect immediately. Employee salaries using this grade will need to be recalculated manually if needed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const originalAmount = {{ $salaryGrade->amount }};

function updatePreview() {
    const newAmount = parseFloat(document.getElementById('amount').value) || originalAmount;
    const difference = newAmount - originalAmount;
    const percentChange = originalAmount > 0 ? ((difference / originalAmount) * 100) : 0;

    // Update preview amount
    document.getElementById('preview_amount').textContent = '₱' + newAmount.toLocaleString('en-PH', {minimumFractionDigits: 2});

    // Update change
    const changeText = (difference >= 0 ? '+' : '') + '₱' + Math.abs(difference).toLocaleString('en-PH', {minimumFractionDigits: 2}) +
                      ' (' + (percentChange >= 0 ? '+' : '') + percentChange.toFixed(1) + '%)';
    const changeElement = document.getElementById('preview_change');
    changeElement.textContent = changeText;
    changeElement.className = difference > 0 ? 'text-lg font-semibold text-green-600' :
                             difference < 0 ? 'text-lg font-semibold text-red-600' :
                             'text-lg font-semibold text-gray-600';
}

// Update status preview
document.getElementById('is_active').addEventListener('change', function() {
    const statusElement = document.getElementById('preview_status');
    if (this.checked) {
        statusElement.textContent = 'Active';
        statusElement.className = 'text-sm font-bold text-green-600';
    } else {
        statusElement.textContent = 'Inactive';
        statusElement.className = 'text-sm font-bold text-gray-600';
    }
});

// Handle form submission
document.getElementById('editSalaryGradeForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        amount: formData.get('amount'),
        is_active: formData.get('is_active') ? 1 : 0
    };

    fetch('{{ route("admin.salary-grades.update", $salaryGrade->id) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.salary-grades.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while saving changes.');
        console.error('Error:', error);
    });
});

// Delete function
function deleteSalaryGrade() {
    if (!confirm('Are you sure you want to delete this salary grade entry? This action cannot be undone.')) {
        return;
    }

    fetch('{{ route("admin.salary-grades.destroy", $salaryGrade->id) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.salary-grades.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the salary grade.');
    });
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
});
</script>
@endpush
@endsection
