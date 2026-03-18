@extends('hr.layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Welcome back, {{ auth()->user()->first_name }}!</h2>
            <p class="text-sm text-gray-500 mt-1">Here's what's happening today — {{ now()->format('l, F d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('hr.employees.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">
                <i class="fas fa-user-plus"></i> Onboard Employee
            </a>
            <a href="{{ route('hr.leave.index') }}" class="flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg">
                <i class="fas fa-calendar-check"></i> Leave Applications
            </a>
        </div>
    </div>

    {{-- Top Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Employees --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                    +{{ $newEmployeesThisMonth }} this month
                </span>
            </div>
            <p class="text-sm text-gray-500">Total Employees</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalEmployees) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $activeEmployees }} active</p>
        </div>

        {{-- Today's Attendance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-user-check text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $attendanceRate >= 80 ? 'bg-green-100 text-green-700' : ($attendanceRate >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ $attendanceRate }}%
                </span>
            </div>
            <p class="text-sm text-gray-500">Today's Attendance</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($todayAttendance) }}</p>
            <p class="text-xs text-gray-400 mt-1">out of {{ $activeEmployees }} active</p>
        </div>

        {{-- Pending Leaves --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i class="fas fa-calendar-times text-amber-600 text-lg"></i>
                </div>
                @if($pendingLeaves > 0)
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-amber-100 text-amber-700">
                        Needs action
                    </span>
                @else
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700">All clear</span>
                @endif
            </div>
            <p class="text-sm text-gray-500">Pending Leaves</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($pendingLeaves) }}</p>
            <a href="{{ route('hr.leave.index') }}" class="text-xs text-blue-600 hover:underline mt-1 block">View all requests →</a>
        </div>

        {{-- New Hires This Month --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <i class="fas fa-user-plus text-purple-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                    {{ now()->format('M Y') }}
                </span>
            </div>
            <p class="text-sm text-gray-500">New Hires</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($newEmployeesThisMonth) }}</p>
            <a href="{{ route('hr.employees.index') }}" class="text-xs text-blue-600 hover:underline mt-1 block">View employee list →</a>
        </div>
    </div>

    {{-- Leave Overview & Recent Hires --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Leave Status Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-500"></i> Leave Overview
                </h3>
                <a href="{{ route('hr.leave.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-amber-700">{{ $leaveStats['pending'] }}</p>
                    <p class="text-xs text-amber-600 mt-1">Pending</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-green-700">{{ $leaveStats['approved'] }}</p>
                    <p class="text-xs text-green-600 mt-1">Approved</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-red-700">{{ $leaveStats['rejected'] }}</p>
                    <p class="text-xs text-red-600 mt-1">Rejected</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $leaveStats['total'] }}</p>
                    <p class="text-xs text-blue-600 mt-1">Total This Month</p>
                </div>
            </div>

            @if($recentLeaves->isNotEmpty())
            <div class="mt-4">
                <p class="text-xs font-medium text-gray-500 mb-2">Recent Requests</p>
                <div class="space-y-2">
                    @foreach($recentLeaves as $leave)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $leave->user->first_name ?? 'Unknown' }} {{ $leave->user->last_name ?? '' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $leave->type }} · {{ \Carbon\Carbon::parse($leave->filing_date)->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ $leave->workflow_status === 'approved' ? 'bg-green-100 text-green-700' :
                               ($leave->workflow_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $leave->workflow_status)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Recent Hires --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-user-plus text-blue-500"></i> Recent Hires
                </h3>
                <a href="{{ route('hr.employees.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            @if($recentHires->isNotEmpty())
            <div class="space-y-3">
                @foreach($recentHires as $employee)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $employee->position->name ?? 'N/A' }} · {{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                    <p class="text-xs text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($employee->hire_date)->diffForHumans() }}</p>
                </div>
                @endforeach
            </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No recent hires.</p>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-blue-500"></i> Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @php
                $actions = [
                    ['icon' => 'fa-user-plus',       'label' => 'Onboard Employee',        'route' => route('hr.employees.create'),           'color' => 'blue'],
                    ['icon' => 'fa-calendar-alt',    'label' => 'Leave Applications',      'route' => route('hr.leave.index'),                 'color' => 'amber'],
                    ['icon' => 'fa-clock',           'label' => 'Attendance Log',          'route' => route('hr.attendance.index'),            'color' => 'blue'],
                    ['icon' => 'fa-money-bill-wave', 'label' => 'Generate Payroll',        'route' => route('hr.payroll.generation.index'),    'color' => 'purple'],
                    ['icon' => 'fa-fingerprint',     'label' => 'Biometric Enrollment',    'route' => route('hr.biometric.index'),             'color' => 'indigo'],
                    ['icon' => 'fa-file-alt',        'label' => 'HR Reports',              'route' => route('hr.report-builder.index'),        'color' => 'gray'],
                ];
            @endphp
            @foreach($actions as $action)
            <a href="{{ $action['route'] }}"
               class="flex flex-col items-center gap-2 p-3 rounded-lg bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors text-center">
                <i class="fas {{ $action['icon'] }} text-{{ $action['color'] }}-600 text-xl"></i>
                <span class="text-xs text-{{ $action['color'] }}-700 font-medium">{{ $action['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Department Distribution --}}
    @if($departmentDistribution->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-building text-gray-500"></i> Employees by Department
        </h3>
        <div class="space-y-3">
            @php $maxCount = $departmentDistribution->max('employees_count') ?: 1; @endphp
            @foreach($departmentDistribution as $dept)
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600 w-36 truncate">{{ $dept->name }}</span>
                <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full"
                         style="width: {{ ($dept->employees_count / $maxCount) * 100 }}%"></div>
                </div>
                <span class="text-xs font-medium text-gray-800 w-8 text-right">{{ $dept->employees_count }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
