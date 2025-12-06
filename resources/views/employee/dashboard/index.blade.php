@extends('employee.layouts.app')

@section('title', 'Employee Dashboard')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->employee->first_name ?? auth()->user()->name }}!</h1>
                <p class="opacity-90">Here's your daily overview</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-semibold">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-2xl font-bold" id="currentTime">{{ now()->format('h:i:s A') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Today's Attendance Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full {{ $todayAttendance ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} mr-4">
                    <i class="fas fa-fingerprint text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Today's Status</p>
                    <h3 class="text-2xl font-bold">{{ $todayAttendance ? $todayAttendance->status : 'Not Checked In' }}</h3>
                    <p class="text-sm {{ $todayAttendance ? 'text-green-500' : 'text-gray-500' }}">
                        @if($todayAttendance && $todayAttendance->check_in)
                            Checked in: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}
                        @else
                            Awaiting check-in
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Monthly Hours -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Monthly Hours</p>
                    <h3 class="text-2xl font-bold">{{ number_format($monthlyStats['total_hours'] ?? 0) }}h</h3>
                    <p class="text-sm text-gray-500">{{ $monthlyStats['work_days'] ?? 0 }} work days</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Payroll -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Next Payday</p>
                    <h3 class="text-2xl font-bold">₱ {{ number_format($nextPayroll?->net_salary ?? 0) }}</h3>
                    <p class="text-sm text-gray-500">
                        @if($nextPayroll)
                            {{ \Carbon\Carbon::parse($nextPayroll->payroll_date)->format('M d') }}
                        @else
                            Not scheduled
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Leave Balance -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-umbrella-beach text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Leave Balance</p>
                    <h3 class="text-2xl font-bold">{{ $leaveBalance ?? 0 }} days</h3>
                    <p class="text-sm text-gray-500">Vacation & Sick</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- DTR Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Daily Time Record</h2>
            <div class="space-y-4">
                @if(!$todayAttendance || !$todayAttendance->check_in)
                <button onclick="checkIn()" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center transition duration-200">
                    <i class="fas fa-fingerprint mr-2"></i>
                    Check In Now
                </button>
                @endif

                @if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                <button onclick="checkOut()" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center transition duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Check Out Now
                </button>
                @endif

                @if($todayAttendance && $todayAttendance->check_out)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-600">Completed for today</p>
                    <p class="text-sm text-gray-500">
                        In: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }} | 
                        Out: {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}
                    </p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-2 mt-4">
                    <a href="" class="bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 px-3 rounded text-sm text-center transition duration-200">
                        <i class="fas fa-history mr-1"></i> DTR History
                    </a>
                    <a href="" class="bg-yellow-50 hover:bg-yellow-100 text-yellow-600 py-2 px-3 rounded text-sm text-center transition duration-200">
                        <i class="fas fa-edit mr-1"></i> Request Adjustment
                    </a>
                </div>
            </div>
        </div>

        <!-- Biometric Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Biometric Status</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 border rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full {{ auth()->user()->employee->isFingerprintEnrolled ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} mr-3">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <div>
                            <p class="font-medium">Fingerprint</p>
                            <p class="text-sm text-gray-500">
                                {{ auth()->user()->employee->isFingerprintEnrolled ? 'Enrolled' : 'Not Enrolled' }}
                            </p>
                        </div>
                    </div>
                    @if(!auth()->user()->employee->isFingerprintEnrolled)
                    <button class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-200">
                        Enroll
                    </button>
                    @endif
                </div>

                <div class="flex items-center justify-between p-3 border rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full {{ auth()->user()->employee->backup_pin ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} mr-3">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <p class="font-medium">Backup PIN</p>
                            <p class="text-sm text-gray-500">
                                {{ auth()->user()->employee->backup_pin ? 'Active' : 'Not Set' }}
                            </p>
                        </div>
                    </div>
                    @if(!auth()->user()->employee->backup_pin)
                    <button class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-200">
                        Set PIN Code
                    </button>
                    @endif
                </div>


                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm font-medium mb-1">Last Biometric Usage</p>
                    <p class="text-xs text-gray-500">
                        @if($lastBiometricUsage)
                            {{ \Carbon\Carbon::parse($lastBiometricUsage->created_at)->format('M d, Y h:i A') }}
                        @else
                            No records found
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Payroll Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Payroll Overview</h2>
            <div class="space-y-4">
                <div class="p-3 border rounded-lg">
                    <p class="text-sm text-gray-500">Current Month</p>
                    <p class="text-xl font-bold">₱ {{ number_format($currentPayroll?->net_salary ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Net Salary</p>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2 border rounded text-center">
                        <p class="text-sm text-gray-500">Basic Pay</p>
                        <p class="font-semibold">₱ {{ number_format($currentPayroll?->basic_salary ?? 0) }}</p>
                    </div>
                    <div class="p-2 border rounded text-center">
                        <p class="text-sm text-gray-500">Allowances</p>
                        <p class="font-semibold">₱ {{ number_format($currentPayroll?->total_allowances ?? 0) }}</p>
                    </div>
                </div>

                <div class="bg-blue-50 p-3 rounded-lg">
                    <p class="text-sm font-medium mb-1">Next Payroll Date</p>
                    <p class="text-lg font-bold text-blue-600">
                        {{ $nextPayrollDate ? \Carbon\Carbon::parse($nextPayrollDate)->format('F d, Y') : 'Not scheduled' }}
                    </p>
                </div>

                <a href="" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded text-sm transition duration-200">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> View Payroll History
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Weekly Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent DTR Activity -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">Recent Attendance</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentAttendance as $attendance)
                    <div class="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-500">
                                In: {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--:--' }} | 
                                Out: {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '--:--' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                               ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No recent attendance records</p>
                    @endforelse
                </div>
                <a href="{{ route('employee.dtr.index') }}" class="block text-center mt-4 text-blue-600 hover:text-blue-700 text-sm">
                    View All Attendance Records →
                </a>
            </div>
        </div>

        <!-- Weekly Hours Summary -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">This Week's Hours</h2>
            </div>
            <div class="p-6">
                <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                    <canvas id="weeklyHoursChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 border rounded">
                        <p class="text-sm text-gray-500">Total Hours</p>
                        <p class="font-semibold">{{ $weeklyStats['total_hours'] ?? 0 }}h</p>
                    </div>
                    <div class="p-2 border rounded">
                        <p class="text-sm text-gray-500">Work Days</p>
                        <p class="font-semibold">{{ $weeklyStats['work_days'] ?? 0 }}/5</p>
                    </div>
                    <div class="p-2 border rounded">
                        <p class="text-sm text-gray-500">Avg. Daily</p>
                        <p class="font-semibold">{{ $weeklyStats['avg_daily'] ?? 0 }}h</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = 
            now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    }
    setInterval(updateTime, 1000);

    // Check In/Out functions
    function checkIn() {
        // Implement biometric check-in logic
        //alert('Biometric check-in triggered. This would integrate with your biometric device.');
    }

    function checkOut() {
        // Implement biometric check-out logic
        //alert('Biometric check-out triggered. This would integrate with your biometric device.');
    }

    // Weekly Hours Chart
    document.addEventListener('DOMContentLoaded', function() {
        const weeklyData = @json($weeklyHours ?? []);
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        
        const ctx = document.getElementById('weeklyHoursChart').getContext('2d');
        const weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Hours Worked',
                    data: weeklyData,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours'
                        }
                    }
                }
            }
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } z-50`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
</script>
@endpush
@endsection