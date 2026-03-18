@extends('admin.layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Attendance Reports"
        description="Generate and view comprehensive attendance analysis"
    >
        <x-slot name="actions">
            <button onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-download"></i> Export CSV
            </button>
            <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-print"></i> Print
            </button>
        </x-slot>
    </x-admin.page-header>

    <!-- Report Filters Card -->
    <x-admin.card class="mt-8 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-blue-600"></i> Filter Reports
        </h3>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" id="startDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" id="endDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department_id" id="departmentFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select id="reportType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="summary">📊 Summary Report</option>
                    <option value="detailed">📋 Detailed Report</option>
                    <option value="department">🏢 Department Report</option>
                    <option value="employee">👤 Employee Report</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" onclick="generateReport()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <i class="fas fa-sync-alt mr-2"></i> Generate
                </button>
            </div>
        </form>
    </x-admin.card>

    <!-- Report Container -->
    <div id="reportContainer">
        <!-- Summary Report (Default) -->
        <div id="summaryReport" class="space-y-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Attendance Records</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $overallStats['total_records'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $overallStats['unique_employees'] }} unique employees</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-calendar text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Check-Ins</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $overallStats['total_checkins'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $overallStats['unique_employees'] ?? 0 }} employees</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-sign-in-alt text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Check-Outs</p>
                            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $overallStats['total_checkouts'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">Paired records</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <i class="fas fa-sign-out-alt text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Biometric Records</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $overallStats['biometric_count'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ round(($overallStats['biometric_count'] / max($overallStats['total_records'], 1)) * 100) }}% of total</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-fingerprint text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Manual Entries</p>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $overallStats['manual_count'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ round(($overallStats['manual_count'] / max($overallStats['total_records'], 1)) * 100) }}% of total</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <i class="fas fa-keyboard text-indigo-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Statistics Table -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Attendance Summary</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Metric</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Count</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-700">Total Records</td>
                                <td class="text-right py-3 px-4 font-semibold">{{ $overallStats['total_records'] }}</td>
                                <td class="text-right py-3 px-4">100%</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-700">
                                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>Check-In Records
                                </td>
                                <td class="text-right py-3 px-4 font-semibold">{{ $overallStats['total_checkins'] }}</td>
                                <td class="text-right py-3 px-4">{{ round(($overallStats['total_checkins'] / max($overallStats['total_records'], 1)) * 100, 1) }}%</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-700">
                                    <span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-2"></span>Check-Out Records
                                </td>
                                <td class="text-right py-3 px-4 font-semibold">{{ $overallStats['total_checkouts'] }}</td>
                                <td class="text-right py-3 px-4">{{ round(($overallStats['total_checkouts'] / max($overallStats['total_records'], 1)) * 100, 1) }}%</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-700">
                                    <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-2"></span>Biometric Source
                                </td>
                                <td class="text-right py-3 px-4 font-semibold">{{ $overallStats['biometric_count'] }}</td>
                                <td class="text-right py-3 px-4">{{ round(($overallStats['biometric_count'] / max($overallStats['total_records'], 1)) * 100, 1) }}%</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-700">
                                    <span class="inline-block w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>Manual Source
                                </td>
                                <td class="text-right py-3 px-4 font-semibold">{{ $overallStats['manual_count'] }}</td>
                                <td class="text-right py-3 px-4">{{ round(($overallStats['manual_count'] / max($overallStats['total_records'], 1)) * 100, 1) }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Average Attendance Insights -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Insights</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-sm text-gray-600 mb-2">Average Attendance Rate</p>
                        <p class="text-2xl font-bold text-blue-600">
                            @if($overallStats['unique_employees'] > 0)
                                {{ round(($overallStats['total_checkins'] / $overallStats['unique_employees']), 1) }}
                            @else
                                0
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">check-ins per employee</p>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-sm text-gray-600 mb-2">Data Quality</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $overallStats['total_checkins'] > 0 && $overallStats['total_checkouts'] > 0 ? 
                                min(round(($overallStats['total_checkouts'] / $overallStats['total_checkins']) * 100, 1), 100) : 0 }}%
                        </p>
                        <p class="text-xs text-gray-500 mt-1">matched check-out records</p>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <p class="text-sm text-gray-600 mb-2">System Adoption</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ round(($overallStats['biometric_count'] / max($overallStats['total_records'], 1)) * 100, 1) }}%
                        </p>
                        <p class="text-xs text-gray-500 mt-1">using biometric system</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Report -->
        <div id="detailedReport" class="hidden space-y-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-table mr-2 text-blue-600"></i>Employee Attendance Details
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Employee</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Department</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Check-Ins</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Check-Outs</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Biometric</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Manual</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Total</th>
                            </tr>
                        </thead>
                        <tbody id="detailedTableBody">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading detailed report...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-xs text-gray-500 text-right">
                    Last updated: <span id="detailsUpdateTime">now</span>
                </div>
            </div>
        </div>

        <!-- Department Report -->
        <div id="departmentReport" class="hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-building mr-2 text-blue-600"></i>Department Attendance Summary
                    </h3>
                    <div class="space-y-3" id="departmentList">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading department data...
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-chart-pie mr-2 text-purple-600"></i>Distribution
                    </h3>
                    <canvas id="departmentChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Employee Report -->
        <div id="employeeReport" class="hidden space-y-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-user mr-2 text-indigo-600"></i>Employee Attendance History
                </h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Employee</label>
                    <select id="employeeSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        onchange="loadEmployeeReport()">
                        <option value="">Choose an employee...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} - {{ $emp->department->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="employeeReportContent" class="hidden space-y-4">
                    <!-- Employee Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <p class="text-sm text-blue-700 font-medium">Total Records</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2" id="empTotal">0</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                            <p class="text-sm text-green-700 font-medium">Check-Ins</p>
                            <p class="text-3xl font-bold text-green-600 mt-2" id="empCheckins">0</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                            <p class="text-sm text-orange-700 font-medium">Check-Outs</p>
                            <p class="text-3xl font-bold text-orange-600 mt-2" id="empCheckouts">0</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                            <p class="text-sm text-purple-700 font-medium">Biometric %</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2" id="empBiometric">0%</p>
                        </div>
                    </div>

                    <!-- Employee Records Table -->
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Time</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Type</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Source</th>
                                </tr>
                            </thead>
                            <tbody id="empReportTable">
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">No records found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Print Styles -->
<style media="print">
    @page {
        size: A4;
        margin: 1cm;
    }
    
    body {
        background: white;
        margin: 0;
        padding: 0;
    }
    
    .hidden {
        display: none !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    
    th {
        background-color: #f3f4f6 !important;
        font-weight: bold;
    }
    
    h1, h2, h3 {
        color: #1f2937;
        page-break-after: avoid;
    }
    
    .page-break {
        page-break-before: always;
    }
</style>

<script>
    let departmentChart = null;
    let allEmployeesData = [];

    function generateReport() {
        const reportType = document.getElementById('reportType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const departmentId = document.getElementById('departmentFilter').value;

        // Hide all reports
        document.getElementById('summaryReport').classList.add('hidden');
        document.getElementById('detailedReport').classList.add('hidden');
        document.getElementById('departmentReport').classList.add('hidden');
        document.getElementById('employeeReport').classList.add('hidden');

        // Show selected report
        switch(reportType) {
            case 'detailed':
                document.getElementById('detailedReport').classList.remove('hidden');
                loadDetailedReport(startDate, endDate, departmentId);
                break;
            case 'department':
                document.getElementById('departmentReport').classList.remove('hidden');
                loadDepartmentReport(startDate, endDate);
                break;
            case 'employee':
                document.getElementById('employeeReport').classList.remove('hidden');
                break;
            default:
                document.getElementById('summaryReport').classList.remove('hidden');
        }
    }

    function loadDetailedReport(startDate, endDate, departmentId) {
        const tbody = document.getElementById('detailedTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Loading...</td></tr>';

        // Fetch detailed employee data
        const params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate,
            department_id: departmentId,
            detailed: true
        });

        fetch(`{{ route('admin.attendance.index') }}?${params}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract employee data from API or build from form
                buildDetailedTable(startDate, endDate, departmentId);
            })
            .catch(error => {
                console.error('Error loading detailed report:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-red-500">Error loading report</td></tr>';
            });
    }

    function buildDetailedTable(startDate, endDate, departmentId) {
        const tbody = document.getElementById('detailedTableBody');
        tbody.innerHTML = '';

        // Fetch attendance data grouped by employee
        const queryParams = new URLSearchParams();
        queryParams.append('start_date', startDate);
        queryParams.append('end_date', endDate);
        if (departmentId) queryParams.append('department_id', departmentId);

        fetch(`/api/attendance-summary?${queryParams}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => {
            if (!response.ok) {
                console.error('Response status:', response.status);
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        })
        .then(employees => {
            if (employees.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-gray-500">No data available</td></tr>';
                return;
            }

            employees.forEach(emp => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50 transition';
                row.innerHTML = `
                    <td class="py-3 px-4 text-gray-700 font-medium">${emp.employee_name}</td>
                    <td class="text-center py-3 px-4 text-gray-600">${emp.department}</td>
                    <td class="text-center py-3 px-4"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">${emp.checkins}</span></td>
                    <td class="text-center py-3 px-4"><span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-semibold">${emp.checkouts}</span></td>
                    <td class="text-center py-3 px-4"><span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold">${emp.biometric}</span></td>
                    <td class="text-center py-3 px-4"><span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-semibold">${emp.manual}</span></td>
                    <td class="text-center py-3 px-4 font-bold text-gray-900">${emp.total}</td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('detailsUpdateTime').textContent = new Date().toLocaleTimeString();
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: Show message
            tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-blue-500">Real-time data will load from API</td></tr>';
        });
    }

    function loadDepartmentReport(startDate, endDate) {
        // Fetch department summary data
        fetch(`{{ route('admin.attendance.department-summary') }}?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                const deptList = document.getElementById('departmentList');
                deptList.innerHTML = '';

                if (data.length === 0) {
                    deptList.innerHTML = '<div class="text-center py-8 text-gray-500">No department data available</div>';
                    return;
                }

                let maxCount = Math.max(...data.map(d => d.total_checkins), 1);

                data.forEach((dept, idx) => {
                    const percentage = (dept.total_checkins / maxCount) * 100;
                    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500', 'bg-indigo-500'];
                    const color = colors[idx % colors.length];
                    
                    deptList.innerHTML += `
                        <div class="space-y-2 pb-4 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full ${color}"></div>
                                    <span class="text-sm font-semibold text-gray-700">${dept.department_name}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-full">${dept.total_checkins}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="${color} h-2 rounded-full transition-all duration-500" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });

                // Update chart
                updateDepartmentChart(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('departmentList').innerHTML = '<div class="text-center py-8 text-red-500">Error loading department data</div>';
            });
    }

    function updateDepartmentChart(data) {
        const ctx = document.getElementById('departmentChart').getContext('2d');
        
        if (departmentChart) {
            departmentChart.destroy();
        }

        departmentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.department_name),
                datasets: [{
                    data: data.map(d => d.total_checkins),
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#EC4899',
                        '#14B8A6',
                        '#F97316',
                        '#6366F1'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15
                        }
                    }
                }
            }
        });
    }

    function loadEmployeeReport() {
        const employeeId = document.getElementById('employeeSelect').value;
        if (!employeeId) {
            document.getElementById('employeeReportContent').classList.add('hidden');
            return;
        }

        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Fetch employee attendance data
        fetch(`/api/employee-attendance/${employeeId}?start_date=${startDate}&end_date=${endDate}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
            .then(response => {
                if (!response.ok) {
                    console.error('Response status:', response.status);
                    throw new Error(`HTTP Error: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('employeeReportContent').classList.remove('hidden');
                
                // Update stats
                document.getElementById('empTotal').textContent = data.stats.total;
                document.getElementById('empCheckins').textContent = data.stats.checkins;
                document.getElementById('empCheckouts').textContent = data.stats.checkouts;
                document.getElementById('empBiometric').textContent = data.stats.biometric_percent + '%';

                // Populate table
                const tbody = document.getElementById('empReportTable');
                tbody.innerHTML = '';

                if (data.records.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-gray-500">No records found</td></tr>';
                    return;
                }

                data.records.forEach(record => {
                    const typeColor = record.type === 'in' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800';
                    const typeLabel = record.type === 'in' ? 'Check-in' : 'Check-out';
                    const sourceColor = record.source === 'biometric' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800';
                    const sourceLabel = record.source === 'biometric' ? 'Biometric' : 'Manual';
                    
                    const row = document.createElement('tr');
                    row.className = 'border-b border-gray-100 hover:bg-gray-50 transition';
                    row.innerHTML = `
                        <td class="py-3 px-4 text-gray-700 font-medium">${record.date}</td>
                        <td class="text-center py-3 px-4 text-gray-600 font-mono">${record.time}</td>
                        <td class="text-center py-3 px-4"><span class="px-3 py-1 rounded-full text-sm font-semibold ${typeColor}">${typeLabel}</span></td>
                        <td class="text-center py-3 px-4"><span class="px-3 py-1 rounded-full text-sm font-semibold ${sourceColor}">${sourceLabel}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error loading employee data:', error);
                alert('Error loading employee attendance data');
            });
    }

    function exportReport() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const departmentId = document.getElementById('departmentFilter').value;

        let url = `{{ route('admin.attendance.export') }}?start_date=${startDate}&end_date=${endDate}`;
        
        if (departmentId) {
            url += `&department_id=${departmentId}`;
        }

        window.location.href = url;
    }

    function printReport() {
        window.print();
    }

    // Initialize with summary report on page load
    document.addEventListener('DOMContentLoaded', function() {
        generateReport();
    });
</script>
@endsection
