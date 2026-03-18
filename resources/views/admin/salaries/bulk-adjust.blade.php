@extends('admin.layouts.app')

@section('title', 'Bulk Salary Adjustment')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Bulk Salary Adjustment"
        description="Apply salary adjustments to multiple employees at once"
    >
        <x-slot name="actions">
            <x-admin.action-button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('salaries.index') }}'"
            >
                Back to Salaries
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Warning Notice -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
            <div>
                <h3 class="text-sm font-semibold text-yellow-800 mb-1">Important Notice</h3>
                <p class="text-sm text-yellow-700">
                    Bulk salary adjustments will be applied immediately to all matching employees. Please review the filters and adjustment values carefully before proceeding.
                </p>
            </div>
        </div>
    </div>

    <!-- Bulk Adjustment Form -->
    <form method="POST" action="{{ route('admin.salaries.bulk-adjust') }}" id="bulkAdjustForm" onsubmit="return confirmBulkAdjustment()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Adjustment Details Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-calculator mr-2 text-blue-600"></i>
                            Adjustment Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Adjustment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cog mr-1"></i> Adjustment Type *
                            </label>
                            <select name="adjustment_type" id="adjustment_type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateAdjustmentLabel()">
                                <option value="percentage">Percentage Increase/Decrease</option>
                                <option value="fixed_amount">Fixed Amount Increase/Decrease</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Choose how the adjustment will be calculated</p>
                        </div>

                        <!-- Adjustment Value -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-percent mr-1"></i> <span id="adjustment_label">Adjustment Percentage</span> *
                            </label>
                            <div class="relative">
                                <input type="number" name="adjustment_value" id="adjustment_value" step="0.01" required
                                       class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter adjustment value"
                                       oninput="calculatePreview()">
                                <span id="adjustment_unit" class="absolute right-4 top-2.5 text-gray-500">%</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Use positive numbers for increases, negative for decreases</p>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1"></i> Reason for Adjustment *
                            </label>
                            <textarea name="reason" id="reason" rows="3" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Explain the reason for this bulk adjustment (e.g., Annual merit increase, Cost of living adjustment)"></textarea>
                            <p class="mt-1 text-xs text-gray-500">This will be recorded in the salary history</p>
                        </div>

                        <!-- Effective Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1"></i> Effective Date *
                            </label>
                            <input type="date" name="effective_date" id="effective_date" required
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Date when the salary adjustment takes effect</p>
                        </div>
                    </div>
                </div>

                <!-- Employee Filters Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-filter mr-2 text-green-600"></i>
                            Employee Filters
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Select which employees will receive the adjustment</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Department Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-1"></i> Department
                            </label>
                            <select name="filter_department" id="filter_department"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateEmployeeCount()">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Position Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-briefcase mr-1"></i> Position
                            </label>
                            <select name="filter_position" id="filter_position"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateEmployeeCount()">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Salary Grade Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-1"></i> Salary Grade
                            </label>
                            <select name="filter_grade" id="filter_grade"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateEmployeeCount()">
                                <option value="">All Grades</option>
                                @for($i = 1; $i <= 33; $i++)
                                    <option value="{{ $i }}">Grade {{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Leave filters empty to apply adjustment to <strong>all employees</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 sticky top-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-purple-700">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-eye mr-2"></i>
                            Preview
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Sample Calculation -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Sample Calculation</p>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Current Salary:</span>
                                    <span class="font-semibold text-gray-900" id="preview_current">₱25,000.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Adjustment:</span>
                                    <span class="font-semibold text-gray-900" id="preview_adjustment">+₱0.00</span>
                                </div>
                                <div class="border-t border-gray-300 pt-2 flex justify-between">
                                    <span class="text-sm font-semibold text-gray-700">New Salary:</span>
                                    <span class="text-lg font-bold text-green-600" id="preview_new">₱25,000.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Estimated Impact -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Estimated Impact</p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-blue-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">Affected Employees</span>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600" id="affected_count">-</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">Est. Monthly Impact</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600" id="monthly_impact">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Warning -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-xs text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                This is an estimate. Actual values may vary.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg font-semibold"
                                style="background: linear-gradient(to right, #059669, #047857); color: white !important;">
                            <i class="fas fa-check mr-2"></i> Apply Bulk Adjustment
                        </button>

                        <button type="button" onclick="window.location.href='{{ route('admin.salaries.index') }}'"
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
function updateAdjustmentLabel() {
    const type = document.getElementById('adjustment_type').value;
    const label = document.getElementById('adjustment_label');
    const unit = document.getElementById('adjustment_unit');
    const input = document.getElementById('adjustment_value');

    if (type === 'percentage') {
        label.textContent = 'Adjustment Percentage';
        unit.textContent = '%';
        input.placeholder = 'e.g., 5 for 5% increase';
    } else {
        label.textContent = 'Adjustment Amount';
        unit.textContent = '₱';
        input.placeholder = 'e.g., 2000 for ₱2,000 increase';
    }

    calculatePreview();
}

function calculatePreview() {
    const type = document.getElementById('adjustment_type').value;
    const value = parseFloat(document.getElementById('adjustment_value').value) || 0;
    const currentSalary = 25000; // Sample salary for preview

    let newSalary, adjustment;

    if (type === 'percentage') {
        adjustment = currentSalary * (value / 100);
        newSalary = currentSalary + adjustment;
    } else {
        adjustment = value;
        newSalary = currentSalary + value;
    }

    document.getElementById('preview_current').textContent = '₱' + currentSalary.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('preview_adjustment').textContent = (adjustment >= 0 ? '+' : '') + '₱' + adjustment.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('preview_new').textContent = '₱' + newSalary.toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function confirmBulkAdjustment() {
    const affectedCount = document.getElementById('affected_count').textContent;

    if (affectedCount === '-') {
        alert('Please wait while we calculate the number of affected employees.');
        return false;
    }

    const type = document.getElementById('adjustment_type').value;
    const value = document.getElementById('adjustment_value').value;
    const reason = document.getElementById('reason').value;

    const typeText = type === 'percentage' ? value + '%' : '₱' + value;

    const message = `Are you sure you want to apply this bulk adjustment?\n\n` +
                    `Adjustment: ${typeText}\n` +
                    `Affected Employees: ${affectedCount}\n` +
                    `Reason: ${reason}\n\n` +
                    `This action cannot be undone.`;

    return confirm(message);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateAdjustmentLabel();

    // Simulate employee count (in production, this would be an AJAX call)
    setTimeout(() => {
        document.getElementById('affected_count').textContent = 'Calculating...';
        // In production: make AJAX call to get actual count based on filters
    }, 500);
});
</script>
@endpush
@endsection
