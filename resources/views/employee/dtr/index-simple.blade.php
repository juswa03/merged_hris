@extends('employee.layouts.app')

@section('title', 'Daily Time Record')
@section('subtitle', 'View and Manage Your DTR')

@section('content')
<style>
    .dtr-table th {
        background-color: #f8fafc;
        font-weight: 600;
        text-align: center;
    }

    .dtr-table td {
        text-align: center;
        font-size: 0.875rem;
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

    .empty-cell {
        color: #9ca3af;
    }

    .stat-card {
        background: white;
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-card.warning {
        border-left-color: #ef4444;
    }

    .stat-card.success {
        border-left-color: #10b981;
    }

    .stat-value {
        font-size: 1.875rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Month Selection & Actions -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- Month Picker -->
            <form method="GET" action="{{ route('employee.dtr.index') }}" class="flex items-center gap-3">
                <label for="month" class="text-sm font-medium text-gray-700">Select Month:</label>
                <input type="month" id="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                    View
                </button>
            </form>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button onclick="location.href='{{ route('employee.dtr.index', ['month' => \Carbon\Carbon::parse($monthYear)->subMonth()->format('Y-m')]) }}'"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition-colors">
                    ← Previous
                </button>
                <button onclick="location.href='{{ route('employee.dtr.index', ['month' => \Carbon\Carbon::parse($monthYear)->addMonth()->format('Y-m')]) }}'"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition-colors">
                    Next →
                </button>
                <form action="{{ route('employee.dtr.export') }}" method="GET" class="inline">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="year" value="{{ request('year') }}">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                        📥 Export PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-label">Work Days</div>
            <div class="stat-value text-blue-600">{{ $summary['total_work_days'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Hours</div>
            <div class="stat-value text-green-600">{{ $summary['total_hours'] }}h {{ $summary['total_minutes'] }}m</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-label">Undertime</div>
            <div class="stat-value text-red-600">{{ floor($summary['total_undertime'] / 60) }}h {{ $summary['total_undertime'] % 60 }}m</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Absences</div>
            <div class="stat-value text-orange-600">{{ $summary['absent_days'] }}</div>
        </div>
    </div>

    <!-- DTR Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 p-4 flex justify-between items-start">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($monthYear)->format('F Y') }} - Daily Time Record</h2>
                <p class="text-sm text-gray-600">{{ $employee->full_name }} | {{ $employee->department->name ?? 'N/A' }}</p>
            </div>

            <!-- Legend (Top Right) -->
            <div class="text-sm text-gray-600">
                <div class="flex gap-4 text-xs">
                    <div><span class="inline-block w-3 h-3 bg-green-100 border border-green-300 rounded mr-2"></span>Present</div>
                    <div><span class="inline-block w-3 h-3 bg-red-100 border border-red-300 rounded mr-2"></span>Absent/Undertime</div>
                    <div><span class="inline-block w-3 h-3 bg-yellow-100 border border-yellow-300 rounded mr-2"></span>Holiday</div>
                    <div><span class="inline-block w-3 h-3 bg-gray-100 border border-gray-300 rounded mr-2"></span>Weekend</div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full dtr-table border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-300">
                        <th class="border border-gray-200 px-3 py-2">Day</th>
                        <th class="border border-gray-200 px-3 py-2">Date</th>
                        <th class="border border-gray-200 px-3 py-2">AM Arrival</th>
                        <th class="border border-gray-200 px-3 py-2">AM Departure</th>
                        <th class="border border-gray-200 px-3 py-2">PM Arrival</th>
                        <th class="border border-gray-200 px-3 py-2">PM Departure</th>
                        <th class="border border-gray-200 px-3 py-2">Undertime (hrs)</th>
                        <th class="border border-gray-200 px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($dtrEntries == null || $dtrEntries->count() == 0)
                    <tr>
                        <td colspan="8" class="border border-gray-200 px-3 py-4 text-center text-gray-500">
                            No DTR records for this month
                        </td>
                    </tr>
                    @endif
                    @foreach($dtrEntries as $entry)
                    @php
                        $isWeekend = $entry->is_weekend;
                        $isHoliday = $entry->is_holiday ?? false;
                        $rowClass = $isWeekend ? 'weekend-row' : ($isHoliday ? 'holiday-row' : '');
                        $hasUndertime = $entry->under_time_minutes > 0;
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="border border-gray-200 px-3 py-2">
                            {{ \Carbon\Carbon::parse($entry->dtr_date)->format('D') }}
                        </td>
                        <td class="border border-gray-200 px-3 py-2 text-sm">
                            {{ \Carbon\Carbon::parse($entry->dtr_date)->format('M d') }}
                        </td>
                        <td class="border border-gray-200 px-3 py-2">
                            @if($entry->am_arrival)
                                <span class="{{ ($entry->is_am_late ?? false) ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $entry->am_arrival }}
                                </span>
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>
                        <td class="border border-gray-200 px-3 py-2">
                            {{ $entry->am_departure ?? '─' }}
                        </td>
                        <td class="border border-gray-200 px-3 py-2">
                            @if($entry->pm_arrival)
                                <span class="{{ ($entry->is_pm_late ?? false) ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $entry->pm_arrival }}
                                </span>
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>
                        <td class="border border-gray-200 px-3 py-2">
                            @if($entry->pm_departure)
                                <span class="{{ ($entry->is_early_out ?? false) ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $entry->pm_departure }}
                                </span>
                            @else
                                <span class="empty-cell">-</span>
                            @endif
                        </td>
                        <td class="border border-gray-200 px-3 py-2 {{ $hasUndertime ? 'font-semibold text-red-600' : '' }}">
                            @if($hasUndertime)
                                {{ floor($entry->under_time_minutes / 60) }}h {{ $entry->under_time_minutes % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td class="border border-gray-200 px-3 py-2 text-xs">
                            @if($isHoliday)
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Holiday</span>
                            @elseif($isWeekend)
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">Weekend</span>
                            @elseif($entry->total_hours == 0 && $entry->total_minutes == 0)
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Absent</span>
                            @else
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Present</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-300 font-semibold">
                        <td colspan="6" class="border border-gray-200 px-3 py-2 text-right">TOTAL</td>
                        <td class="border border-gray-200 px-3 py-2">
                            {{ floor($summary['total_undertime'] / 60) }}h {{ $summary['total_undertime'] % 60 }}m
                        </td>
                        <td class="border border-gray-200 px-3 py-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection
