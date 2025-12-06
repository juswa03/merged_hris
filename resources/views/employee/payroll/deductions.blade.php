@extends('employee.layouts.app')

@section('title', 'Deductions & Benefits')
@section('subtitle', 'Salary Components & Contributions Breakdown')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Deductions -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Deductions</p>
                    <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($totalMonthlyDeductions, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Current Month</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-hand-holding-usd text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Benefits -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Benefits</p>
                    <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($totalMonthlyBenefits, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Allowances & Perks</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-gift text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Net Impact -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Net Impact</p>
                    <p class="text-2xl font-bold {{ $netImpact >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netImpact >= 0 ? '+' : '' }}₱ {{ number_format(abs($netImpact), 2) }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">Benefits - Deductions</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-scale-balanced text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Government Contributions -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Government Mandated Contributions</h3>
            <p class="text-sm text-gray-600 mt-1">Monthly deductions as required by law</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contribution Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee Share
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employer Share
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($governmentContributions as $contribution)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 mr-3">
                                    <i class="fas fa-landmark text-blue-600 text-sm"></i>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $contribution->name }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $contribution->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                            -₱ {{ number_format($contribution->employee_share, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                            +₱ {{ number_format($contribution->employer_share, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱ {{ number_format($contribution->employee_share + $contribution->employer_share, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Active
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            <i class="fas fa-landmark text-2xl mb-2 text-gray-300"></i>
                            <p>No government contributions found</p>
                        </td>
                    </tr>
                    @endforelse
                    
                    @if($governmentContributions->isNotEmpty())
                    <!-- Total Row -->
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="2" class="px-6 py-4 text-sm text-gray-900 text-right">
                            Total Government Contributions:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                            -₱ {{ number_format($governmentContributions->sum('employee_share'), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                            +₱ {{ number_format($governmentContributions->sum('employer_share'), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱ {{ number_format($governmentContributions->sum('employee_share') + $governmentContributions->sum('employer_share'), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Two Column Layout for Other Deductions and Benefits -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Other Deductions -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-red-50">
                <h3 class="text-lg font-semibold text-red-900">Other Deductions</h3>
                <p class="text-sm text-red-700 mt-1">Additional salary deductions</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deduction Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Frequency
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($otherDeductions as $deduction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-red-100 mr-3">
                                        <i class="fas fa-minus-circle text-red-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $deduction->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $deduction->description }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                -₱ {{ number_format($deduction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($deduction->frequency) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $deduction->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $deduction->status === 'active' ? 'fa-check-circle' : 'fa-pause-circle' }} mr-1"></i>
                                    {{ ucfirst($deduction->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                <i class="fas fa-minus-circle text-2xl mb-2 text-gray-300"></i>
                                <p>No other deductions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Benefits & Allowances -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50">
                <h3 class="text-lg font-semibold text-green-900">Benefits & Allowances</h3>
                <p class="text-sm text-green-700 mt-1">Additional compensation & perks</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Benefit Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Frequency
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($benefits as $benefit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-green-100 mr-3">
                                        <i class="fas fa-plus-circle text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $benefit->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $benefit->description }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                                +₱ {{ number_format($benefit->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucfirst($benefit->frequency) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $benefit->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $benefit->status === 'active' ? 'fa-check-circle' : 'fa-pause-circle' }} mr-1"></i>
                                    {{ ucfirst($benefit->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                <i class="fas fa-gift text-2xl mb-2 text-gray-300"></i>
                                <p>No benefits found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tax Information -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-yellow-50">
            <h3 class="text-lg font-semibold text-yellow-900">Tax Information</h3>
            <p class="text-sm text-yellow-700 mt-1">Withholding tax details</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Tax Computation</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Taxable Income:</span>
                            <span class="font-medium">₱ {{ number_format($taxInfo['taxable_income'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Withholding Tax:</span>
                            <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['withholding_tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Effective Tax Rate:</span>
                            <span class="font-medium">{{ $taxInfo['tax_rate'] }}%</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Tax Benefits</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Personal Exemption:</span>
                            <span class="font-medium text-green-600">+₱ {{ number_format($taxInfo['personal_exemption'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Additional Exemptions:</span>
                            <span class="font-medium text-green-600">+₱ {{ number_format($taxInfo['additional_exemption'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 bg-blue-50 rounded-lg px-3">
                            <span class="text-sm font-medium text-blue-900">Net Taxable Income:</span>
                            <span class="font-bold text-blue-900">
                                ₱ {{ number_format(max(0, $taxInfo['taxable_income'] - $taxInfo['personal_exemption'] - $taxInfo['additional_exemption']), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Contribution Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contribution Summary</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Employee Contributions:</span>
                    <span class="font-medium text-red-600">-₱ {{ number_format($governmentContributions->sum('employee_share') + $otherDeductions->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Employer Contributions:</span>
                    <span class="font-medium text-green-600">+₱ {{ number_format($governmentContributions->sum('employer_share'), 2) }}</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between items-center font-semibold">
                        <span class="text-gray-900">Net Contribution Impact:</span>
                        <span class="{{ ($governmentContributions->sum('employer_share') - ($governmentContributions->sum('employee_share') + $otherDeductions->sum('amount'))) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($governmentContributions->sum('employer_share') - ($governmentContributions->sum('employee_share') + $otherDeductions->sum('amount'))) >= 0 ? '+' : '' }}₱ 
                            {{ number_format(abs($governmentContributions->sum('employer_share') - ($governmentContributions->sum('employee_share') + $otherDeductions->sum('amount'))), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year-to-Date Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-to-Date Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Total Benefits Received:</span>
                    <span class="font-medium text-green-600">+₱ {{ number_format($totalMonthlyBenefits * 12, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Total Deductions Paid:</span>
                    <span class="font-medium text-red-600">-₱ {{ number_format($totalMonthlyDeductions * 12, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Net Benefits YTD:</span>
                    <span class="font-medium {{ $netImpact * 12 >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netImpact * 12 >= 0 ? '+' : '' }}₱ {{ number_format(abs($netImpact * 12), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-question-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-blue-900">Need Help Understanding Your Deductions?</h4>
                <p class="text-blue-800 mt-1">Contact the HR or Finance Department for any questions about your salary components.</p>
                <div class="mt-3 flex flex-wrap gap-4">
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-phone mr-2"></i>
                        <span>HR Department: (053) 123-4567</span>
                    </div>
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>hr@bipsu.edu.ph</span>
                    </div>
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Mon-Fri, 8:00 AM - 5:00 PM</span>
                    </div>
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>HR Office, Administration Building</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-yellow-900">Important Information</h4>
                <ul class="text-yellow-800 mt-2 space-y-1 list-disc list-inside text-sm">
                    <li>All amounts shown are monthly unless specified otherwise</li>
                    <li>Government contributions are computed based on current BIR, SSS, PhilHealth, and Pag-IBIG rates</li>
                    <li>Annual allowances are divided equally across 12 months for display purposes</li>
                    <li>Tax computations follow the latest BIR graduated tax table</li>
                    <li>For official documents, please request a certified statement from HR</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// You can add interactive features here if needed
document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive functionality here
    console.log('Deductions page loaded');
});

// Print functionality
function printDeductions() {
    window.print();
}

// Export functionality
function exportDeductions() {
    showToast('Preparing export...', 'info');
    // Implement export functionality here
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
    
    .shadow-sm {
        box-shadow: none !important;
    }
}
</style>
@endpush