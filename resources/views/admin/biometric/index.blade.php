@extends('layouts.app')

@section('title', 'Biometric Enrollment')

@section('content')
    <style>
        [x-cloak] { display: none !important; }
        
        .pulse-animation {
            animation: pulse-custom 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse-custom {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }

        /* Added missing pulse animation for status indicator */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .fingerprint-scanner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .enrollment-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .enrollment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }
        
        .status-indicator {
            position: relative;
        }
        
        .status-indicator.connecting::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #fbbf24;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .status-indicator.connected::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
        }
        
        .status-indicator.disconnected::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }
    </style>
    
    <div class="min-h-screen" x-data="biometricEnrollmentModule()" x-init="init()" x-cloak>
        <!-- Header with Connection Status -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-fingerprint text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-900">Biometric Enrollment</h1>
                            <p class="text-sm text-gray-500">Employee fingerprint registration system</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('biometric.enrolled') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out flex items-center shadow-sm">
                            <i class="fas fa-list mr-2"></i> View Enrolled
                        </a>
                        <!-- Stats -->
                        <div class="bg-blue-50 px-4 py-2 rounded-lg">
                            <span class="text-sm font-medium text-blue-700">
                                <i class="fas fa-users mr-2"></i>
                                <span x-text="pendingEnrollments"></span> Pending
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input 
                                x-model="searchQuery" 
                                @input="debounceFilter()"
                                type="text" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" 
                                placeholder="Search by name, ID, or department..."
                                aria-label="Search employees"
                            >
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <select 
                            x-model="selectedDepartment" 
                            @change="filterEmployees()"
                            class="block w-48 pl-3 pr-10 py-3 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg transition duration-150"
                            aria-label="Filter by department"
                        >
                            <option value="">All Departments</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                        
                        <button 
                            @click="refreshEmployeeList()"
                            :disabled="isLoading"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 disabled:opacity-50 text-gray-700 rounded-lg transition duration-150 flex items-center"
                            aria-label="Refresh employee list"
                        >
                            <i class="fas fa-sync-alt mr-2" :class="{'animate-spin': isLoading}"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Employees</p>
                            <p class="text-2xl font-semibold text-gray-900" x-text="totalEmployees"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Enrolled</p>
                            <p class="text-2xl font-semibold text-gray-900" x-text="enrolledCount"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900" x-text="pendingEnrollments"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Enrollment Rate</p>
                            <p class="text-2xl font-semibold text-gray-900" x-text="enrollmentRate + '%'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-list-ul mr-2"></i>
                            Employees Pending Enrollment
                            <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full" 
                                  x-text="filteredEmployees.length + ' employees'"></span>
                        </h3>
                    </div>
                </div>

                <!-- Employee Grid -->
                <div class="p-6">
                    <template x-if="isLoading">
                        <div class="flex justify-center py-12" role="status" aria-live="polite">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600" aria-hidden="true"></div>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </template>
                    
                    <template x-if="!isLoading && filteredEmployees.length > 0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="employee in paginatedEmployees" :key="employee.id">
                                <div class="enrollment-card bg-white rounded-lg p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-sm font-semibold text-gray-900" 
                                                    x-text="employee.first_name + ' ' + employee.last_name"></h4>
                                                <p class="text-xs text-gray-500" x-text="'ID: ' + (employee.employee_id || employee.id)"></p>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-building w-4 text-gray-400 mr-2"></i>
                                            <span x-text="employee.department?.name || 'N/A'"></span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-briefcase w-4 text-gray-400 mr-2"></i>
                                            <span x-text="employee.position?.name || 'N/A'"></span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-envelope w-4 text-gray-400 mr-2"></i>
                                            <span x-text="employee.user?.email || 'N/A'"></span>
                                        </div>
                                    </div>
                                    
                                    <button 
                                        @click="initiateEnrollment(employee)"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition duration-150 flex items-center justify-center group"
                                        aria-label="Enroll biometric for employee"
                                    >
                                        <i class="fas fa-fingerprint mr-2 group-hover:scale-110 transition-transform"></i>
                                        Enroll Biometric
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <template x-if="!isLoading && filteredEmployees.length === 0">
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">All Set!</h3>
                            <p class="text-gray-500">
                                <template x-if="searchQuery || selectedDepartment">
                                    <span>No employees match your search criteria.</span>
                                </template>
                                <template x-if="!searchQuery && !selectedDepartment">
                                    <span>All employees have completed biometric enrollment.</span>
                                </template>
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Biometric Enrollment Modal -->
        <div 
            x-show="isBiometricModalOpen" 
            @keydown.escape.window="closeBiometricModal()"
            @click.away="closeBiometricModal()"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300"
            style="display: none;"
            x-transition
            role="dialog"
            aria-modal="true"
            aria-labelledby="biometric-modal-title"
        >
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-xl shadow-lg rounded-md bg-white" @click.stop>
                <!-- Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 id="biometric-modal-title" class="text-xl font-semibold text-gray-900">
                        Biometric Registration
                    </h3>
                    <button @click="closeBiometricModal()" class="text-gray-500 hover:text-gray-700" aria-label="Close modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="mt-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-fingerprint text-5xl text-purple-600 animate-pulse"></i>
                        <p class="text-sm text-gray-600 mt-2">
                            Please instruct <span class="font-semibold text-gray-800" x-text="employeeForBiometric ? employeeForBiometric.first_name + ' ' + employeeForBiometric.last_name : ''"></span>
                            to place their finger on the biometric scanner.
                        </p>
                    </div>

                    <div class="mt-4">
                        <p class="text-gray-500 text-sm">
                            This process will securely register their fingerprint using the selected device.
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 flex justify-end">
                    <button 
                        @click="closeBiometricModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>

@push('scripts')
    <script>
        function biometricEnrollmentModule() {
    return {
        // Employee data - Fixed to use correct backend variables
        allEmployees: @json($all_employees ?? []),
        enrollmentStats: @json($enrollment_stats ?? []),

        // Filter and pagination
        searchQuery: '',
        selectedDepartment: '',
        currentPage: 1,
        pageSize: 9,
        searchTimeout: null,
        isLoading: false,
        
        // Modal states
        isBiometricModalOpen: false,
        employeeForBiometric: null,
        isDeviceSelectionOpen: false,
        selectedDeviceId: null,
        biometricStatus: '',
        availableDevices: [
            { id: 'device1', name: 'ZKTeco Live10R' },
            { id: 'device2', name: 'Suprema BioMini' },
        ],

        // Computed properties
        get totalEmployees() {
            return this.allEmployees.length || 0;
        },
        
        get enrollmentRate() {
            if (this.enrollmentStats.total_employees === 0) return 0;
            return this.enrollmentStats.enrollment_percentage || 0;
        },
        
        get enrolledCount() {
            return this.enrollmentStats.enrolled_count || 0;
        },
        
        get pendingEnrollments() {
            return this.enrollmentStats.pending_count || this.allEmployees.length;
        },
        
        get filteredEmployees() {
            let filtered = [...this.allEmployees];
            
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(emp => 
                    (emp.first_name && emp.first_name.toLowerCase().includes(query)) || 
                    (emp.last_name && emp.last_name.toLowerCase().includes(query)) ||
                    (emp.employee_id && emp.employee_id.toLowerCase().includes(query)) ||
                    (emp.user && emp.user.email && emp.user.email.toLowerCase().includes(query)) ||
                    (emp.department && emp.department.name && emp.department.name.toLowerCase().includes(query))
                );
            }
            
            if (this.selectedDepartment) {
                filtered = filtered.filter(emp => 
                    emp.department && emp.department.name === this.selectedDepartment
                );
            }
            
            return filtered;
        },
        
        get totalPages() {
            return Math.ceil(this.filteredEmployees.length / this.pageSize);
        },
        
        get visiblePages() {
            const pages = [];
            const maxVisible = 5;
            let startPage = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(this.totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage + 1 < maxVisible) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            
            return pages;
        },
        
        get paginatedEmployees() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredEmployees.slice(start, start + this.pageSize);
        },

        // Methods
        debounceFilter() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.filterEmployees();
            }, 300);
        },
        
        filterEmployees() {
            this.currentPage = 1;
        },
        
        async refreshEmployeeList() {
            this.isLoading = true;
            try {
                // Fetch fresh data from server
                const response = await fetch('/biometric/api/unenrolled-employees', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error:', response.status, errorText);
                    throw new Error(`Failed to fetch employee list (${response.status})`);
                }

                const data = await response.json();

                // Update employee list
                this.allEmployees = data.employees || [];

                // Update stats if provided
                if (data.statistics) {
                    this.enrollmentStats = {
                        total_employees: data.statistics.total_employees || 0,
                        enrolled_count: data.statistics.enrolled_employees || 0,
                        pending_count: data.statistics.pending_enrollments || 0,
                        enrollment_percentage: data.statistics.enrollment_percentage || 0
                    };
                }

                if (typeof showToast === 'function') {
                    showToast('Employee list refreshed successfully', 'success');
                }
                console.log('Employee list refreshed:', this.allEmployees.length, 'pending employees');
            } catch (error) {
                console.error('Failed to refresh employee list:', error);
                if (typeof showToast === 'function') {
                    showToast(error.message || 'Failed to refresh employee list', 'error');
                } else {
                    alert(error.message || 'Failed to refresh employee list');
                }
            } finally {
                this.isLoading = false;
            }
        },
        
        initiateEnrollment(employee) {
            if (employee) {
                this.employeeForBiometric = employee;
                this.selectedDeviceId = null;
                this.isBiometricModalOpen = true;
                console.log('Initiating enrollment for:', employee);
                fetch('/api/biometric/start-enrollment', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        employee_id: String(this.employeeForBiometric.id)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Biometric registration triggered successfully.');
                    } else {
                        throw new Error(data.message || 'Failed to trigger biometric registration.');
                    }
                })
                .catch(error => {
                    showToast(error.message, 'error');
                    console.error('Error:', error);
                });
            }
        },

        
        closeBiometricModal() {
            this.isBiometricModalOpen = false;
            this.employeeForBiometric = null;
        },
        
        init() {
            console.log('Biometric Enrollment Module initialized');
            console.log('Total unenrolled employees loaded:', this.allEmployees.length);
            console.log('Enrollment stats:', this.enrollmentStats);
            if (this.allEmployees.length > 0) {
                console.log('Sample employee data:', this.allEmployees[0]);
            } else {
                console.log('No unenrolled employees found - all employees are enrolled or no active employees exist');
            }
        }
    };
}
        
    </script>
@endpush
@endsection