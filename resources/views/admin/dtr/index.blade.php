@extends('admin.layouts.app')

@section('title', 'DTR Management')

@section('content')

<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="DTR Management"
        description="View and manage Daily Time Records for all employees"
    >
        <x-slot name="actions">
            <x-admin.action-button :href="route('admin.dtr.export', request()->all())" variant="success" icon="fas fa-file-pdf">Export to PDF</x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Total Employees" :value="$totalEmployees" icon="fas fa-users" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Work Days" :value="$totalWorkDays" icon="fas fa-calendar-check" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Total Absences" :value="$totalAbsences" icon="fas fa-user-times" gradientFrom="red-500" gradientTo="red-600"/>
        <x-admin.gradient-stat-card title="Total Undertime" :value="floor($totalUndertime / 60) . 'h ' . ($totalUndertime % 60) . 'm'" icon="fas fa-clock" gradientFrom="yellow-500" gradientTo="yellow-600"/>
    </div>

    <!-- Filters and Actions -->
    <x-admin.card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.dtr.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <input type="month" id="month" name="month" value="{{ $month }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select id="employee_id" name="employee_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search employee..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
            <div class="flex gap-3">
                <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter">Apply Filters</x-admin.action-button>
                <x-admin.action-button :href="route('admin.dtr.index')" variant="secondary" icon="fas fa-redo">Reset</x-admin.action-button>
            </div>
        </form>
    </x-admin.card>

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
                        <a href="{{ route('admin.dtr.show', ['employee' => $data['employee']->id, 'month' => $month]) }}"
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
                                    <a href="{{ route('admin.dtr.edit', $entry->id) }}"
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
                    <a href="{{ route('admin.dtr.show', ['employee' => $data['employee']->id, 'month' => $month]) }}"
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all {{ $data['entries']->count() }} entries <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-admin.empty-state
                icon="fas fa-calendar-times"
                title="No DTR Records Found"
                message="No DTR records found for the selected filters. Try adjusting your search criteria."
            />
        </div>
        @endforelse
    </div>
</div>

@endsection
