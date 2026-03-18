@extends('admin.layouts.app')

@section('title', 'Adjust Employee Salary')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.salaries.index') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Salary Management
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('admin.salaries.show', $employee->id) }}" class="text-gray-700 hover:text-blue-600">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Adjust Salary</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Adjust Salary</h1>
        <p class="mt-2 text-sm text-gray-600">Modify employee salary and salary grade assignment</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Employee Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-6 text-center">
                    <img class="h-20 w-20 rounded-full mx-auto border-4 border-white shadow-lg"
                         src="{{ $employee->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->first_name . ' ' . $employee->last_name) }}"
                         alt="">
                    <h3 class="mt-4 text-lg font-semibold text-white">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                    <p class="text-blue-100 text-sm">EMP-{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Current Salary</p>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($employee->basic_salary, 2) }}</p>
                    </div>

                    <div class="border-t pt-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Department</p>
                        <p class="text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Position</p>
                        <p class="text-sm text-gray-900">{{ $employee->position->name ?? 'N/A' }}</p>
                    </div>

                    @if($employee->salary_grade && $employee->salary_step)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Current Grade</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            SG-{{ $employee->salary_grade }} Step {{ $employee->salary_step }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Adjustment Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.salaries.adjust', $employee->id) }}" method="POST" id="salaryAdjustForm">
                @csrf
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Salary Adjustment Details</h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Adjustment Type -->
                        <div>
                            <label for="adjustment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Adjustment Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="adjustment_type"
                                name="adjustment_type"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('adjustment_type') border-red-500 @enderror"
                            >
                                <option value="">Select adjustment type</option>
                                <option value="merit_increase">Merit Increase</option>
                                <option value="promotion">Promotion</option>
                                <option value="grade_change">Salary Grade Change</option>
                                <option value="annual_increment">Annual Increment</option>
                                <option value="adjustment">Manual Adjustment</option>
                            </select>
                            @error('adjustment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Salary Grade & Step -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="salary_grade" class="block text-sm font-medium text-gray-700 mb-2">
                                    Salary Grade
                                </label>
                                <select
                                    id="salary_grade"
                                    name="salary_grade"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('salary_grade') border-red-500 @enderror"
                                    onchange="updateSalaryFromGrade()"
                                >
                                    <option value="">Select grade</option>
                                    @for($i = 1; $i <= 33; $i++)
                                        <option value="{{ $i }}" {{ old('salary_grade', $employee->salary_grade) == $i ? 'selected' : '' }}>
                                            SG-{{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('salary_grade')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Philippine Government Salary Grade (1-33)</p>
                            </div>

                            <div>
                                <label for="salary_step" class="block text-sm font-medium text-gray-700 mb-2">
                                    Salary Step
                                </label>
                                <select
                                    id="salary_step"
                                    name="salary_step"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('salary_step') border-red-500 @enderror"
                                    onchange="updateSalaryFromGrade()"
                                >
                                    <option value="">Select step</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('salary_step', $employee->salary_step) == $i ? 'selected' : '' }}>
                                            Step {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('salary_step')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Increment level within grade (1-8)</p>
                            </div>
                        </div>

                        <!-- New Salary -->
                        <div>
                            <label for="new_salary" class="block text-sm font-medium text-gray-700 mb-2">
                                New Basic Salary <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">₱</span>
                                <input
                                    type="number"
                                    id="new_salary"
                                    name="new_salary"
                                    step="0.01"
                                    min="0"
                                    required
                                    value="{{ old('new_salary', $employee->basic_salary) }}"
                                    class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('new_salary') border-red-500 @enderror"
                                    placeholder="0.00"
                                >
                            </div>
                            @error('new_salary')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Current: ₱{{ number_format($employee->basic_salary, 2) }}
                                <span id="salary-difference" class="ml-2"></span>
                            </p>
                        </div>

                        <!-- Effective Date -->
                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Effective Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="effective_date"
                                name="effective_date"
                                required
                                value="{{ old('effective_date', now()->format('Y-m-d')) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('effective_date') border-red-500 @enderror"
                            >
                            @error('effective_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Date when the new salary takes effect</p>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Adjustment <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="reason"
                                name="reason"
                                rows="4"
                                required
                                maxlength="500"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror"
                                placeholder="Provide a detailed reason for this salary adjustment..."
                            >{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
                        </div>

                        <!-- Warning if large change -->
                        <div id="warning-box" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        This is a significant salary change. Please ensure all details are correct before submitting.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ route('admin.salaries.show', $employee->id) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            <i class="fas fa-save mr-2"></i> Apply Adjustment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const currentSalary = {{ $employee->basic_salary }};

// Update salary when grade/step changes
async function updateSalaryFromGrade() {
    const grade = document.getElementById('salary_grade').value;
    const step = document.getElementById('salary_step').value;

    if (!grade || !step) {
        return;
    }

    try {
        const response = await fetch('{{ route("admin.salary-grades.get-salary") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ grade, step })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('new_salary').value = data.salary;
            updateSalaryDifference();
        } else {
            alert('Salary grade not found in schedule');
        }
    } catch (error) {
        console.error('Error fetching salary:', error);
    }
}

// Calculate and display salary difference
function updateSalaryDifference() {
    const newSalary = parseFloat(document.getElementById('new_salary').value) || 0;
    const difference = newSalary - currentSalary;
    const percentChange = ((difference / currentSalary) * 100).toFixed(2);

    const diffElement = document.getElementById('salary-difference');
    const warningBox = document.getElementById('warning-box');

    if (difference !== 0) {
        const sign = difference > 0 ? '+' : '';
        const color = difference > 0 ? 'text-green-600' : 'text-red-600';

        diffElement.innerHTML = `<span class="${color} font-semibold">${sign}₱${Math.abs(difference).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${sign}${percentChange}%)</span>`;

        // Show warning if change is greater than 20%
        if (Math.abs(percentChange) > 20) {
            warningBox.classList.remove('hidden');
        } else {
            warningBox.classList.add('hidden');
        }
    } else {
        diffElement.innerHTML = '';
        warningBox.classList.add('hidden');
    }
}

// Listen to new salary changes
document.getElementById('new_salary').addEventListener('input', updateSalaryDifference);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSalaryDifference();
});
</script>
@endpush
@endsection
