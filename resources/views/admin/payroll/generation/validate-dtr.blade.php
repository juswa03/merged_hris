@extends('layouts.app')

@section('title', 'Validate DTR - Payroll Generation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="DTR Validation"
        description="Review DTR data before generating payroll for {{ $period->period_name }}"
    />

    <div id="validationContent" class="space-y-6">
        <!-- Validation Summary -->
        <x-admin.card title="Validation Summary">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $validation['total_employees'] ?? 0 }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="text-sm text-gray-600">Complete DTR</p>
                    <p class="text-2xl font-bold text-green-600">{{ $validation['complete_count'] ?? 0 }}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-sm text-gray-600">Incomplete</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $validation['incomplete_count'] ?? 0 }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <p class="text-sm text-gray-600">Missing</p>
                    <p class="text-2xl font-bold text-red-600">{{ $validation['missing_count'] ?? 0 }}</p>
                </div>
            </div>
        </x-admin.card>

        <!-- Validation Status -->
        @if(($validation['missing_count'] ?? 0) > 0 || ($validation['incomplete_count'] ?? 0) > 0)
            <x-admin.alert type="warning">
                <strong>⚠️ Warning:</strong> There are incomplete or missing DTR entries. Please review and fix before proceeding with payroll generation.
            </x-admin.alert>
        @else
            <x-admin.alert type="success">
                <strong>✓ All Clear:</strong> All DTR entries are complete and ready for payroll generation.
            </x-admin.alert>
        @endif

        <!-- DTR Summary Table -->
        @if(isset($summary['employees_summary']) && count($summary['employees_summary']) > 0)
            <x-admin.card title="Employee DTR Status">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="text-left py-2 px-3">Employee Name</th>
                                <th class="text-center py-2 px-3">Total Days</th>
                                <th class="text-center py-2 px-3">Present</th>
                                <th class="text-center py-2 px-3">Absent</th>
                                <th class="text-center py-2 px-3">Undertime (mins)</th>
                                <th class="text-center py-2 px-3">Status</th>
                                <th class="text-center py-2 px-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary['employees_summary'] as $emp)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <span class="font-medium">{{ $emp['name'] ?? 'Unknown' }}</span>
                                    </td>
                                    <td class="text-center py-2 px-3">{{ $emp['total_days'] ?? 0 }}</td>
                                    <td class="text-center py-2 px-3">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                            {{ $emp['present_days'] ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center py-2 px-3">
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                            {{ $emp['absent_days'] ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center py-2 px-3">
                                        <span class="{{ ($emp['undertime_minutes'] ?? 0) > 0 ? 'text-orange-600 font-bold' : 'text-gray-600' }}">
                                            {{ $emp['undertime_minutes'] ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center py-2 px-3">
                                        @if(($emp['status'] ?? 'complete') === 'complete')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                                <i class="fas fa-check mr-1"></i> Complete
                                            </span>
                                        @else
                                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium">
                                                <i class="fas fa-exclamation mr-1"></i> Incomplete
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center py-2 px-3">
                                        @if(($emp['status'] ?? 'complete') !== 'complete')
                                            <span class="text-orange-600 text-xs font-medium">Incomplete DTR</span>
                                        @elseif(($emp['undertime_minutes'] ?? 0) > 0)
                                            <span class="text-orange-600 text-xs font-medium">With Undertime</span>
                                        @else
                                            <span class="text-green-600 text-xs font-medium">Complete</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <a href="{{ route('payroll.generation.index') }}" class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition text-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            @if(($validation['missing_count'] ?? 0) === 0 && ($validation['incomplete_count'] ?? 0) === 0)
                <form method="POST" action="{{ route('payroll.generation.generate', $period->id) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-calculator mr-2"></i> Generate Payroll
                    </button>
                </form>
            @else
                <button disabled class="flex-1 bg-gray-400 text-white py-2 px-4 rounded-lg cursor-not-allowed text-center">
                    <i class="fas fa-lock mr-2"></i> Fix Issues First
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
