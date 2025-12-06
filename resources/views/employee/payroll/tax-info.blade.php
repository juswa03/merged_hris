@extends('employee.layouts.app')

@section('title', 'Tax Information')
@section('subtitle', 'Withholding Tax Details & Computation')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Taxable Income -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Taxable Income</p>
                    <p class="text-2xl font-bold text-gray-900">₱ {{ number_format($taxInfo['taxable_income'], 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Current Period</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Withholding Tax -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Withholding Tax</p>
                    <p class="text-2xl font-bold text-red-600">-₱ {{ number_format($taxInfo['withholding_tax'], 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Current Period</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-receipt text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Effective Tax Rate -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Effective Tax Rate</p>
                    <p class="text-2xl font-bold {{ $taxInfo['tax_rate'] <= 10 ? 'text-green-600' : ($taxInfo['tax_rate'] <= 20 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($taxInfo['tax_rate'], 1) }}%
                    </p>
                    <p class="text-xs text-gray-600 mt-1">Tax / Taxable Income</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-percentage text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Computation Breakdown -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-blue-50">
            <h3 class="text-lg font-semibold text-blue-900">Tax Computation Breakdown</h3>
            <p class="text-sm text-blue-700 mt-1">Detailed calculation of your withholding tax</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Income Components -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-900 border-b pb-2">Income Components</h4>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Basic Salary:</span>
                            <span class="font-medium">₱ {{ number_format($taxInfo['income_breakdown']['basic_salary'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Overtime Pay:</span>
                            <span class="font-medium text-green-600">+₱ {{ number_format($taxInfo['income_breakdown']['overtime_pay'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Allowances & Bonuses:</span>
                            <span class="font-medium text-green-600">+₱ {{ number_format($taxInfo['income_breakdown']['allowances_bonuses'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 bg-gray-50 rounded-lg px-3">
                            <span class="text-sm font-medium text-gray-900">Gross Income:</span>
                            <span class="font-bold text-gray-900">
                                ₱ {{ number_format($taxInfo['income_breakdown']['gross_income'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Deductions & Exemptions -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-900 border-b pb-2">Deductions & Exemptions</h4>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">SSS Contribution:</span>
                            <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['deductions']['sss'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">PhilHealth Contribution:</span>
                            <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['deductions']['philhealth'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Pag-IBIG Contribution:</span>
                            <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['deductions']['pagibig'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Personal Exemption:</span>
                            <span class="font-medium text-green-600">+₱ {{ number_format($taxInfo['exemptions']['personal'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 bg-blue-50 rounded-lg px-3">
                            <span class="text-sm font-medium text-blue-900">Net Taxable Income:</span>
                            <span class="font-bold text-blue-900">
                                ₱ {{ number_format($taxInfo['taxable_income'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Bracket Information -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-purple-50">
            <h3 class="text-lg font-semibold text-purple-900">Philippine Tax Bracket Information</h3>
            <p class="text-sm text-purple-700 mt-1">Graduated income tax rates as per BIR regulations</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tax Bracket
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Annual Income Range
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tax Rate
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Base Tax
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Your Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($taxInfo['tax_brackets'] as $bracket)
                    <tr class="hover:bg-gray-50 {{ $bracket['is_current'] ? 'bg-yellow-50 border-l-4 border-yellow-500' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration }}
                            @if($bracket['is_current'])
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                Current
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($bracket['to'])
                                ₱ {{ number_format($bracket['from'], 0) }} - ₱ {{ number_format($bracket['to'], 0) }}
                            @else
                                Over ₱ {{ number_format($bracket['from'], 0) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $bracket['rate'] }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱ {{ number_format($bracket['base_tax'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($bracket['is_current'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Applicable
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Not Applicable
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Two Column Layout for Non-Taxable and YTD Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Non-Taxable Benefits -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50">
                <h3 class="text-lg font-semibold text-green-900">Non-Taxable Benefits</h3>
                <p class="text-sm text-green-700 mt-1">Benefits excluded from taxable income</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($taxInfo['non_taxable_benefits'] as $benefit => $amount)
                    <div class="flex justify-between items-center py-2 border-b">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $benefit }}</span>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($benefit === 'De Minimis Benefits')
                                De minimis benefits up to ₱90,000 annually
                                @elseif($benefit === '13th Month Pay')
                                13th month pay and other benefits up to ₱90,000
                                @else
                                Other non-taxable allowances
                                @endif
                            </p>
                        </div>
                        <span class="text-sm font-medium text-green-600">
                            ₱ {{ number_format($amount, 2) }}
                        </span>
                    </div>
                    @endforeach
                    
                    <div class="bg-green-50 rounded-lg p-4 mt-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-green-600 mr-2"></i>
                            <p class="text-sm text-green-800">
                                Total non-taxable benefits: <strong>₱ {{ number_format(array_sum($taxInfo['non_taxable_benefits']), 2) }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year-to-Date Tax Summary -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-orange-50">
                <h3 class="text-lg font-semibold text-orange-900">Year-to-Date Tax Summary</h3>
                <p class="text-sm text-orange-700 mt-1">Cumulative tax information for {{ date('Y') }}</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-600">Total Income YTD:</span>
                        <span class="font-medium">₱ {{ number_format($taxInfo['ytd_summary']['total_income'], 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-600">Total Tax Paid YTD:</span>
                        <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['ytd_summary']['total_tax_paid'], 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-600">Average Monthly Tax:</span>
                        <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['ytd_summary']['average_monthly_tax'], 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-600">Projected Annual Tax:</span>
                        <span class="font-medium text-red-600">-₱ {{ number_format($taxInfo['ytd_summary']['projected_annual_tax'], 2) }}</span>
                    </div>
                    
                    <div class="bg-orange-50 rounded-lg p-4 mt-4">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-orange-600 mr-2"></i>
                            <p class="text-sm text-orange-800">
                                Based on {{ $taxInfo['ytd_summary']['payroll_periods'] }} payroll periods in {{ date('Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Payment History -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-indigo-50">
            <h3 class="text-lg font-semibold text-indigo-900">Recent Tax Payments</h3>
            <p class="text-sm text-indigo-700 mt-1">Last 6 months withholding tax history</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Period
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Taxable Income
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Withholding Tax
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Effective Rate
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($taxInfo['payment_history'] as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $payment['period'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱ {{ number_format($payment['taxable_income'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                            -₱ {{ number_format($payment['withholding_tax'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($payment['effective_rate'], 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $payment['status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $payment['status'] === 'paid' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                {{ ucfirst($payment['status']) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            <i class="fas fa-receipt text-2xl mb-2 text-gray-300"></i>
                            <p>No tax payment history available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Important Tax Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tax Filing Assistance -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tax Filing Assistance</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <i class="fas fa-file-invoice text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">BIR Form 2316</p>
                        <p class="text-xs text-gray-600">Certificate of Compensation Payment/Tax Withheld</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-calendar-alt text-green-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Annual Filing</p>
                        <p class="text-xs text-gray-600">Due every April 15th of the following year</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-download text-purple-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Digital Copies</p>
                        <p class="text-xs text-gray-600">Download your tax documents anytime</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t">
                <button onclick="downloadTaxDocuments()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium transition duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Download Tax Documents
                </button>
            </div>
        </div>

        <!-- Tax Calculator -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Tax Estimate</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Gross Income</label>
                    <input type="number" id="grossIncome" value="{{ $taxInfo['income_breakdown']['gross_income'] }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Deductions</label>
                    <input type="number" id="monthlyDeductions" 
                           value="{{ $taxInfo['deductions']['sss'] + $taxInfo['deductions']['philhealth'] + $taxInfo['deductions']['pagibig'] }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button onclick="calculateTax()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md text-sm font-medium transition duration-200">
                    <i class="fas fa-calculator mr-2"></i>
                    Calculate Estimated Tax
                </button>
                <div id="taxResult" class="hidden p-3 bg-gray-50 rounded-md">
                    <p class="text-sm font-medium text-gray-900">Estimated Monthly Tax: <span id="estimatedTax" class="text-red-600"></span></p>
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
                <h4 class="text-lg font-semibold text-blue-900">Need Help With Your Taxes?</h4>
                <p class="text-blue-800 mt-1">Contact our HR or Finance Department for tax-related inquiries.</p>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Important Tax Notes -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-yellow-900">Important Tax Information</h4>
                <ul class="text-yellow-800 mt-2 space-y-1 list-disc list-inside text-sm">
                    <li>Tax computations follow the latest BIR graduated tax table (Train Law)</li>
                    <li>Withholding tax is computed monthly but reconciled annually</li>
                    <li>De Minimis benefits and 13th month pay up to ₱90,000 are tax-exempt</li>
                    <li>Personal exemption of ₱50,000 is automatically applied</li>
                    <li>Additional exemptions may apply for qualified dependents</li>
                    <li>For official tax documents, request BIR Form 2316 from HR</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tax information page loaded');
});

function calculateTax() {
    const grossIncome = parseFloat(document.getElementById('grossIncome').value) || 0;
    const monthlyDeductions = parseFloat(document.getElementById('monthlyDeductions').value) || 0;
    
    // Simple tax calculation based on Philippine tax brackets
    const annualTaxable = (grossIncome - monthlyDeductions) * 12;
    let annualTax = 0;
    
    if (annualTaxable <= 250000) {
        annualTax = 0;
    } else if (annualTaxable <= 400000) {
        annualTax = (annualTaxable - 250000) * 0.20;
    } else if (annualTaxable <= 800000) {
        annualTax = 30000 + (annualTaxable - 400000) * 0.25;
    } else if (annualTaxable <= 2000000) {
        annualTax = 130000 + (annualTaxable - 800000) * 0.30;
    } else if (annualTaxable <= 8000000) {
        annualTax = 490000 + (annualTaxable - 2000000) * 0.32;
    } else {
        annualTax = 2410000 + (annualTaxable - 8000000) * 0.35;
    }
    
    const monthlyTax = annualTax / 12;
    
    document.getElementById('estimatedTax').textContent = '₱ ' + monthlyTax.toFixed(2);
    document.getElementById('taxResult').classList.remove('hidden');
}

function downloadTaxDocuments() {
    showToast('Preparing tax documents for download...', 'info');
    // Implement tax document download functionality here
    setTimeout(() => {
        showToast('Tax documents are ready for download!', 'success');
    }, 2000);
}

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

// Print functionality
function printTaxInfo() {
    window.print();
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
    
    button {
        display: none !important;
    }
}
</style>
@endpush