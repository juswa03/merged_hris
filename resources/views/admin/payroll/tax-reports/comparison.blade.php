@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('payroll.tax-reports.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">← Back to Tax Reports</a>
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Tax Liability Comparison</h1>
            <p class="text-slate-600">Compare tax obligations between regular employees and self-employed individuals</p>
        </div>

        <!-- Income Input Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Calculate Tax Comparison</h2>
            <form method="GET" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Monthly Gross Income</label>
                    <input type="number" name="gross_amount" step="0.01" value="{{ $monthlyGross }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter amount">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                    Calculate
                </button>
            </form>
        </div>

        @if($comparison)
        <!-- Comparison Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Employee Tax Calculation -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-lg overflow-hidden border-2 border-blue-200">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Regular Employee</h3>
                    <p class="text-blue-100 text-sm">Withholding Tax (Employee Income Tax)</p>
                </div>
                
                <div class="p-6">
                    <div class="mb-6 pb-6 border-b border-blue-200">
                        <p class="text-sm text-blue-600 font-medium mb-2">Gross Monthly Income</p>
                        <p class="text-3xl font-bold text-blue-900">₱{{ number_format($monthlyGross, 2) }}</p>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600">SSS Contribution (12%)</span>
                                <span class="font-semibold text-slate-900">-₱{{ number_format($comparison['employee']['sss'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Both employee and employer contribute</div>
                        </div>

                        <div class="bg-white rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600">PhilHealth (3.6%)</span>
                                <span class="font-semibold text-slate-900">-₱{{ number_format($comparison['employee']['philhealth'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Employee contribution only</div>
                        </div>

                        <div class="bg-white rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600">Pag-IBIG Fund (2%)</span>
                                <span class="font-semibold text-slate-900">-₱{{ number_format($comparison['employee']['pagibig'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Contribution to home development fund</div>
                        </div>

                        <div class="bg-white rounded-lg p-4 border-2 border-red-300">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600 font-medium">Income Tax Withholding (BIR)</span>
                                <span class="font-bold text-red-600">-₱{{ number_format($comparison['employee']['income_tax'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Based on progressive tax brackets</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-300">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-slate-900">Net Takehome Pay</span>
                            <span class="text-2xl font-bold text-green-600">₱{{ number_format($comparison['employee']['net_pay'] ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-50 rounded-lg text-sm">
                        <p class="text-blue-900"><strong>Effective Tax Rate:</strong> {{ number_format($comparison['employee']['effective_tax_rate'] ?? 0, 2) }}%</p>
                        <p class="text-blue-900 mt-2"><strong>Total Deductions:</strong> ₱{{ number_format(($monthlyGross - ($comparison['employee']['net_pay'] ?? 0)), 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Self-Employed Tax Calculation -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-lg overflow-hidden border-2 border-purple-200">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Self-Employed / Freelancer</h3>
                    <p class="text-purple-100 text-sm">Self-Employment Tax (Optimized)</p>
                </div>

                <div class="p-6">
                    <div class="mb-6 pb-6 border-b border-purple-200">
                        <p class="text-sm text-purple-600 font-medium mb-2">Gross Monthly Income</p>
                        <p class="text-3xl font-bold text-purple-900">₱{{ number_format($monthlyGross, 2) }}</p>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600">SSS Self-Employed (14%)</span>
                                <span class="font-semibold text-slate-900">-₱{{ number_format($comparison['self_employed']['sss'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Higher self-employed SSS premium</div>
                        </div>

                        <div class="bg-white rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600">PhilHealth (5.5%)</span>
                                <span class="font-semibold text-slate-900">-₱{{ number_format($comparison['self_employed']['philhealth'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Full self-employed contribution</div>
                        </div>

                        <div class="bg-white rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600">Pag-IBIG Self-Employed (3%)</span>
                                <span class="font-semibold text-slate-900">-₱{{ number_format($comparison['self_employed']['pagibig'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Higher contribution rate for self-employed</div>
                        </div>

                        <div class="bg-white rounded-lg p-4 border-2 border-red-300">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600 font-medium">Income Tax (BIR)</span>
                                <span class="font-bold text-red-600">-₱{{ number_format($comparison['self_employed']['income_tax'] ?? 0, 2) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Based on progressive tax brackets</div>
                        </div>

                        <div class="bg-white rounded-lg p-4 bg-yellow-50 border border-yellow-300">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-600 font-medium">Business Expenses (Optional)</span>
                                <span class="font-semibold text-slate-900">-₱0.00</span>
                            </div>
                            <div class="text-xs text-slate-500">May deduct legitimate business expenses</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-300">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-slate-900">Net Income</span>
                            <span class="text-2xl font-bold text-green-600">₱{{ number_format($comparison['self_employed']['net_income'] ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-purple-50 rounded-lg text-sm">
                        <p class="text-purple-900"><strong>Effective Tax Rate:</strong> {{ number_format($comparison['self_employed']['effective_tax_rate'] ?? 0, 2) }}%</p>
                        <p class="text-purple-900 mt-2"><strong>Total Contributions:</strong> ₱{{ number_format(($monthlyGross - ($comparison['self_employed']['net_income'] ?? 0)), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Summary -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Financial Comparison Summary</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tax Difference -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 border-l-4 border-red-500">
                    <p class="text-sm text-red-600 font-medium mb-2">Total Tax Difference</p>
                    <p class="text-3xl font-bold text-red-900">₱{{ number_format(abs(($comparison['self_employed']['income_tax'] ?? 0) - ($comparison['employee']['income_tax'] ?? 0)), 2) }}</p>
                    <p class="text-xs text-red-600 mt-2">
                        @if(($comparison['self_employed']['income_tax'] ?? 0) > ($comparison['employee']['income_tax'] ?? 0))
                            Self-employed pays more
                        @else
                            Employee pays more
                        @endif
                    </p>
                </div>

                <!-- Net Income Difference -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm text-green-600 font-medium mb-2">Net Income Difference</p>
                    <p class="text-3xl font-bold text-green-900">₱{{ number_format(abs(($comparison['self_employed']['net_income'] ?? 0) - ($comparison['employee']['net_pay'] ?? 0)), 2) }}</p>
                    <p class="text-xs text-green-600 mt-2">
                        @if(($comparison['self_employed']['net_income'] ?? 0) > ($comparison['employee']['net_pay'] ?? 0))
                            Self-employed takes more home
                        @else
                            Employee takes more home
                        @endif
                    </p>
                </div>

                <!-- Total Contribution/Deduction Difference -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-blue-600 font-medium mb-2">Total Contribution Difference</p>
                    <p class="text-3xl font-bold text-blue-900">₱{{ number_format(abs((($monthlyGross - ($comparison['self_employed']['net_income'] ?? 0))) - (($monthlyGross - ($comparison['employee']['net_pay'] ?? 0)))), 2) }}</p>
                    <p class="text-xs text-blue-600 mt-2">Higher government contributions for self-employed</p>
                </div>
            </div>
        </div>

        <!-- Key Insights -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-blue-900 mb-3">Employee Benefits</h4>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Employer contributes to SSS (matching funds)
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Lower overall tax burden
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        SSS benefits and retirement security
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Job security and employee protections
                    </li>
                </ul>
            </div>

            <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-purple-900 mb-3">Self-Employed Benefits</h4>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Can deduct legitimate business expenses
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Flexible work schedule and independence
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Potential for higher income growth
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Multiple income stream opportunities
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
