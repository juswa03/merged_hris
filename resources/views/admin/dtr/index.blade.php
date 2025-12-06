@extends('layouts.app')

@section('title', 'DTR Management')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">DTR Management</h1>
        <p class="mt-2 text-sm text-gray-600">View and manage Daily Time Records for all employees</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $totalEmployees }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Work Days</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $totalWorkDays }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Absences</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $totalAbsences }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-user-times text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Undertime</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ floor($totalUndertime / 60) }}h {{ $totalUndertime % 60 }}m</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('dtr.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Month Picker -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <input type="month" id="month" name="month" value="{{ $month }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Employee Filter -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                    <select id="employee_id" name="employee_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search employee..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t">
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('dtr.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dtr.export', request()->all()) }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export to CSV
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- DTR Summary by Employee -->
    <div class="space-y-4">
        @forelse($employeeDtrData as $data)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Employee Header -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if($data['employee']->photo_url)
                            <img src="{{ asset($data['employee']->photo_url) }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <div class="h-12 w-12 rounded-full bg-blue-200 flex items-center justify-center">
                                <span class="text-blue-700 font-bold text-lg">
                                    {{ substr($data['employee']->first_name, 0, 1) }}{{ substr($data['employee']->last_name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $data['employee']->full_name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $data['employee']->department->name ?? 'N/A' }} • {{ $data['employee']->position->title ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dtr.show', ['employee' => $data['employee']->id, 'month' => $month]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Full DTR
                        </a>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-6 bg-gray-50">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $data['summary']['total_work_days'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Work Days</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">
                        {{ $data['summary']['total_hours'] }}h {{ $data['summary']['total_minutes'] }}m
                    </p>
                    <p class="text-xs text-gray-600 mt-1">Total Hours</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $data['summary']['absent_days'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Absences</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ floor($data['summary']['total_undertime'] / 60) }}h {{ $data['summary']['total_undertime'] % 60 }}m
                    </p>
                    <p class="text-xs text-gray-600 mt-1">Undertime</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $data['entries']->count() }}</p>
                    <p class="text-xs text-gray-600 mt-1">DTR Entries</p>
                </div>
            </div>

            <!-- Quick DTR Preview (Last 7 Days) -->
            <div class="p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Recent DTR Entries</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">AM In</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">AM Out</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">PM In</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">PM Out</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Hours</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Undertime</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data['entries']->take(7) as $entry)
                            <tr class="hover:bg-gray-50 {{ $entry->is_weekend ? 'bg-gray-100' : '' }}">
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($entry->dtr_date)->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($entry->dtr_date)->format('l') }}</span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                    {{ $entry->am_arrival ?? '-' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                    {{ $entry->am_departure ?? '-' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                    {{ $entry->pm_arrival ?? '-' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                    {{ $entry->pm_departure ?? '-' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                    {{ $entry->total_hours }}h {{ $entry->total_minutes }}m
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                    @if($entry->under_time_minutes > 0)
                                        <span class="text-yellow-600 font-medium">
                                            {{ floor($entry->under_time_minutes / 60) }}h {{ $entry->under_time_minutes % 60 }}m
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                    <a href="{{ route('dtr.edit', $entry->id) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($data['entries']->count() > 7)
                <div class="text-center mt-4">
                    <a href="{{ route('dtr.show', ['employee' => $data['employee']->id, 'month' => $month]) }}"
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all {{ $data['entries']->count() }} entries <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-calendar-times text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No DTR Records Found</h3>
            <p class="text-gray-500">No DTR records found for the selected filters. Try adjusting your search criteria.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
