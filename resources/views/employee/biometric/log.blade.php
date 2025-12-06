@extends('employee.layouts.app')

@section('title', 'Biometric Log')
@section('subtitle', 'Fingerprint & RFID Access History')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Logs -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Logs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $logStats['total'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">All time records</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-history text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Logs -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Today's Logs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $logStats['today'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ now()->format('M d, Y') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Fingerprint Logs -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-purple-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Fingerprint</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $logStats['fingerprint'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Fingerprint accesses</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-fingerprint text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- RFID Logs -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-orange-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">RFID Logs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $logStats['rfid'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">RFID card accesses</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-id-card text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" id="startDate" value="{{ $filters['start_date'] }}" 
                               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="self-center text-gray-500">to</span>
                        <input type="date" id="endDate" value="{{ $filters['end_date'] }}"
                               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Log Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Log Type</label>
                    <select id="logType" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="fingerprint" {{ $filters['type'] == 'fingerprint' ? 'selected' : '' }}>Fingerprint</option>
                        <option value="rfid" {{ $filters['type'] == 'rfid' ? 'selected' : '' }}>RFID</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="success" {{ $filters['status'] == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ $filters['status'] == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-3">
                <button onclick="applyFilters()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
                <button onclick="resetFilters()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
                <button onclick="exportLogs()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Biometric Logs Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Biometric Access Logs</h3>
                    <p class="text-sm text-gray-600 mt-1">Your fingerprint and RFID access history</p>
                </div>
                <div class="text-sm text-gray-500">
                    Showing {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} records
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Access Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Device
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Details
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->timestamp->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $log->timestamp->format('h:i A') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $log->type === 'fingerprint' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                    <i class="fas {{ $log->type === 'fingerprint' ? 'fa-fingerprint' : 'fa-id-card' }} mr-1"></i>
                                    {{ ucfirst($log->type) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $log->device->device_id ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $log->device->model ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->location ?? 'Main Office' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $log->status === 'success' ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($log->type === 'Pin')
                                Card: {{ $log->type ?? 'N/A' }}
                            @else
                                Fingerprint verified
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-history text-4xl mb-3"></i>
                                <p class="text-lg font-medium text-gray-500">No biometric logs found</p>
                                <p class="text-sm mt-1">Your biometric access logs will appear here</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                </div>
                <div class="flex space-x-2">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Access Patterns -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Access Patterns</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Most Used Device</span>
                        <span class="text-sm text-gray-600">{{ $patterns['most_used_device'] ?? 'N/A' }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Preferred Method</span>
                        <span class="text-sm text-gray-600 capitalize">{{ $patterns['preferred_method'] ?? 'N/A' }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $patterns['method_percentage'] ?? 50 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Success Rate</span>
                        <span class="text-sm text-gray-600">{{ $patterns['success_rate'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $patterns['success_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-fingerprint text-blue-600 mr-3"></i>
                        <span class="text-sm font-medium text-blue-900">Last Fingerprint</span>
                    </div>
                    <span class="text-sm text-blue-700">
                        {{ $recentActivity['last_fingerprint'] ? $recentActivity['last_fingerprint']->diffForHumans() : 'Never' }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-id-card text-orange-600 mr-3"></i>
                        <span class="text-sm font-medium text-orange-900">Last RFID</span>
                    </div>
                    <span class="text-sm text-orange-700">
                        {{ $recentActivity['last_rfid'] ? $recentActivity['last_rfid']->diffForHumans() : 'Never' }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-door-open text-green-600 mr-3"></i>
                        <span class="text-sm font-medium text-green-900">Today's Accesses</span>
                    </div>
                    <span class="text-sm text-green-700">{{ $recentActivity['today_accesses'] }}</span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-3"></i>
                        <span class="text-sm font-medium text-purple-900">Average Daily</span>
                    </div>
                    <span class="text-sm text-purple-700">{{ $recentActivity['avg_daily'] }} accesses</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-question-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-blue-900">About Biometric Logs</h4>
                <p class="text-blue-800 mt-1">This page shows your complete biometric access history including fingerprint and RFID usage.</p>
                <div class="mt-3 flex flex-wrap gap-4 text-sm text-blue-700">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Logs are updated in real-time</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i>
                        <span>Data refreshes automatically</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        <span>Export logs for your records</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Issues -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-yellow-900">Notice Missing or Incorrect Logs?</h4>
                <p class="text-yellow-800 mt-1">If you notice any discrepancies in your biometric logs or missing entries, please report them immediately.</p>
                <div class="mt-4">
                    <button onclick="reportLogIssue()" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-flag mr-2"></i>
                        Report Log Issue
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">Log Details</h3>
                <button onclick="closeModal('logDetailsModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="logDetailsContent" class="mt-4">
                <!-- Dynamic content will be loaded here -->
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeModal('logDetailsModal')" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter functions
function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const logType = document.getElementById('logType').value;
    const status = document.getElementById('status').value;
    
let url = '{{ route("employee.biometric.logs") }}?';

    const params = new URLSearchParams();
    
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (logType) params.append('type', logType);
    if (status) params.append('status', status);
    
    window.location.href = url + params.toString();
}

function resetFilters() {
    window.location.href = '{{ route("employee.biometric.logs") }}';
}

function exportLogs() {
    showToast('Preparing export...', 'info');
    // Get current filters and add to export URL
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const logType = document.getElementById('logType').value;
    const status = document.getElementById('status').value;
    
    let url = '{{ route("employee.biometric.logs.export") }}?';
    const params = new URLSearchParams();
    
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (logType) params.append('type', logType);
    if (status) params.append('status', status);
    
    // Trigger download
    window.open(url + params.toString(), '_blank');
}

function viewLogDetails(logId) {
    // Fetch log details via AJAX
    fetch(`/employee/biometric-logs/${logId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('logDetailsContent').innerHTML = data.html;
            document.getElementById('logDetailsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching log details:', error);
            showToast('Error loading log details', 'error');
        });
}

function reportLogIssue() {
    showToast('Redirecting to issue reporting...', 'info');
    // Redirect to issue reporting page or open modal
    window.location.href = '?type=biometric';
}

// Modal functions
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'logDetailsModal') {
        closeModal('logDetailsModal');
    }
});

// Auto-refresh logs every 30 seconds (optional)
setInterval(() => {
    if (!document.hidden) {
        window.location.reload();
    }
}, 30000);

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Initialize date inputs with default values if empty
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    
    if (!startDate.value) {
        // Default to start of current month
        const firstDay = new Date();
        firstDay.setDate(1);
        startDate.value = firstDay.toISOString().split('T')[0];
    }
    
    if (!endDate.value) {
        // Default to today
        endDate.value = new Date().toISOString().split('T')[0];
    }
});
</script>

<style>
/* Additional styling for better UX */
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

.log-row {
    cursor: pointer;
}

.log-row:hover {
    background-color: #f9fafb;
}
</style>
@endpush