@extends('layouts.app')

@section('title', 'Bulk Payment Export')

@section('content')
<div class="w-full px-6 py-6 max-w-4xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Bulk Payment Export"
        description="Generate payment files for bank transfers"
    >
        <x-slot name="actions">
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Payroll
            </a>
        </x-slot>
    </x-admin.page-header>

    <!-- Summary Card -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-green-100 text-sm">Payroll Period</p>
                <p class="text-2xl font-bold">{{ $period->period_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-green-100 text-sm">Total Employees</p>
                <p class="text-2xl font-bold">{{ $summary['total_employees'] }}</p>
            </div>
            <div>
                <p class="text-green-100 text-sm">Total Amount</p>
                <p class="text-2xl font-bold">₱{{ number_format($summary['total_amount'], 2) }}</p>
            </div>
            <div>
                <p class="text-green-100 text-sm">Pay Date</p>
                <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($summary['pay_date'])->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Export Format Selection -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Standard Formats -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-file-csv text-blue-600"></i> Standard Formats
            </h3>

            <form action="{{ route('payroll.generate-bulk-payment', $period) }}" method="POST" class="space-y-3">
                @csrf

                <!-- CSV Format -->
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition cursor-pointer">
                    <label class="flex items-start gap-3">
                        <input type="radio" name="export_format" value="csv" class="mt-1" checked>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">CSV Format</p>
                            <p class="text-sm text-gray-600">Standard Excel/Spreadsheet format. Compatible with most banking systems.</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-check text-green-600 mr-1"></i> Universal compatibility
                                <i class="fas fa-check text-green-600 mr-1"></i> Easy to review
                            </p>
                        </div>
                    </label>
                </div>

                <!-- ACH Format -->
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition cursor-pointer">
                    <label class="flex items-start gap-3">
                        <input type="radio" name="export_format" value="ach">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">ACH Format (Automated Clearing House)</p>
                            <p class="text-sm text-gray-600">Standard format for Philippine bank batch transfers.</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-check text-green-600 mr-1"></i> Bank standard
                                <i class="fas fa-check text-green-600 mr-1"></i> Fast processing
                            </p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i> Download Standard Format
                </button>
            </form>
        </div>

        <!-- Bank-Specific Formats -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-bank text-green-600"></i> Bank-Specific Formats
            </h3>

            <form action="{{ route('payroll.generate-bulk-payment', $period) }}" method="POST" class="space-y-3">
                @csrf

                <div class="border border-gray-200 rounded-lg p-3 hover:border-green-500 hover:bg-green-50 transition cursor-pointer">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="export_format" value="bpi">
                        <span class="font-semibold text-gray-900">BPI Batch Transfer</span>
                    </label>
                </div>

                <div class="border border-gray-200 rounded-lg p-3 hover:border-green-500 hover:bg-green-50 transition cursor-pointer">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="export_format" value="bdo">
                        <span class="font-semibold text-gray-900">BDO e-Pay Corporate</span>
                    </label>
                </div>

                <div class="border border-gray-200 rounded-lg p-3 hover:border-green-500 hover:bg-green-50 transition cursor-pointer">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="export_format" value="pnb">
                        <span class="font-semibold text-gray-900">PNB Online Payroll</span>
                    </label>
                </div>

                <div class="border border-gray-200 rounded-lg p-3 hover:border-green-500 hover:bg-green-50 transition cursor-pointer">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="export_format" value="metrobank">
                        <span class="font-semibold text-gray-900">Metrobank Corporate Payroll</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i> Download Bank Format
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Summary</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-gray-600">Total Employees</p>
                <p class="text-3xl font-bold text-blue-600">{{ $summary['total_employees'] }}</p>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-gray-600">Total Payroll Amount</p>
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($summary['total_amount'], 2) }}</p>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <p class="text-sm text-gray-600">Average Pay</p>
                <p class="text-2xl font-bold text-purple-600">₱{{ number_format($summary['average_pay'], 2) }}</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                <strong>Important:</strong> Ensure all payroll records are marked as "Processed" before exporting. 
                Files generated on {{ $summary['generated_at'] }}
            </p>
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-6">
        <h4 class="font-semibold text-yellow-900 mb-3 flex items-center gap-2">
            <i class="fas fa-lightbulb"></i> How to Use
        </h4>
        <ul class="text-sm text-yellow-800 space-y-2 list-disc list-inside">
            <li>Select the format compatible with your bank's payment system</li>
            <li>Download the generated file</li>
            <li>Upload to your bank's corporate portal</li>
            <li>Review and approve the batch transfer</li>
            <li>Submit for processing</li>
            <li>Update payroll status to "Paid" in the system after confirmation</li>
        </ul>
    </div>
</div>

@endsection
