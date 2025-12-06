@extends('employee.layouts.app')

@section('title', 'Daily Time Record')
@section('subtitle', 'Civil Service Form No. 48')

@section('content')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #dtr-area, #dtr-area * {
            visibility: visible;
        }
        #dtr-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none !important;
        }
        .page-break {
            page-break-after: always;
        }
        .dtr-table {
            font-size: 10px !important;
        }
        .dtr-header {
            font-size: 11px !important;
        }
    }

    .dtr-table th {
        text-align: center;
        font-weight: 600;
        background-color: #f8fafc;
    }

    .weekend-row {
        background-color: #f1f5f9;
        color: #64748b;
        font-style: italic;
    }

    .holiday-row {
        background-color: #fef3c7;
        color: #92400e;
    }

    .undertime-warning {
        background-color: #fef3c7;
    }

    .empty-cell {
        color: #9ca3af;
        font-style: italic;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Controls Section -->
    <div class="no-print bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <!-- Month Picker -->
                <form method="GET" action="{{ route('employee.dtr.index') }}" class="flex items-center gap-2">
                    <label for="month" class="text-sm font-medium text-gray-700">Select Month:</label>
                    <input type="month" id="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                           class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                        <i class="fas fa-calendar-alt mr-2"></i>View DTR
                    </button>
                </form>

                <!-- Quick Month Navigation -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('employee.dtr.index', ['month' => \Carbon\Carbon::parse($monthYear)->subMonth()->format('Y-m')]) }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="{{ route('employee.dtr.index', ['month' => \Carbon\Carbon::parse($monthYear)->addMonth()->format('Y-m')]) }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Request Adjustment -->
                <a href="" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors no-print">
                    <i class="fas fa-edit mr-2"></i>Request Adjustment
                </a>

                <!-- Print Button -->
                <button onclick="printDTR()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors no-print">
                    <i class="fas fa-print mr-2"></i>Print DTR
                </button>

                <!-- Export Button -->
                <button onclick="exportDTR()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors no-print">
                    <i class="fas fa-download mr-2"></i>Export PDF
                </button>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['total_work_days'] }}</p>
                <p class="text-sm text-gray-600">Work Days</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $summary['total_hours'] }}h {{ $summary['total_minutes'] }}m</p>
                <p class="text-sm text-gray-600">Total Hours</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">{{ floor($summary['total_undertime'] / 60) }}h {{ $summary['total_undertime'] % 60 }}m</p>
                <p class="text-sm text-gray-600">Total Undertime</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-orange-600">{{ $summary['absent_days'] }}</p>
                <p class="text-sm text-gray-600">Absent Days</p>
            </div>
        </div>
    </div>

    <!-- DTR Form No. 48 -->
    <div id="dtr-area" class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Civil Service Header -->
        <div class="border-b border-gray-300 p-4 dtr-header">
            <div class="flex justify-between items-start">
                <div class="text-left">
                    <p class="text-xs font-bold">Civil Service Form No. 48</p>
                    <p class="text-xs">Revised 1995</p>
                </div>
                <div class="text-center flex-1">
                    <p class="text-sm font-bold">DAILY TIME RECORD</p>
                </div>
                <div class="text-right">
                    <p class="text-xs">CS Form No. 48, Revised 1995</p>
                </div>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="p-4 border-b border-gray-300">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p><span class="font-semibold">Name:</span> {{ strtoupper($employee->last_name) }}, {{ strtoupper($employee->first_name) }} {{ strtoupper($employee->middle_name) }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">Position:</span> {{ $employee->position->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">Office/Department:</span> {{ $employee->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">For the month of:</span> {{ $monthYear }}</p>
                </div>
            </div>
        </div>

        <!-- Official Hours Note -->
        <div class="bg-yellow-50 border-b border-yellow-200 p-2">
            <p class="text-xs text-center text-yellow-800 font-medium">
                Official Hours: Arrival 8:00 AM | Departure 5:00 PM (1 hour lunch break)
            </p>
        </div>

        <!-- DTR Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full dtr-table border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-2 py-1 align-middle" rowspan="2">DAY</th>
                        <th class="border border-gray-300 px-2 py-1" colspan="2">A.M.</th>
                        <th class="border border-gray-300 px-2 py-1" colspan="2">P.M.</th>
                        <th class="border border-gray-300 px-2 py-1" colspan="2">UNDERTIME</th>
                        <th class="border border-gray-300 px-2 py-1" colspan="2">OVERTIME</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="2">REMARKS</th>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-1 py-1 text-xs">ARRIVAL</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">DEPARTURE</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">ARRIVAL</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">DEPARTURE</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">HOURS</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">MINUTES</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">HOURS</th>
                        <th class="border border-gray-300 px-1 py-1 text-xs">MINUTES</th>
                    </tr>
                </thead>
                <tbody>
                                        @if ($dtrEntries == null)
                        <h1>NO DTR RECORD</h1>
                    @endif
                    @foreach($dtrEntries as $entry)
                    @php
                        $isWeekend = $entry->is_weekend;
                        $isHoliday = $entry->is_holiday ?? false;
                        $rowClass = $isWeekend ? 'weekend-row' : ($isHoliday ? 'holiday-row' : '');
                        $hasUndertime = $entry->under_time_minutes > 0;
                    @endphp
                    <tr class="{{ $rowClass }} {{ $hasUndertime ? 'undertime-warning' : '' }}">
                        <!-- Day -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs font-medium">
                            {{ \Carbon\Carbon::parse($entry->dtr_date)->format('d') }}
                            <br>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($entry->dtr_date)->format('D') }}
                            </span>
                        </td>

                        <!-- AM Arrival -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            @if($entry->am_arrival)
                                <span class="{{ $entry->is_am_late ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                    {{ $entry->am_arrival }}
                                </span>
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>

                        <!-- AM Departure -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            {!! $entry->am_departure ?? '<span class="empty-cell">-</span>' !!}
                        </td>


                        <!-- PM Arrival -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            @if($entry->pm_arrival)
                                <span class="{{ $entry->is_pm_late ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                    {{ $entry->pm_arrival }}
                                </span>
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>

                        <!-- PM Departure -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            @if($entry->pm_departure)
                                <span class="{{ $entry->is_early_out ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                    {{ $entry->pm_departure }}
                                </span>
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>

                        <!-- Undertime Hours -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            {{ $hasUndertime ? floor($entry->under_time_minutes / 60) : '-' }}
                        </td>

                        <!-- Undertime Minutes -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            {{ $hasUndertime ? ($entry->under_time_minutes % 60) : '-' }}
                        </td>

                        <!-- Overtime Hours -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            {{ $entry->over_time_hours ?? '-' }}
                        </td>

                        <!-- Overtime Minutes -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            {{ $entry->over_time_minutes ?? '-' }}
                        </td>

                        <!-- Remarks -->
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">
                            @if($isWeekend)
                                <span class="text-blue-600 font-medium">WEEKEND</span>
                            @elseif($isHoliday)
                                <span class="text-orange-600 font-medium">HOLIDAY</span>
                            @elseif($entry->remarks)
                                {{ $entry->remarks }}
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="5" class="border border-gray-300 px-2 py-1 text-right text-xs">TOTAL</td>
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">{{ floor($totalUndertime / 60) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">{{ $totalUndertime % 60 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">{{ $totalOvertimeHours ?? 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs">{{ $totalOvertimeMinutes ?? 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center text-xs"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Certification Section -->
        <div class="p-6 border-t border-gray-300">
            <div class="mb-6">
                <p class="text-sm leading-relaxed mb-4">
                    I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, 
                    record of which was made daily at the time of arrival and departure from office.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Employee Signature -->
                <div class="text-center">
                    <div class="border-t border-gray-400 mt-12 pt-2 mx-auto" style="width: 200px;"></div>
                    <p class="font-semibold text-sm mt-2">{{ strtoupper($employee->first_name) }} {{ strtoupper($employee->last_name) }}</p>
                    <p class="text-xs text-gray-600">Employee</p>
                </div>

                <!-- Supervisor Signature -->
                <div class="text-center">
                    <div class="border-t border-gray-400 mt-12 pt-2 mx-auto" style="width: 200px;"></div>
                    <p class="font-semibold text-sm mt-2">{{ strtoupper($certifiedBy->name ?? 'SUPERVISOR') }}</p>
                    <p class="text-xs text-gray-600">Immediate Supervisor</p>
                </div>
            </div>

            <!-- Verification Section -->
            <div class="mt-8 text-center">
                <div class="border-t border-gray-400 mt-12 pt-2 mx-auto" style="width: 300px;"></div>
                <p class="font-semibold text-sm mt-2">{{ strtoupper($verifiedBy->name ?? 'HR OFFICER') }}</p>
                <p class="text-xs text-gray-600">Verified by (HR/Authorized Officer)</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printDTR() {
    window.print();
}

function exportDTR() {
    const month = document.getElementById('month').value;
    window.location.href = `{{ route('employee.dtr.export') }}?month=${month}`;
}

// Add keyboard shortcut for printing
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printDTR();
    }
});

// Auto-refresh every 30 seconds to check for updates
setInterval(() => {
    // You can add auto-refresh logic here if needed
}, 30000);
</script>
@endpush