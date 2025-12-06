@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Tax Reports & Calculations</h1>
            <p class="text-slate-600">Manage tax withholding, generate Form 2316, and view tax compliance reports</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('payroll.tax-reports.brackets') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-lg p-3 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Tax Brackets</p>
                        <p class="text-lg font-semibold text-slate-900">BIR 2024/2025</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('payroll.tax-reports.comparison') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-lg p-3 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Tax Comparison</p>
                        <p class="text-lg font-semibold text-slate-900">Employee vs Self</p>
                    </div>
                </div>
            </a>

            <a href="javascript:void(0)" onclick="showCalculator()" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-purple-500 cursor-pointer">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-lg p-3 mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Calculator</p>
                        <p class="text-lg font-semibold text-slate-900">Quick Calc</p>
                    </div>
                </div>
            </a>

            @if($selectedPeriod)
            <a href="{{ route('payroll.tax-reports.export', $selectedPeriod->id) }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="bg-orange-100 rounded-lg p-3 mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Export Report</p>
                        <p class="text-lg font-semibold text-slate-900">CSV/PDF</p>
                    </div>
                </div>
            </a>
            @endif
        </div>

        <!-- Period Selector -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Select Payroll Period</h2>
            <form method="GET" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Payroll Period</label>
                    <select name="period_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select Period --</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}" {{ $selectedPeriod?->id == $period->id ? 'selected' : '' }}>
                                {{ $period->period_display }} ({{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($selectedPeriod && $taxReport)
        <!-- Tax Report Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Employees -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Employees</p>
                        <p class="text-3xl font-bold text-blue-900 mt-2">{{ $taxReport['payroll_count'] }}</p>
                    </div>
                    <div class="bg-blue-200 rounded-full p-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM16 20a3 3 0 01-6 0"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Gross Income -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Total Gross Income</p>
                        <p class="text-3xl font-bold text-green-900 mt-2">₱{{ number_format($taxReport['total_gross_income'], 2) }}</p>
                    </div>
                    <div class="bg-green-200 rounded-full p-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Tax Withheld -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Total Tax Withheld</p>
                        <p class="text-3xl font-bold text-red-900 mt-2">₱{{ number_format($taxReport['total_tax_withheld'], 2) }}</p>
                    </div>
                    <div class="bg-red-200 rounded-full p-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4m0 0l-8 8"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Tax Rate -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Average Tax Rate</p>
                        <p class="text-3xl font-bold text-purple-900 mt-2">{{ number_format($taxReport['average_tax_rate'], 2) }}%</p>
                    </div>
                    <div class="bg-purple-200 rounded-full p-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Distribution Chart (Simple Stats) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Tax Brackets Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Tax Distribution by Bracket</h3>
                <div class="space-y-3">
                    @foreach($taxBrackets as $bracket)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700">{{ $bracket['description'] }}</p>
                            <p class="text-xs text-slate-500">₱{{ number_format($bracket['min'], 2) }} - ₱{{ number_format($bracket['max'] ?? 999999999, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-slate-900">{{ $bracket['rate'] }}%</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tax Compliance Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Tax Compliance Summary</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="text-sm font-medium text-green-900">On-Time Remittance</p>
                            <p class="text-xs text-green-600">Tax properly withheld and ready for remittance</p>
                        </div>
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm font-medium text-slate-900 mb-2">Period: {{ $selectedPeriod->period_display }}</p>
                        <p class="text-xs text-slate-600">{{ $selectedPeriod->start_date->format('F d') }} - {{ $selectedPeriod->end_date->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Employee Tax List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Employee Tax Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Gross Income</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tax Withheld</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Effective Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Net Income</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($taxReport['employees'] as $emp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                <a href="{{ route('payroll.tax-reports.employee-details', $emp['employee_id']) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $emp['employee_name'] }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">₱{{ number_format($emp['gross_income'], 2) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-red-600">₱{{ number_format($emp['tax_withheld'], 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($emp['effective_rate'], 2) }}%</td>
                            <td class="px-6 py-4 text-sm text-green-600 font-semibold">₱{{ number_format($emp['net_income'], 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('payroll.tax-reports.employee-details', $emp['employee_id']) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-slate-600 text-lg">Select a payroll period to view tax reports</p>
        </div>
        @endif
    </div>
</div>

<!-- Tax Calculator Modal -->
<div id="calculatorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <h3 class="text-xl font-bold text-white">Quick Tax Calculator</h3>
        </div>
        <div class="p-6">
            <form id="taxCalculatorForm" onsubmit="calculateTax(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Gross Monthly Income</label>
                    <input type="number" id="grossIncome" step="0.01" placeholder="Enter amount" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Allowances (Optional)</label>
                    <input type="number" id="allowances" step="0.01" placeholder="Non-taxable allowances" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div id="calculatorResult" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <p class="text-sm text-slate-600 mb-2">Withholding Tax:</p>
                    <p class="text-2xl font-bold text-purple-600">₱<span id="taxResult">0.00</span></p>
                    <p class="text-xs text-slate-600 mt-2">Effective Rate: <span id="rateResult">0.00</span>%</p>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-lg font-medium hover:bg-purple-700 transition">Calculate</button>
                    <button type="button" onclick="closeCalculator()" class="flex-1 bg-slate-200 text-slate-900 py-2 rounded-lg font-medium hover:bg-slate-300 transition">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCalculator() {
    document.getElementById('calculatorModal').classList.remove('hidden');
}

function closeCalculator() {
    document.getElementById('calculatorModal').classList.add('hidden');
}

function calculateTax(event) {
    event.preventDefault();
    
    const grossIncome = parseFloat(document.getElementById('grossIncome').value) || 0;
    const allowances = parseFloat(document.getElementById('allowances').value) || 0;
    
    if (grossIncome <= 0) {
        alert('Please enter a valid gross income');
        return;
    }
    
    fetch('{{ route("payroll.tax-reports.calculate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            gross_income: grossIncome,
            allowances: allowances
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('taxResult').textContent = data.withholding_tax.toFixed(2);
        document.getElementById('rateResult').textContent = (data.effective_tax_rate * 100).toFixed(2);
        document.getElementById('calculatorResult').classList.remove('hidden');
    })
    .catch(error => console.error('Error:', error));
}
</script>

@endsection
