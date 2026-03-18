@extends('admin.layouts.app')

@section('title', 'Employee DTR Details')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Employee DTR - {{ $monthYear }}</h1>
                <p class="mt-2 text-sm text-gray-600">Daily Time Record for {{ $employee->full_name }}</p>
            </div>
            <a href="{{ route('admin.dtr.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Employee Information Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center space-x-4 mb-4">
            @if($employee->photo_url)
                <img src="{{ asset($employee->photo_url) }}" alt="" class="h-16 w-16 rounded-full object-cover">
            @else
                <div class="h-16 w-16 rounded-full bg-blue-200 flex items-center justify-center">
                    <span class="text-blue-700 font-bold text-2xl">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </span>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                <p class="text-sm text-gray-600">
                    {{ $employee->department->name ?? 'N/A' }} • {{ $employee->position->title ?? 'N/A' }}
                </p>
            </div>
        </div>

        <!-- Month Navigation -->
        <div class="flex items-center justify-between pt-4 border-t">
            <a href="{{ route('admin.dtr.show', ['employee' => $employee->id, 'month' => $selectedDate->copy()->subMonth()->format('Y-m')]) }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm transition-colors">
                <i class="fas fa-chevron-left mr-2"></i>Previous Month
            </a>
            <span class="text-lg font-semibold text-gray-900">{{ $monthYear }}</span>
            <a href="{{ route('admin.dtr.show', ['employee' => $employee->id, 'month' => $selectedDate->copy()->addMonth()->format('Y-m')]) }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm transition-colors">
                Next Month<i class="fas fa-chevron-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Work Days</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $summary['total_work_days'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Hours</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $summary['total_hours'] }}h {{ $summary['total_minutes'] }}m</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Undertime</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ floor($summary['total_undertime'] / 60) }}h {{ $summary['total_undertime'] % 60 }}m
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Absences</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $summary['absent_days'] }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-user-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- DTR Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daily Time Record</h3>
            <a href="{{ route('admin.dtr.export-cs-form-48', ['employee' => $employee->id, 'month' => $selectedDate->month, 'year' => $selectedDate->year]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-export mr-2"></i>Export DTR
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AM In</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AM Out</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PM In</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PM Out</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Undertime</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dtrEntries as $entry)
                    @php
                        $isWeekend = $entry->is_weekend;
                        $isHoliday = $entry->is_holiday ?? false;
                        $hasUndertime = $entry->under_time_minutes > 0;
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isWeekend ? 'bg-gray-100' : '' }} {{ $isHoliday ? 'bg-yellow-50' : '' }}">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($entry->dtr_date)->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($entry->dtr_date)->format('l') }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            {{ $entry->am_arrival ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            {{ $entry->am_departure ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            {{ $entry->pm_arrival ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            {{ $entry->pm_departure ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            @if($entry->total_hours > 0 || $entry->total_minutes > 0)
                                {{ $entry->total_hours }}h {{ $entry->total_minutes }}m
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            @if($hasUndertime)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ floor($entry->under_time_minutes / 60) }}h {{ $entry->under_time_minutes % 60 }}m
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm">
                            @if($isWeekend)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    WEEKEND
                                </span>
                            @elseif($isHoliday)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    HOLIDAY
                                </span>
                            @elseif($entry->remarks)
                                <span class="text-sm text-gray-600">{{ $entry->remarks }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm no-print">
                            <a href="{{ route('admin.dtr.edit', $entry->id) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-calendar-times text-4xl mb-2"></i>
                            <p>No DTR records found for this month</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="font-semibold">
                        <td colspan="5" class="px-4 py-3 text-right text-sm text-gray-700">TOTAL</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-900">
                            {{ $summary['total_hours'] }}h {{ $summary['total_minutes'] }}m
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-900">
                            {{ floor($summary['total_undertime'] / 60) }}h {{ $summary['total_undertime'] % 60 }}m
                        </td>
                        <td colspan="2" class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body * {
        visibility: hidden;
    }
    .container, .container * {
        visibility: visible;
    }
    .container {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>

@endsection
