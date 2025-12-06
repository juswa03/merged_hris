@extends('layouts.app')

@section('title', 'Enrolled Employees')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="enrolledEmployees()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Biometric Enrolled Employees</h1>
            <p class="text-gray-600">Manage employees with registered fingerprint data</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('biometric.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out flex items-center">
                <i class="fas fa-plus mr-2"></i> Enroll New Employee
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Enrolled</p>
                    <p class="text-2xl font-bold text-gray-800" x-text="employees.length"></p>
                </div>
            </div>
        </div>
        <!-- Add more stats if needed -->
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </span>
                <input type="text" x-model="search" placeholder="Search by name or ID..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Department Filter -->
            <div>
                <select x-model="department" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="employee in filteredEmployees" :key="employee.id">
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" :src="employee.photo_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(employee.first_name + ' ' + employee.last_name) + '&color=7F9CF5&background=EBF4FF'" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="employee.first_name + ' ' + employee.last_name"></div>
                                        <div class="text-sm text-gray-500" x-text="employee.employee_id || 'No ID'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800" x-text="employee.department ? employee.department.name : 'N/A'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="employee.position ? employee.position.name : 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(employee.fingerprint_template ? employee.fingerprint_template.created_at : null)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="removeEnrollment(employee.id)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors duration-150">
                                    <i class="fas fa-trash-alt mr-1"></i> Remove
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredEmployees.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                                <p>No enrolled employees found matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function enrolledEmployees() {
        return {
            search: '',
            department: '',
            employees: @json($enrolled_employees),
            
            get filteredEmployees() {
                return this.employees.filter(employee => {
                    const fullName = (employee.first_name + ' ' + employee.last_name).toLowerCase();
                    const empId = (employee.employee_id || '').toLowerCase();
                    const deptName = (employee.department ? employee.department.name : '').toLowerCase();
                    const searchLower = this.search.toLowerCase();
                    const deptFilter = this.department.toLowerCase();
                    
                    const matchesSearch = fullName.includes(searchLower) || empId.includes(searchLower);
                    const matchesDept = this.department === '' || deptName === deptFilter;
                    
                    return matchesSearch && matchesDept;
                });
            },

            formatDate(dateString) {
                if (!dateString) return 'N/A';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            },

            removeEnrollment(employeeId) {
                if (confirm('Are you sure you want to remove the biometric enrollment for this employee? This action cannot be undone.')) {
                    fetch(`/biometric/api/remove-enrollment/${employeeId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Enrollment removed successfully.');
                            // Remove from local list
                            this.employees = this.employees.filter(e => e.id !== employeeId);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing enrollment.');
                    });
                }
            }
        }
    }
</script>
@endsection
