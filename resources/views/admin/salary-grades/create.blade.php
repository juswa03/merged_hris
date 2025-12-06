@extends('layouts.app')

@section('title', 'Create Salary Schedule')

@section('content')
<div class="w-full px-6 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Create New Salary Schedule"
        description="Define a new salary grade schedule with effective date"
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

    <form method="POST" action="{{ route('salary-grades.store') }}" id="salaryScheduleForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Schedule Details Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Schedule Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Effective Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-1"></i> Effective Date *
                                </label>
                                <input type="date" name="effective_date" id="effective_date" required
                                       value="{{ old('effective_date', date('Y-m-d')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('effective_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tranche/Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-1"></i> Tranche/Name *
                                </label>
                                <input type="text" name="tranche" id="tranche" required
                                       value="{{ old('tranche') }}"
                                       placeholder="e.g., Tranche 5, 2025 SSL"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('tranche')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1"></i> Remarks
                            </label>
                            <textarea name="remarks" id="remarks" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Optional notes about this salary schedule">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Grid Configuration Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-sliders-h mr-2 text-purple-600"></i>
                            Grid Configuration
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Number of Grades -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-layer-group mr-1"></i> Number of Grades *
                                </label>
                                <input type="number" name="num_grades" id="num_grades" required
                                       value="{{ old('num_grades', 33) }}"
                                       min="1" max="50"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       onchange="regenerateTable()">
                                <p class="mt-1 text-xs text-gray-500">1 - 50 grades</p>
                                @error('num_grades')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Number of Steps -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-arrow-up mr-1"></i> Number of Steps *
                                </label>
                                <input type="number" name="num_steps" id="num_steps" required
                                       value="{{ old('num_steps', 8) }}"
                                       min="1" max="15"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       onchange="regenerateTable()">
                                <p class="mt-1 text-xs text-gray-500">1 - 15 steps</p>
                                @error('num_steps')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Info:</strong> Change these values and the salary grid below will automatically update. You can have between 1-50 grades and 1-15 steps.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Salary Grid Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-table mr-2 text-green-600"></i>
                                Salary Grid <span id="grid-title-info" class="text-sm text-gray-500 ml-2">(Grades 1-33, Steps 1-8)</span>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Enter salary amounts for each grade and step combination</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="fillSampleData()" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-sm">
                                <i class="fas fa-magic mr-1"></i> Fill Sample Data
                            </button>
                            <button type="button" onclick="clearAllFields()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                <i class="fas fa-eraser mr-1"></i> Clear All
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-auto max-h-[70vh]">
                            <table class="min-w-full border-collapse border border-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-20">
                                    <tr>
                                        <th class="px-4 py-3 border border-gray-200 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 z-30 bg-gray-50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                            Grade
                                        </th>
                                        <tbody id="salaryGridBody"></tbody>
                                    </tr>
                                </thead>
                                <tbody id="salaryGridBody" class="bg-white">
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Tip:</strong> You can click "Fill Sample Data" to populate the grid with typical government salary progression values, then adjust as needed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 lg:sticky lg:top-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-600 to-green-700">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-chart-line mr-2"></i>
                            Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Progress -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Completion</p>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-700" id="filled_count">0 / 264</span>
                                <span class="text-sm font-bold text-green-600" id="filled_percentage">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progress_bar" class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-700">Total Entries</span>
                                <span class="text-lg font-bold text-blue-600">264</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm text-gray-700">Lowest Amount</span>
                                <span class="text-sm font-bold text-purple-600" id="min_amount">-</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span class="text-sm text-gray-700">Highest Amount</span>
                                <span class="text-sm font-bold text-green-600" id="max_amount">-</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-md hover:shadow-lg font-semibold">
                            <i class="fas fa-save mr-2"></i> Create Salary Schedule
                        </button>

                        <button type="button" onclick="window.location.href='{{ route('salary-grades.index') }}'"
                                class="w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 transition-all duration-200 font-semibold">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const maxGrades = 50;
const maxSteps = 15;

function regenerateTable() {
    const numGrades = parseInt(document.getElementById('num_grades').value) || 33;
    const numSteps = parseInt(document.getElementById('num_steps').value) || 8;

    // Validation
    if (numGrades < 1 || numGrades > maxGrades) {
        alert(`Number of grades must be between 1 and ${maxGrades}`);
        document.getElementById('num_grades').value = 33;
        return;
    }
    if (numSteps < 1 || numSteps > maxSteps) {
        alert(`Number of steps must be between 1 and ${maxSteps}`);
        document.getElementById('num_steps').value = 8;
        return;
    }

    // Update title info
    document.getElementById('grid-title-info').textContent = `(Grades 1-${numGrades}, Steps 1-${numSteps})`;

    // Generate table header
    let headerHtml = `<tr>
        <th class="px-4 py-3 border border-gray-200 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 z-30 bg-gray-50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
            Grade
        </th>`;
    
    for (let step = 1; step <= numSteps; step++) {
        headerHtml += `<th class="px-4 py-3 border border-gray-200 text-center text-xs font-medium text-gray-500 uppercase bg-gray-50">
            Step ${step}
        </th>`;
    }
    headerHtml += `</tr>`;

    document.querySelector('thead tr').innerHTML = headerHtml;

    // Generate table body
    let bodyHtml = '';
    for (let grade = 1; grade <= numGrades; grade++) {
        const bgClass = grade % 2 === 0 ? 'bg-gray-50' : 'bg-white';
        bodyHtml += `<tr class="${bgClass}">
            <td class="px-4 py-2 border border-gray-200 font-semibold text-sm text-gray-700 sticky left-0 z-10 ${bgClass} shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                Grade ${grade}
            </td>`;
        
        for (let step = 1; step <= numSteps; step++) {
            const index = (grade - 1) * numSteps + (step - 1);
            const existingValue = document.querySelector(`input[id="grade_${grade}_step_${step}"]`)?.value || '';
            
            bodyHtml += `<td class="px-2 py-2 border border-gray-200">
                <input type="hidden" name="grades[${index}][grade]" value="${grade}">
                <input type="hidden" name="grades[${index}][step]" value="${step}">
                <input type="number"
                       name="grades[${index}][amount]"
                       id="grade_${grade}_step_${step}"
                       step="0.01"
                       min="0"
                       required
                       placeholder="0.00"
                       value="${existingValue}"
                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </td>`;
        }
        bodyHtml += `</tr>`;
    }

    document.getElementById('salaryGridBody').innerHTML = bodyHtml;

    // Re-attach event listeners
    const inputs = document.querySelectorAll('input[name*="[amount]"]');
    inputs.forEach(input => {
        input.removeEventListener('input', updateSummary);
        input.addEventListener('input', updateSummary);
    });

    // Update summary
    updateSummary();
}

function updateSummary() {
    const inputs = document.querySelectorAll('input[name*="[amount]"]');
    let filled = 0;
    let min = Infinity;
    let max = -Infinity;

    inputs.forEach(input => {
        const value = parseFloat(input.value);
        if (value && value > 0) {
            filled++;
            min = Math.min(min, value);
            max = Math.max(max, value);
        }
    });

    const total = inputs.length;
    const percentage = total > 0 ? ((filled / total) * 100).toFixed(1) : 0;

    document.getElementById('filled_count').textContent = `${filled} / ${total}`;
    document.getElementById('filled_percentage').textContent = `${percentage}%`;
    document.getElementById('progress_bar').style.width = `${percentage}%`;

    document.getElementById('min_amount').textContent = filled > 0 ? '₱' + min.toLocaleString('en-PH', {minimumFractionDigits: 2}) : '-';
    document.getElementById('max_amount').textContent = filled > 0 ? '₱' + max.toLocaleString('en-PH', {minimumFractionDigits: 2}) : '-';
}

function fillSampleData() {
    if (!confirm('This will fill all fields with sample salary data. Continue?')) return;

    const numGrades = parseInt(document.getElementById('num_grades').value) || 33;
    const numSteps = parseInt(document.getElementById('num_steps').value) || 8;

    // Base salary for Grade 1 Step 1
    const baseSalary = 12000;
    const gradeIncrement = 1500; // Increment per grade
    const stepIncrement = 800;   // Increment per step

    for (let grade = 1; grade <= numGrades; grade++) {
        for (let step = 1; step <= numSteps; step++) {
            const amount = baseSalary + ((grade - 1) * gradeIncrement) + ((step - 1) * stepIncrement);
            const input = document.getElementById(`grade_${grade}_step_${step}`);
            if (input) {
                input.value = amount.toFixed(2);
            }
        }
    }

    updateSummary();
}

function clearAllFields() {
    if (!confirm('This will clear all salary amounts. Are you sure?')) return;

    const inputs = document.querySelectorAll('input[name*="[amount]"]');
    inputs.forEach(input => input.value = '');
    updateSummary();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Generate initial table
    regenerateTable();

    // Form validation
    document.getElementById('salaryScheduleForm').addEventListener('submit', function(e) {
        const filled = document.getElementById('filled_count').textContent.split(' / ')[0];
        if (parseInt(filled) === 0) {
            e.preventDefault();
            alert('Please fill in at least some salary amounts before submitting.');
            return false;
        }
    });
});
</script>
@endpush
@endsection
