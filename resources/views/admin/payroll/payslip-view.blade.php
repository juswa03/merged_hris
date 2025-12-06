@extends('layouts.app')

@section('title', 'Payslip - ' . $payroll->employee->first_name . ' ' . $payroll->employee->last_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Action Buttons (Hide on Print) -->
    <div class="no-print mb-4 flex justify-between items-center">
        <a href="{{ route('payroll.payslips') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Payslips
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <a href="{{ route('payroll.download-payslip', $payroll->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Payslip Document -->
    <div class="bg-white shadow-lg mx-auto" style="max-width: 800px;" id="payslip-content">
        <!-- Payslip Container -->
        <div class="border-4 border-black p-8">
            <!-- Header -->
            <div class="text-center mb-6 border-b-2 border-black pb-4">
                <h1 class="text-2xl font-bold uppercase">Biliran Province State University</h1>
                <h2 class="text-lg font-semibold mt-2">*** PAYSLIP ***</h2>
            </div>

            <!-- Employee Information Section -->
            <div class="mb-6">
                <table class="w-full border-collapse">
                    <tr class="border-b border-black">
                        <td class="py-2 font-semibold w-1/2">Name of Employee:</td>
                        <td class="py-2 text-right">{{ strtoupper($payroll->employee->last_name) }}, {{ strtoupper($payroll->employee->first_name) }} {{ strtoupper(substr($payroll->employee->middle_name ?? '', 0, 1)) }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td class="py-2 font-semibold">Position:</td>
                        <td class="py-2 text-right">{{ strtoupper($payroll->employee->position->name ?? 'N/A') }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td class="py-2 font-semibold">Salary for the Period of:</td>
                        <td class="py-2 text-right">
                            @if($payroll->payrollPeriod)
                                {{ strtoupper($payroll->payrollPeriod->start_date->format('F j')) }} - {{ $payroll->payrollPeriod->end_date->format('j, Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Earnings Section -->
            <div class="mb-4">
                <table class="w-full border-collapse">
                    <tr class="border-b border-black">
                        <td class="py-2 font-semibold">Basic Monthly Salary:</td>
                        <td class="py-2 text-right">{{ number_format($payroll->basic_salary, 2) }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td class="py-2 font-semibold">Other Compensation:</td>
                        <td class="py-2 text-right">{{ $payroll->overtime_pay > 0 ? number_format($payroll->overtime_pay, 2) : '-' }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td class="py-2 font-semibold">PERA:</td>
                        <td class="py-2 text-right">{{ $payroll->total_allowances > 0 ? number_format($payroll->total_allowances, 2) : '-' }}</td>
                    </tr>
                    <tr class="border-b-2 border-black">
                        <td class="py-2 font-bold italic">Gross Amount Earned:</td>
                        <td class="py-2 text-right font-bold">{{ number_format($payroll->basic_salary + $payroll->overtime_pay + $payroll->total_allowances, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Deductions Section -->
            <div class="mb-4">
                <h3 class="font-bold italic mb-2">Deductions:</h3>
                <table class="w-full border-collapse">
                    @php
                        $grossPay = $payroll->basic_salary + $payroll->overtime_pay + $payroll->total_allowances;

                        // Calculate government contributions (proportional to total deductions)
                        $totalDeductions = $payroll->total_deductions;

                        // Estimate breakdown based on typical government rates
                        $withholdingTax = $totalDeductions * 0.10; // ~10% for withholding tax
                        $lifeRetirement = $totalDeductions * 0.15; // ~15% for GSIS life & retirement
                        $pagibigPremium = min(200, $totalDeductions * 0.01); // Pag-IBIG capped at 200
                        $philhealthPremium = $totalDeductions * 0.04; // ~4% PhilHealth
                        $otherDeductions = $totalDeductions - ($withholdingTax + $lifeRetirement + $pagibigPremium + $philhealthPremium);
                    @endphp

                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Witholding Tax</td>
                        <td class="py-1 text-right">{{ number_format($withholdingTax, 2) }}</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Leave w/out Pay</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Life & Retirement</td>
                        <td class="py-1 text-right">{{ number_format($lifeRetirement, 2) }}</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Pag-ibig Premium</td>
                        <td class="py-1 text-right">{{ number_format($pagibigPremium, 2) }}</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">MP Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">MP2</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">PHIC Prem</td>
                        <td class="py-1 text-right">{{ number_format($philhealthPremium, 2) }}</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Understated PHIC (JAN)</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Consol Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Policy Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Emergency Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">HELP</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">GFAL</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">MP LITE</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">GSIS MP LOAN</td>
                        <td class="py-1 text-right">{{ $otherDeductions > 0 ? number_format($otherDeductions * 0.5, 2) : '-' }}</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Mutual</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">BIPSUFEA/FA Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Disallowance</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">Refund Disallowance</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">CSB Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">CFI Loan</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">BDO Loan</td>
                        <td class="py-1 text-right">{{ $otherDeductions > 0 ? number_format($otherDeductions * 0.5, 2) : '-' }}</td>
                    </tr>
                    <tr class="border-b border-gray-400">
                        <td class="py-1 italic">GSIS CPL</td>
                        <td class="py-1 text-right">-</td>
                    </tr>
                    <tr class="border-b-2 border-black">
                        <td class="py-2 font-bold italic">Total Deductions:</td>
                        <td class="py-2 text-right font-bold">{{ number_format($totalDeductions, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Net Pay -->
            <div class="mb-6">
                <table class="w-full border-collapse">
                    <tr class="border-b-2 border-black bg-gray-100">
                        <td class="py-3 font-bold italic text-lg">NET Amount Received:</td>
                        <td class="py-3 text-right font-bold text-lg">{{ number_format($payroll->net_pay, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Pay Period Breakdown -->
            <div class="mb-6">
                <table class="w-full border-collapse">
                    <tr class="border-b border-black">
                        <td class="py-2">1 to 15 days</td>
                        <td class="py-2 text-right">{{ number_format($payroll->net_pay / 2, 3) }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td class="py-2">16 to 30 days</td>
                        <td class="py-2 text-right">{{ number_format($payroll->net_pay / 2, 3) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Footer / Signature -->
            <div class="mt-8 pt-4 border-t border-black">
                <div class="mb-12">
                    <p class="font-semibold">Prepared by:</p>
                </div>
                <div class="text-center">
                    <p class="font-bold uppercase border-t-2 border-black inline-block px-12 pt-2">
                        {{ auth()->user()->name ?? 'PAYROLL OFFICER' }}
                    </p>
                    <p class="text-sm italic mt-1">Admin Officer III/Payroll Incharge</p>
                </div>
            </div>

            <!-- Generation Date -->
            <div class="text-center text-xs text-gray-500 mt-4">
                <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
                <p class="mt-1">This is a computer-generated payslip.</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #payslip-content, #payslip-content * {
            visibility: visible;
        }
        #payslip-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100% !important;
        }
        .no-print {
            display: none !important;
        }
        @page {
            size: A4;
            margin: 1cm;
        }
    }

    /* Ensure borders print correctly */
    table {
        border-collapse: collapse;
    }

    /* Print-friendly colors */
    @media print {
        .bg-gray-100 {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush
@endsection
