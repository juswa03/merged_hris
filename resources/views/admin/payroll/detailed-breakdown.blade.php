@extends('admin.layouts.app')

@section('title', 'Payroll Detailed Breakdown with DTR')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.payroll.index') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Payroll Management
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Detailed Breakdown</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payroll Breakdown with DTR Integration</h1>
            <p class="mt-2 text-sm text-gray-600">Complete breakdown showing how DTR affects payroll calculations</p>
        </div>
        <a href="{{ route('admin.payroll.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Employee Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-6 text-center">
                    <img class="h-20 w-20 rounded-full mx-auto border-4 border-white shadow-lg"
                         src="{{ $payroll->employee->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($payroll->employee->first_name . ' ' . $payroll->employee->last_name) }}"
                         alt="">
                    <h3 class="mt-4 text-lg font-semibold text-white">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</h3>
                    <p class="text-blue-100 text-sm">EMP-{{ str_pad($payroll->employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Payroll Period</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $payroll->payrollPeriod->start_date->format('M d') }} - {{ $payroll->payrollPeriod->end_date->format('M d, Y') }}</p>
                    </div>

                    <div class="border-t pt-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Department</p>
                        <p class="text-sm text-gray-900">{{ $payroll->employee->department->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Position</p>
                        <p class="text-sm text-gray-900">{{ $payroll->employee->position->name ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-green-50 rounded-lg p-3 mt-4">
                        <p class="text-xs font-medium text-green-600 uppercase">Net Pay</p>
                        <p class="text-2xl font-bold text-green-900">₱{{ number_format($payroll->net_pay, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- DTR Statistics Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-600"></i>
                        DTR Summary for Payroll Period
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $dtrStats['present_days'] }}</div>
                            <div class="text-xs text-gray-600">Present Days</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $dtrStats['absent_days'] }}</div>
                            <div class="text-xs text-gray-600">Absent Days</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $dtrStats['late_days'] }}</div>
                            <div class="text-xs text-gray-600">Late Arrivals</div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $dtrStats['undertime_days'] }}</div>
                            <div class="text-xs text-gray-600">Undertime</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($dtrStats['total_hours_worked'], 1) }}</div>
                            <div class="text-xs text-gray-600">Total Hours</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $dtrStats['overtime_days'] }}</div>
                            <div class="text-xs text-gray-600">OT Days</div>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-indigo-600">{{ $dtrStats['weekend_days'] }}</div>
                            <div class="text-xs text-gray-600">Weekends</div>
                        </div>
                        <div class="bg-pink-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-pink-600">{{ $dtrStats['holiday_days'] }}</div>
                            <div class="text-xs text-gray-600">Holidays</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Breakdown Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calculator mr-2 text-green-600"></i>
                        Payroll Calculation Breakdown
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Earnings Section -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Earnings</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Basic Salary</span>
                                <span class="text-sm font-semibold text-gray-900">₱{{ number_format($payroll->basic_salary, 2) }}</span>
                            </div>

                            @if($payroll->overtime_pay > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <span class="text-sm text-gray-700">Overtime Pay</span>
                                    <span class="ml-2 text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded">From DTR</span>
                                    @if($payroll->regular_overtime_hours > 0)
                                    <div class="text-xs text-gray-500 mt-1">Regular OT: {{ $payroll->regular_overtime_hours }}h @ ₱{{ number_format($payroll->regular_overtime_pay, 2) }}</div>
                                    @endif
                                    @if($payroll->restday_overtime_hours > 0)
                                    <div class="text-xs text-gray-500">Rest Day OT: {{ $payroll->restday_overtime_hours }}h @ ₱{{ number_format($payroll->restday_overtime_pay, 2) }}</div>
                                    @endif
                                    @if($payroll->holiday_overtime_hours > 0)
                                    <div class="text-xs text-gray-500">Holiday OT: {{ $payroll->holiday_overtime_hours }}h @ ₱{{ number_format($payroll->holiday_overtime_pay, 2) }}</div>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-green-600">+₱{{ number_format($payroll->overtime_pay, 2) }}</span>
                            </div>
                            @endif

                            @if($payroll->holiday_pay > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <span class="text-sm text-gray-700">Holiday Pay</span>
                                    <span class="ml-2 text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded">From DTR</span>
                                </div>
                                <span class="text-sm font-semibold text-green-600">+₱{{ number_format($payroll->holiday_pay, 2) }}</span>
                            </div>
                            @endif

                            @if($payroll->night_differential > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <span class="text-sm text-gray-700">Night Differential</span>
                                    <span class="ml-2 text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded">From DTR</span>
                                </div>
                                <span class="text-sm font-semibold text-green-600">+₱{{ number_format($payroll->night_differential, 2) }}</span>
                            </div>
                            @endif

                            @if($payroll->total_allowances > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Allowances</span>
                                <span class="text-sm font-semibold text-green-600">+₱{{ number_format($payroll->total_allowances, 2) }}</span>
                            </div>
                            @endif

                            @if($payroll->bonuses > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Bonuses</span>
                                <span class="text-sm font-semibold text-green-600">+₱{{ number_format($payroll->bonuses, 2) }}</span>
                            </div>
                            @endif

                            <div class="flex justify-between items-center py-3 bg-green-50 px-4 rounded-lg mt-2">
                                <span class="text-sm font-semibold text-green-700">Gross Pay</span>
                                <span class="text-lg font-bold text-green-700">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions Section -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Deductions</h4>
                        <div class="space-y-2">
                            <!-- DTR-Related Deductions -->
                            @if($payroll->late_deductions > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <span class="text-sm text-gray-700">Late Deductions</span>
                                    <span class="ml-2 text-xs text-red-600 bg-red-100 px-2 py-0.5 rounded">From DTR</span>
                                    <div class="text-xs text-gray-500 mt-1">{{ $dtrStats['late_days'] }} late arrival(s)</div>
                                </div>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->late_deductions, 2) }}</span>
                            </div>
                            @endif

                            @if($payroll->absent_deductions > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <span class="text-sm text-gray-700">Absent Deductions</span>
                                    <span class="ml-2 text-xs text-red-600 bg-red-100 px-2 py-0.5 rounded">From DTR</span>
                                    <div class="text-xs text-gray-500 mt-1">{{ $dtrStats['absent_days'] }} absent day(s)</div>
                                </div>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->absent_deductions, 2) }}</span>
                            </div>
                            @endif

                            @if($payroll->undertime_deductions > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <span class="text-sm text-gray-700">Undertime Deductions</span>
                                    <span class="ml-2 text-xs text-red-600 bg-red-100 px-2 py-0.5 rounded">From DTR</span>
                                    <div class="text-xs text-gray-500 mt-1">{{ number_format($dtrStats['total_undertime_hours'], 2) }} undertime hour(s)</div>
                                </div>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->undertime_deductions, 2) }}</span>
                            </div>
                            @endif

                            <!-- Government Deductions -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">GSIS Contribution</span>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->gsis_contribution, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">PhilHealth Contribution</span>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->philhealth_contribution, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Pag-IBIG Contribution</span>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->pagibig_contribution, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Withholding Tax</span>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->withholding_tax, 2) }}</span>
                            </div>

                            @if($payroll->other_deductions > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Other Deductions</span>
                                <span class="text-sm font-semibold text-red-600">-₱{{ number_format($payroll->other_deductions, 2) }}</span>
                            </div>
                            @endif

                            <div class="flex justify-between items-center py-3 bg-red-50 px-4 rounded-lg mt-2">
                                <span class="text-sm font-semibold text-red-700">Total Deductions</span>
                                <span class="text-lg font-bold text-red-700">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Net Pay -->
                    <div class="flex justify-between items-center py-4 bg-blue-600 px-6 rounded-lg">
                        <div>
                            <span class="text-sm font-medium text-blue-100">Net Pay</span>
                            <p class="text-xs text-blue-200">Take-home pay after all deductions</p>
                        </div>
                        <span class="text-2xl font-bold text-white">₱{{ number_format($payroll->net_pay, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Daily DTR Entries Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-gray-600"></i>
                        Daily Time Record (DTR) Entries
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AM In/Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PM In/Out</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($dtrEntries as $dtr)
                            <tr class="hover:bg-gray-50 {{ $dtr->is_weekend ? 'bg-blue-50' : '' }} {{ $dtr->is_holiday ? 'bg-yellow-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $dtr->dtr_date->format('M d, D') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $dtr->am_arrival ?? '--:--' }} / {{ $dtr->am_departure ?? '--:--' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $dtr->pm_arrival ?? '--:--' }} / {{ $dtr->pm_departure ?? '--:--' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 font-medium">
                                    {{ number_format($dtr->total_hours + ($dtr->total_minutes / 60), 2) }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($dtr->status === 'present')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Present</span>
                                    @elseif($dtr->status === 'late')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Late</span>
                                    @elseif($dtr->status === 'undertime')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Undertime</span>
                                    @elseif($dtr->status === 'absent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Absent</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($dtr->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $dtr->remarks }}
                                    @if($dtr->under_time_minutes > 0)
                                        <span class="text-xs text-orange-600">({{ $dtr->under_time_minutes }} min undertime)</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                                    <p>No DTR entries found for this payroll period</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
