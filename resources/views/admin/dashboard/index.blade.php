@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Admin Dashboard"
        description="Welcome back! Here's what's happening with your organization today."
    />

    <!-- Alert Cards -->
    @if($lowAttendanceDays > 0 || $missingDtrCount > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @if($lowAttendanceDays > 0)
        <x-admin.alert type="warning" title="Low Attendance Alert">
            {{ $lowAttendanceDays }} day(s) in the past week had below 70% attendance.
        </x-admin.alert>
        @endif

        @if($missingDtrCount > 0)
        <x-admin.alert type="error" title="Missing DTR">
            {{ $missingDtrCount }} active employee(s) have no DTR entry for yesterday.
        </x-admin.alert>
        @endif
    </div>
    @endif

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.gradient-stat-card
            title="Total Employees"
            :value="$totalEmployees"
            icon="fas fa-users"
            gradientFrom="blue-500"
            gradientTo="blue-600"
            :description="$newEmployeesThisMonth > 0 ? '↑ ' . $newEmployeesThisMonth . ' new this month' : 'No new hires this month'"
        />
        <x-admin.gradient-stat-card
            title="Active Employees"
            :value="$activeEmployees"
            icon="fas fa-user-check"
            gradientFrom="green-500"
            gradientTo="green-600"
            :description="($totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0) . '% of total'"
        />
        <x-admin.gradient-stat-card
            title="Present Today"
            :value="$todayAttendance"
            icon="fas fa-calendar-check"
            gradientFrom="purple-500"
            gradientTo="purple-600"
            :description="$attendanceRate . '% attendance rate'"
        />
        <x-admin.gradient-stat-card
            title="Departments"
            :value="$departmentCount"
            icon="fas fa-building"
            gradientFrom="orange-500"
            gradientTo="orange-600"
            :description="$lateToday > 0 ? $lateToday . ' late arrival(s) today' : 'No late arrivals today'"
        />
    </div>

    <!-- Payroll Summary Card -->
    @if($lastCompletedPeriod)
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Last Payroll Summary</h3>
                <p class="text-sm opacity-90">{{ $lastCompletedPeriod->name }}</p>
                <p class="text-sm opacity-75">{{ $lastCompletedPeriod->formatted_period }}</p>
            </div>
            <div class="text-right">
                <p class="text-4xl font-bold">₱{{ number_format($totalPayrollAmount, 2) }}</p>
                <p class="text-sm opacity-90 mt-2">Total Disbursed</p>
            </div>
        </div>
        @if($currentPayrollPeriod && $pendingPayrolls > 0)
        <div class="mt-4 pt-4 border-t border-blue-400">
            <div class="flex items-center justify-between">
                <p class="text-sm">Current Period: {{ $currentPayrollPeriod->name }}</p>
                <p class="text-sm font-medium">{{ $pendingPayrolls }} pending payroll(s)</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Attendance Trend Chart -->
        <x-admin.card title="30-Day Attendance Trend">
            <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </x-admin.card>

        <!-- Department Distribution Chart -->
        <x-admin.card title="Employee Distribution by Department">
            <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="departmentChart"></canvas>
            </div>
        </x-admin.card>
    </div>

    <!-- Weekly Hours Chart -->
    <x-admin.card title="Weekly Hours Worked (Last 4 Weeks)" class="mb-6">
        <div class="chart-container" style="position: relative; height: 300px;">
            <canvas id="weeklyHoursChart"></canvas>
        </div>
    </x-admin.card>

    <!-- Recent Activities and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent Attendance -->
        <x-admin.card title="Recent Attendance" class="lg:col-span-2">
            <div class="space-y-3">
                @forelse($recentAttendance as $attendance)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($attendance->employee->photo_url)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($attendance->employee->photo_url) }}" alt="">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">
                                        {{ substr($attendance->employee->first_name, 0, 1) }}{{ substr($attendance->employee->last_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $attendance->employee->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $attendance->attendanceType->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-900">{{ $attendance->created_at->format('h:i A') }}</p>
                        <p class="text-xs text-gray-500">{{ $attendance->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <x-admin.empty-state
                    icon="fas fa-calendar-times"
                    title="No recent attendance records"
                />
                @endforelse
            </div>
        </x-admin.card>

        <!-- Quick Actions -->
        <x-admin.card title="Quick Actions">
            <div class="space-y-3">
                <a href="{{ route('admin.employees.index') }}" class="block w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        <span class="text-sm font-medium text-blue-900">Manage Employees</span>
                    </div>
                </a>
                <a href="{{ route('admin.attendance.index') }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-check text-green-600 mr-3"></i>
                        <span class="text-sm font-medium text-green-900">View Attendance</span>
                    </div>
                </a>
                <a href="{{ route('admin.departments.index') }}" class="block w-full text-left px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-building text-yellow-600 mr-3"></i>
                        <span class="text-sm font-medium text-yellow-900">Manage Departments</span>
                    </div>
                </a>
                <a href="{{ route('admin.payroll.index') }}" class="block w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-purple-600 mr-3"></i>
                        <span class="text-sm font-medium text-purple-900">Process Payroll</span>
                    </div>
                </a>
                <a href="{{ route('admin.dtr.index') }}" class="block w-full text-left px-4 py-3 bg-pink-50 hover:bg-pink-100 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-pink-600 mr-3"></i>
                        <span class="text-sm font-medium text-pink-900">DTR Management</span>
                    </div>
                </a>
            </div>
        </x-admin.card>
    </div>

    <!-- Recent Employees Table -->
    <x-admin.card title="Recently Added Employees" :padding="false">
        <x-slot name="footer">
            <div class="text-center">
                <a href="{{ route('admin.employees.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View All Employees <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </x-slot>

        <x-admin.table-wrapper :responsive="false">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentEmployees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($employee->photo_url)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($employee->photo_url) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium text-sm">
                                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->contact_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $employee->department->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $employee->position->title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($employee->jobStatus)
                            <x-admin.badge :variant="$employee->jobStatus->name === 'Active' ? 'success' : 'default'">
                                {{ $employee->jobStatus->name }}
                            </x-admin.badge>
                        @else
                            <x-admin.badge variant="default">N/A</x-admin.badge>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-admin.empty-state
                            icon="fas fa-users"
                            title="No employees found"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
    </x-admin.card>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Trend Chart (Last 30 Days)
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: {!! $attendanceChartLabels !!},
        datasets: [{
            label: 'Employees Present',
            data: {!! $attendanceChartData !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointHoverRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5
                }
            }
        }
    }
});

// Department Distribution Pie Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
const departmentData = @json($departmentDistribution);
const departmentChart = new Chart(departmentCtx, {
    type: 'doughnut',
    data: {
        labels: departmentData.map(d => d.name),
        datasets: [{
            data: departmentData.map(d => d.count),
            backgroundColor: [
                "rgba(230, 126, 34, 1)",   // STED - Orange
                "rgba(241, 196, 15, 1)",   // SOE - Golden Yellow
                "rgba(142, 68, 173, 1)",   // SAS - Purple
                "rgba(52, 73, 94, 1)",     // SME - Navy Blue
                "rgba(192, 57, 43, 1)",    // SCJE - Deep Red
                "rgba(46, 134, 222, 1)",   // STCS - Royal Blue
                "rgba(26, 188, 156, 1)",   // NURSING - Teal
                "rgba(39, 174, 96, 1)"     // HR - Forest Green
            ],

            borderColor: [
                "rgba(230, 126, 34, 1)",   // STED - Orange
                "rgba(241, 196, 15, 1)",   // SOE - Golden Yellow
                "rgba(142, 68, 173, 1)",   // SAS - Purple
                "rgba(52, 73, 94, 1)",     // SME - Navy Blue
                "rgba(192, 57, 43, 1)",    // SCJE - Deep Red
                "rgba(46, 134, 222, 1)",   // STCS - Royal Blue
                "rgba(26, 188, 156, 1)",   // NURSING - Teal
                "rgba(39, 174, 96, 1)"     // HR - Forest Green
            ],

            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed + ' employees';
                        return label;
                    }
                }
            }
        }
    }
});

// Weekly Hours Bar Chart
const weeklyHoursCtx = document.getElementById('weeklyHoursChart').getContext('2d');
const weeklyHoursChart = new Chart(weeklyHoursCtx, {
    type: 'bar',
    data: {
        labels: {!! $weeklyHoursLabels !!},
        datasets: [{
            label: 'Total Hours Worked',
            data: {!! $weeklyHoursData !!},
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed.y + ' hours';
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + ' hrs';
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection
