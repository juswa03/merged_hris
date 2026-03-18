@extends('admin.layouts.app')

@section('title', 'Generate Payroll')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Payroll Generation"
        description="Generate payroll from DTR data with validation and review"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Panel -->
        <div class="lg:col-span-2">
            <!-- Step 1: Select Period -->
            <x-admin.card title="Step 1: Select Payroll Period" class="mb-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payroll Period</label>
                        <select id="periodSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select a Period --</option>
                            @forelse($periods as $period)
                                <option value="{{ $period->id }}" data-period-id="{{ $period->id }}">
                                    {{ $period->start_date->format('F Y') }} | 
                                    {{ $period->start_date->day <= 15 ? '1st Period' : '2nd Period' }} 
                                    ({{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d') }})
                                </option>
                            @empty
                                <option disabled>No payroll periods available</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button id="validateBtn" onclick="validateDtr()" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-check-circle mr-2"></i> Validate DTR
                        </button>
                    </div>
                </div>
            </x-admin.card>

            <!-- Step 2: Validation Results -->
            <x-admin.card title="Step 2: DTR Validation Results" class="mb-6" id="validationSection" style="display: none;">
                <div class="space-y-4">
                    <div id="validationResults"></div>
                </div>
            </x-admin.card>
        </div>

        <!-- Sidebar: Info Panel -->
        <div>
            <!-- Info Card -->
            <x-admin.card title="Generation Process" class="mb-6">
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">1</div>
                        <div>
                            <p class="font-medium">Select Period</p>
                            <p class="text-xs text-gray-600">Choose the payroll period</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">2</div>
                        <div>
                            <p class="font-medium">Validate DTR</p>
                            <p class="text-xs text-gray-600">Check data completeness</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">3</div>
                        <div>
                            <p class="font-medium">Generate Payroll</p>
                            <p class="text-xs text-gray-600">Create payroll records</p>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Tips Card -->
            <x-admin.card title="Tips" class="bg-blue-50 border border-blue-200">
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                    <li>Ensure all DTR entries are complete</li>
                    <li>Review validation results carefully</li>
                    <li>Salary grades should be assigned</li>
                    <li>Check allowances/deductions are active</li>
                </ul>
            </x-admin.card>
        </div>
    </div>
</div>

@push('scripts')
<script>
const periodSelect = document.getElementById('periodSelect');
const validateBtn = document.getElementById('validateBtn');

// Enable validate button when period is selected
periodSelect.addEventListener('change', function() {
    validateBtn.disabled = !this.value;
});

function validateDtr() {
    const periodId = periodSelect.value;
    if (!periodId) {
        alert('Please select a period');
        return;
    }

    validateBtn.disabled = true;
    validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Validating...';

    fetch(`/payrolls/generation/${periodId}/validate-dtr`)
        .then(response => response.text())
        .then(html => {
            // Extract the validation content from the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const validationContent = doc.querySelector('#validationContent') || doc.body;
            
            document.getElementById('validationResults').innerHTML = validationContent.innerHTML;
            document.getElementById('validationSection').style.display = 'block';
            
            validateBtn.disabled = false;
            validateBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Validate DTR';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error validating DTR: ' + error);
            validateBtn.disabled = false;
            validateBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Validate DTR';
        });
}
</script>
@endpush

@endsection
