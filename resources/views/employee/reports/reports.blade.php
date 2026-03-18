@extends('employee.layouts.app')

@section('title', 'My Reports')

@section('content')

<main class="p-6">
    <!-- Employee Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Attendance This Month</p>
                    <h3 class="text-2xl font-bold text-gray-800">92%</h3>
                    <p class="text-green-500 text-sm mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 3% from last month
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-calendar-check text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Remaining Leave Days</p>
                    <h3 class="text-2xl font-bold text-gray-800">12</h3>
                    <p class="text-red-500 text-sm mt-1">
                        <i class="fas fa-arrow-down mr-1"></i> 2 used this year
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-umbrella-beach text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="report-card bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Next Pay Date</p>
                    <h3 class="text-2xl font-bold text-gray-800">May 28</h3>
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-clock mr-1"></i> 5 days remaining
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Reports Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">My Reports</h2>
        </div>
        
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Attendance Report Card -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-calendar-check text-blue-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">My Attendance</h3>
                            <p class="text-sm text-gray-600 mt-1">View your attendance records</p>
                            <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Report <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Leave Report Card -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <i class="fas fa-umbrella-beach text-green-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">My Leave History</h3>
                            <p class="text-sm text-gray-600 mt-1">View your leave applications and balances</p>
                            <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Report <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Payroll Report Card -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="bg-purple-100 p-3 rounded-full mr-4">
                            <i class="fas fa-money-bill-wave text-purple-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">My Payslips</h3>
                            <p class="text-sm text-gray-600 mt-1">View and download your payslips</p>
                            <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Report <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Tax Report Card -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="bg-yellow-100 p-3 rounded-full mr-4">
                            <i class="fas fa-file-invoice-dollar text-yellow-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">My Tax Documents</h3>
                            <p class="text-sm text-gray-600 mt-1">View your tax forms and deductions</p>
                            <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Report <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Training Report Card -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="bg-red-100 p-3 rounded-full mr-4">
                            <i class="fas fa-graduation-cap text-red-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">My Training Records</h3>
                            <p class="text-sm text-gray-600 mt-1">View your completed training programs</p>
                            <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Report <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Performance Report Card -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="bg-indigo-100 p-3 rounded-full mr-4">
                            <i class="fas fa-chart-line text-indigo-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">My Performance Reviews</h3>
                            <p class="text-sm text-gray-600 mt-1">View your performance evaluations</p>
                            <button class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Report <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Monthly Attendance Chart -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">My Monthly Attendance</h2>
                <select class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Last 3 Months</option>
                    <option>Last 6 Months</option>
                    <option>This Year</option>
                </select>
            </div>
            <div class="chart-container" style="height: 300px;">
                <canvas id="myAttendanceChart"></canvas>
            </div>
        </div>
        
        <!-- Leave Balance Chart -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">My Leave Balances</h2>
                <select class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Current Year</option>
                    <option>Last Year</option>
                </select>
            </div>
            <div class="chart-container" style="height: 300px;">
                <canvas id="myLeaveChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recently Accessed Reports -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recently Accessed Reports</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Report Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Viewed
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-blue-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Attendance - April 2023</div>
                                    <div class="text-sm text-gray-500">PDF</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Apr 30, 2023</div>
                            <div class="text-sm text-gray-500">3:45 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-invoice-dollar text-green-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Payslip - March 2023</div>
                                    <div class="text-sm text-gray-500">PDF</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Mar 31, 2023</div>
                            <div class="text-sm text-gray-500">5:20 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-umbrella-beach text-purple-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Leave Balance</div>
                                    <div class="text-sm text-gray-500">PDF</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Mar 15, 2023</div>
                            <div class="text-sm text-gray-500">10:15 AM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-alt text-yellow-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Tax Form - 2022</div>
                                    <div class="text-sm text-gray-500">PDF</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Jan 30, 2023</div>
                            <div class="text-sm text-gray-500">2:45 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">12</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            1
                        </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            2
                        </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            3
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        const initCharts = () => {
            // My Attendance Chart
            const myAttendanceCtx = document.getElementById('myAttendanceChart').getContext('2d');
            const myAttendanceChart = new Chart(myAttendanceCtx, {
                type: 'bar',
                data: {
                    labels: ['March', 'April', 'May'],
                    datasets: [
                        {
                            label: 'Present',
                            data: [22, 21, 15],
                            backgroundColor: '#10B981',
                            borderColor: '#10B981',
                            borderWidth: 1
                        },
                        {
                            label: 'Absent',
                            data: [0, 1, 0],
                            backgroundColor: '#EF4444',
                            borderColor: '#EF4444',
                            borderWidth: 1
                        },
                        {
                            label: 'Late',
                            data: [2, 1, 3],
                            backgroundColor: '#F59E0B',
                            borderColor: '#F59E0B',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
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

            // My Leave Chart
            const myLeaveCtx = document.getElementById('myLeaveChart').getContext('2d');
            const myLeaveChart = new Chart(myLeaveCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Vacation', 'Sick', 'Personal'],
                    datasets: [{
                        data: [8, 4, 2],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#8B5CF6'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value} days`;
                                }
                            }
                        }
                    }
                }
            });
        };

        // Initialize charts when page loads
        initCharts();
    });
</script>
@endpush
@endsection