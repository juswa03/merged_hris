@extends('employee.layouts.app')

@section('title', 'My Attendance')
@section('subtitle', 'Daily Time Records & Attendance History')

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Present This Month</p>
                    <h3 class="text-2xl font-bold">{{ $monthlyStats['present'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total Hours</p>
                    <h3 class="text-2xl font-bold">{{ $monthlyStats['total_hours'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Average Daily</p>
                    <h3 class="text-2xl font-bold">{{ $monthlyStats['avg_hours'] ?? 0 }}h</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Biometric Logs</p>
                    <h3 class="text-2xl font-bold">{{ $monthlyStats['biometric_count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                    <!-- Date Range Filter -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">From:</label>
                        <input type="date" id="startDate" class="border rounded px-3 py-1 text-sm" 
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">To:</label>
                        <input type="date" id="endDate" class="border rounded px-3 py-1 text-sm"
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <button onclick="filterAttendance()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm">
                        Apply
                    </button>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button onclick="exportAttendance()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm flex items-center">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                    <a href="" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded text-sm flex items-center">
                        <i class="fas fa-edit mr-2"></i> Request Adjustment
                    </a>
                </div>
            </div>
        </div>

        <!-- Attendance Records Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->created_at->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $attendance->attendanceType->name === 'Check-in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $attendance->attendanceType->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->attendanceSource->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $attendance->device_uid ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $attendance->remarks ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $attendance->remarks; 
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $status === 'On Time' ? 'bg-green-100 text-green-800' : 
                                   ($status === 'Late' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No attendance records found for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>

    <!-- Monthly Summary Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Attendance Summary</h3>
        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels'] ?? []),
                datasets: [{
                    label: 'Check-ins',
                    data: @json($chartData['checkins'] ?? []),
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }, {
                    label: 'Check-outs',
                    data: @json($chartData['checkouts'] ?? []),
                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                    borderColor: 'rgb(239, 68, 68)',
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
                            text: 'Number of Records'
                        }
                    }
                }
            }
        });
    });

    function filterAttendance() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        
        window.location.href = url.toString();
    }

    function exportAttendance() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        const url = '' + 
                   `?start_date=${startDate}&end_date=${endDate}`;
        
        window.open(url, '_blank');
    }
</script>
@endpush