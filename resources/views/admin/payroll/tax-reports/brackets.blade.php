@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('payroll.tax-reports.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">← Back to Tax Reports</a>
            <h1 class="text-4xl font-bold text-slate-900 mb-2">BIR Tax Brackets 2024/2025</h1>
            <p class="text-slate-600">Philippine Bureau of Internal Revenue (BIR) Income Tax Brackets for Individual Employees</p>
        </div>

        <!-- Information Alert -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 000 2h6a1 1 0 100-2H8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-900">Tax Calculation Method</h3>
                    <p class="text-sm text-blue-700 mt-2">This system uses <strong>progressive tax brackets</strong>, meaning different portions of income are taxed at different rates. The tax rate shown below applies only to income within that bracket range.</p>
                </div>
            </div>
        </div>

        <!-- Main Tax Brackets Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-700 to-slate-800">
                <h2 class="text-xl font-semibold text-white">Individual Income Tax Brackets</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Bracket Level</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Income Range (Monthly)</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-slate-900">Tax Rate</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Calculation Formula</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-slate-900">Example</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <!-- Bracket 1: Tax-Free -->
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Tier 1</span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                ₱0 to ₱20,833
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 font-bold rounded">0%</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                No tax (Tax-exempt threshold)
                            </td>
                            <td class="px-6 py-4 text-right text-green-600 font-semibold">
                                ₱0
                            </td>
                        </tr>

                        <!-- Bracket 2: 5% -->
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Tier 2</span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                ₱20,833.01 to ₱33,333
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 font-bold rounded">5%</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                (Income - ₱20,833) × 5%
                            </td>
                            <td class="px-6 py-4 text-right text-yellow-600 font-semibold">
                                (₱30,000 - ₱20,833) × 5% = ₱458.35
                            </td>
                        </tr>

                        <!-- Bracket 3: 10% -->
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-semibold">Tier 3</span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                ₱33,333.01 to ₱66,667
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-3 py-1 bg-orange-100 text-orange-800 font-bold rounded">10%</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                ₱625 + (Income - ₱33,333) × 10%
                            </td>
                            <td class="px-6 py-4 text-right text-orange-600 font-semibold">
                                ₱625 + (₱50,000 - ₱33,333) × 10% = ₱2,291.70
                            </td>
                        </tr>

                        <!-- Bracket 4: 15% -->
                        <tr class="hover:bg-red-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Tier 4</span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                ₱66,667.01 to ₱166,667
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 font-bold rounded">15%</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                ₱3,958.40 + (Income - ₱66,667) × 15%
                            </td>
                            <td class="px-6 py-4 text-right text-red-600 font-semibold">
                                ₱3,958.40 + (₱100,000 - ₱66,667) × 15% = ₱8,958.55
                            </td>
                        </tr>

                        <!-- Bracket 5: 20% -->
                        <tr class="hover:bg-purple-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">Tier 5</span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                ₱166,667.01 to ₱666,667
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 font-bold rounded">20%</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                ₱18,958.40 + (Income - ₱166,667) × 20%
                            </td>
                            <td class="px-6 py-4 text-right text-purple-600 font-semibold">
                                ₱18,958.40 + (₱300,000 - ₱166,667) × 20% = ₱46,625.07
                            </td>
                        </tr>

                        <!-- Bracket 6: 25% -->
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">Tier 6</span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                ₱666,667.01 and above
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 font-bold rounded">25%</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                ₱118,958.40 + (Income - ₱666,667) × 25%
                            </td>
                            <td class="px-6 py-4 text-right text-indigo-600 font-semibold">
                                ₱118,958.40 + (₱1M - ₱666,667) × 25% = ₱202,125.73
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Example Calculations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Example 1: Below threshold -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Example 1: Below Tax Threshold</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Monthly Gross Income:</span>
                        <span class="font-semibold text-slate-900">₱18,000</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Falls in Bracket:</span>
                        <span class="font-semibold text-green-600">Tier 1 (Tax-Free)</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between">
                        <span class="text-slate-600 font-medium">Income Tax Withheld:</span>
                        <span class="font-bold text-green-600">₱0.00</span>
                    </div>
                </div>
            </div>

            <!-- Example 2: Multiple brackets -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Example 2: Multi-Bracket Calculation</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Monthly Gross Income:</span>
                        <span class="font-semibold text-slate-900">₱60,000</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded">
                        <p class="text-slate-600 mb-2">Breakdown by bracket:</p>
                        <ul class="space-y-1 text-xs">
                            <li>• ₱20,833 @ 0% = ₱0</li>
                            <li>• ₱12,500 @ 5% = ₱625</li>
                            <li>• ₱26,667 @ 10% = ₱2,666.70</li>
                        </ul>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between">
                        <span class="text-slate-600 font-medium">Income Tax Withheld:</span>
                        <span class="font-bold text-blue-600">₱3,291.70</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Effective Tax Rate Information -->
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Understanding Effective Tax Rate</h3>
            <div class="space-y-4 text-slate-600">
                <p>
                    <strong>Effective Tax Rate</strong> is the average percentage of tax you pay on your total income. It's always lower than the marginal rate (highest bracket) because only the income within each bracket is taxed at that rate.
                </p>
                <p>
                    <strong>Formula:</strong> Effective Tax Rate = (Total Income Tax / Total Income) × 100
                </p>
                <p>
                    <strong>Example:</strong> If you earn ₱60,000 and owe ₱3,291.70 in taxes, your effective rate is (3,291.70 / 60,000) × 100 = <span class="font-bold">5.49%</span>, even though your income spans brackets up to 10%.
                </p>
            </div>
        </div>

        <!-- Quick Calculator -->
        <div class="mt-8 flex justify-center">
            <a href="{{ route('payroll.tax-reports.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                Back to Tax Reports
            </a>
        </div>
    </div>
</div>
@endsection
