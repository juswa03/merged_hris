@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('payroll.tax-reports.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">← Back to Tax Reports</a>
                <h1 class="text-4xl font-bold text-slate-900">{{ $employee->full_name }}'s Tax Details</h1>
                <p class="text-slate-600 mt-2">Employee ID: {{ $employee->employee_id }} | Year: {{ $year }}</p>
            </div>
            <a href="{{ route('payroll.tax-reports.download-form-2316', ['employee' => $employee->id, 'year' => $year]) }}" class="bg-red-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-red-700 transition">
                Download Form 2316
            </a>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-sm text-blue-600 font-medium">Total Payrolls</p>
                <p class="text-3xl font-bold text-blue-900 mt-2">{{ $payrolls->count() }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-sm text-green-600 font-medium">Total Gross Income</p>
                <p class="text-3xl font-bold text-green-900 mt-2">₱{{ number_format($payrolls->sum('gross_pay'), 2) }}</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow p-6 border-l-4 border-red-500">
                <p class="text-sm text-red-600 font-medium">Total Tax Withheld</p>
                <p class="text-3xl font-bold text-red-900 mt-2">₱{{ number_format($payrolls->sum('withholding_tax'), 2) }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-sm text-purple-600 font-medium">Effective Tax Rate</p>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $payrolls->sum('gross_pay') > 0 ? number_format(($payrolls->sum('withholding_tax') / $payrolls->sum('gross_pay')) * 100, 2) : 0 }}%</p>
            </div>
        </div>

        <!-- Form 2316 Preview -->
        @if($form2316)
        <div class="bg-white rounded-lg shadow p-6 mb-8 border-l-4 border-amber-500">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-slate-900">Form 2316 - Certificate of Creditable Tax Withheld</h2>
                <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-medium">BIR Official Form</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Taxpayer Name</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $form2316['employee_name'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">TIN</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $form2316['tin'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Tax Year</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $form2316['period'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Employer Name</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $form2316['employer_name'] ?? config('app.name') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Total Compensation Income</p>
                    <p class="text-lg font-semibold text-slate-900">₱{{ number_format($form2316['total_compensation'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Total Income Tax Withheld</p>
                    <p class="text-lg font-semibold text-red-600">₱{{ number_format($form2316['total_tax_withheld'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Quarter</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $form2316['quarter'] ?? 'Full Year' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Date Issued</p>
                    <p class="text-lg font-semibold text-slate-900">{{ now()->format('F d, Y') }}</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200">
                <a href="{{ route('payroll.tax-reports.download-form-2316', ['employee' => $employee->id, 'year' => $year]) }}" class="inline-block bg-amber-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-amber-700 transition">
                    Download Form 2316 (PDF)
                </a>
            </div>
        </div>
        @endif

        <!-- Year-End Projection -->
        @if($projection)
        <div class="bg-white rounded-lg shadow p-6 mb-8 border-l-4 border-indigo-500">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Year-End Tax Projection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-indigo-50 rounded-lg">
                    <p class="text-sm text-indigo-600 font-medium">YTD Projection</p>
                    <p class="text-2xl font-bold text-indigo-900 mt-2">₱{{ number_format($projection['ytd_tax_projected'] ?? 0, 2) }}</p>
                    <p class="text-xs text-indigo-600 mt-1">Based on current run rate</p>
                </div>

                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-sm text-slate-600 font-medium">Months Paid</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">{{ $payrolls->count() }} of 12</p>
                    <p class="text-xs text-slate-600 mt-1">{{ $payrolls->count() }} payroll(s) processed</p>
                </div>

                <div class="p-4 bg-emerald-50 rounded-lg">
                    <p class="text-sm text-emerald-600 font-medium">Annualized Rate</p>
                    <p class="text-2xl font-bold text-emerald-900 mt-2">{{ number_format(($projection['ytd_tax_projected'] ?? 0) / max(1, $payrolls->count()), 2) }}</p>
                    <p class="text-xs text-emerald-600 mt-1">Per payroll period</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Payroll History -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Payroll History - {{ $year }}</h3>
            </div>

            @if($payrolls->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-slate-600 text-lg">No payrolls found for this employee in {{ $year }}</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Gross Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tax Withheld</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Effective Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">GSIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">PhilHealth</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pag-IBIG</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Net Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($payrolls as $payroll)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                {{ $payroll->payrollPeriod->period_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-red-600">₱{{ number_format($payroll->withholding_tax, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $payroll->gross_pay > 0 ? number_format(($payroll->withholding_tax / $payroll->gross_pay) * 100, 2) : 0 }}%
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">₱{{ number_format($payroll->gsis_contribution ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">₱{{ number_format($payroll->philhealth_contribution ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">₱{{ number_format($payroll->pagibig_contribution ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600">₱{{ number_format($payroll->net_pay, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('payroll.show', $payroll->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-slate-600">No payroll records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-semibold">
                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-900">TOTAL</td>
                            <td class="px-6 py-4 text-sm text-slate-900">₱{{ number_format($payrolls->sum('gross_pay'), 2) }}</td>
                            <td class="px-6 py-4 text-sm text-red-600">₱{{ number_format($payrolls->sum('withholding_tax'), 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">
                                {{ $payrolls->sum('gross_pay') > 0 ? number_format(($payrolls->sum('withholding_tax') / $payrolls->sum('gross_pay')) * 100, 2) : 0 }}%
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-900">₱{{ number_format($payrolls->sum('gsis_contribution'), 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">₱{{ number_format($payrolls->sum('philhealth_contribution'), 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">₱{{ number_format($payrolls->sum('pagibig_contribution'), 2) }}</td>
                            <td class="px-6 py-4 text-sm text-green-600">₱{{ number_format($payrolls->sum('net_pay'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
