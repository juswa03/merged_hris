@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="System Settings"
        description="Configure application settings and preferences"
    />

    <!-- Settings Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500">
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab active rounded-t-lg bg-blue-600 text-white">
                    <i class="fas fa-cog mr-2"></i>
                    General
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Attendance
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-calendar-minus mr-2"></i>
                    Leaves
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Payroll
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-envelope mr-2"></i>
                    Email
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Permissions
                </a>
            </li>
        </ul>
    </div>

    <!-- Success/Error Messages -->
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 hidden" id="successMessage" role="alert">
        <p id="successMessageText">Settings updated successfully!</p>
    </div>
    
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 hidden" id="errorMessage" role="alert">
        <p id="errorMessageText">There was an error saving your settings.</p>
    </div>

    <!-- Settings Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- System Status Card -->
        <div class="bg-white p-6 rounded-lg shadow settings-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-server mr-2 text-blue-600"></i>
                    System Status
                </h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Operational
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Last Backup</span>
                    <span class="font-medium">Yesterday, 2:30 AM</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">System Version</span>
                    <span class="font-medium">v2.5.3</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Database Size</span>
                    <span class="font-medium">345 MB</span>
                </div>
                <button class="mt-4 w-full py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-database mr-2"></i>
                    Create Backup
                </button>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white p-6 rounded-lg shadow settings-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-bolt mr-2 text-blue-600"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <button class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Reset Cache
                </button>
                <button class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-tasks mr-2"></i>
                    Run Maintenance
                </button>
                <button class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-key mr-2"></i>
                    Update API Keys
                </button>
                <button class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-eye mr-2"></i>
                    Audit Logs
                </button>
                <button class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Security Scan
                </button>
                <button class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-terminal mr-2"></i>
                    CLI Settings
                </button>
            </div>
        </div>

        <!-- System Configuration Card -->
        <div class="bg-white p-6 rounded-lg shadow settings-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-sliders-h mr-2 text-blue-600"></i>
                System Configuration
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-700">Dark Mode</span>
                        <div class="relative">
                            <input type="checkbox" id="darkModeToggle" class="toggle-checkbox sr-only">
                            <div class="toggle-label block w-10 h-6 bg-gray-200 rounded-full"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-700">Email Notifications</span>
                        <div class="relative">
                            <input type="checkbox" id="emailToggle" class="toggle-checkbox sr-only" checked>
                            <div class="toggle-label block w-10 h-6 bg-blue-600 rounded-full"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-4"></div>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-700">Two-Factor Auth</span>
                        <div class="relative">
                            <input type="checkbox" id="twoFactorToggle" class="toggle-checkbox sr-only">
                            <div class="toggle-label block w-10 h-6 bg-gray-200 rounded-full"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-700">Maintenance Mode</span>
                        <div class="relative">
                            <input type="checkbox" id="maintenanceToggle" class="toggle-checkbox sr-only">
                            <div class="toggle-label block w-10 h-6 bg-gray-200 rounded-full"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                        </div>
                    </label>
                </div>
                <div class="pt-2">
                    <button class="w-full py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-save mr-2"></i>
                        Save Configuration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- General Settings Form -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-university mr-2 text-blue-600"></i>
            University Information
        </h3>
        <form class="settings-form space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="universityName" class="block text-sm font-medium text-gray-700">University Name</label>
                    <input type="text" id="universityName" name="universityName" value="Biliran Province State University" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                </div>
                <div>
                    <label for="universityAcronym" class="block text-sm font-medium text-gray-700">Acronym</label>
                    <input type="text" id="universityAcronym" name="universityAcronym" value="BIPSU" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                </div>
            </div>
            
            <div>
                <label for="universityAddress" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea id="universityAddress" name="universityAddress" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">Naval, Biliran, Philippines</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="universityEmail" class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" id="universityEmail" name="universityEmail" value="info@bipsu.edu.ph" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                </div>
                <div>
                    <label for="universityPhone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" id="universityPhone" name="universityPhone" value="+63 999 999 9999" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                </div>
                <div>
                    <label for="universityWebsite" class="block text-sm font-medium text-gray-700">Website</label>
                    <input type="url" id="universityWebsite" name="universityWebsite" value="https://www.bipsu.edu.ph" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Academic Settings -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-graduation-cap mr-2 text-blue-600"></i>
            Academic Settings
        </h3>
        <form class="settings-form space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="academicYear" class="block text-sm font-medium text-gray-700">Current Academic Year</label>
                    <select id="academicYear" name="academicYear" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                        <option>2023-2024</option>
                        <option>2022-2023</option>
                        <option>2021-2022</option>
                    </select>
                </div>
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700">Current Semester</label>
                    <select id="semester" name="semester" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                        <option>Second Semester</option>
                        <option>First Semester</option>
                        <option>Summer</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="departments" class="block text-sm font-medium text-gray-700">Departments</label>
                <select id="departments" name="departments" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border min-h-[100px]">
                    <option selected>College of Arts and Sciences</option>
                    <option selected>College of Business Management</option>
                    <option selected>College of Education</option>
                    <option selected>College of Engineering</option>
                    <option selected>College of Industrial Technology</option>
                    <option selected>College of Nursing</option>
                    <option>College of Agriculture</option>
                    <option>College of Fisheries</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple departments</p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Calendar Settings -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
            Academic Calendar
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">First Day of Classes</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">August 15, 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">August 15, 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="ml-2 text-red-600 hover:text-red-900">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Midterm Examinations</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">September 25, 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">September 29, 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="ml-2 text-red-600 hover:text-red-900">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Christmas Break</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">December 20, 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">January 2, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="ml-2 text-red-600 hover:text-red-900">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus-circle mr-2"></i>
                Add New Event
            </button>
        </div>
    </div>
</div>
    
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle profile dropdown toggle
        const dropdown = document.querySelector('.dropdown');
        if (dropdown) {
            dropdown.addEventListener('click', function(e) {
                if (e.target.closest('.dropdown-menu')) return;
                this.querySelector('.dropdown-menu').classList.toggle('hidden');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                const openDropdown = document.querySelector('.dropdown-menu:not(.hidden)');
                if (openDropdown) openDropdown.classList.add('hidden');
            }
        });

        // Toggle Sidebar Functionality
        const toggleSidebar = () => {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            
            if (!sidebar || !toggleBtn) return;
            
            sidebar.classList.toggle('collapsed');
            
            const icon = toggleBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                toggleBtn.querySelector('.nav-text').textContent = 'Expand';
            } else {
                icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
                toggleBtn.querySelector('.nav-text').textContent = 'Collapse';
            }
            
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        };
                
        // Mobile sidebar toggle
        const toggleMobileSidebar = () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('mobile-open');
            
            // On mobile, we don't want the collapsed state when opening
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('collapsed');
            }
        };
                
        // Initialize sidebar state from localStorage
        const initializeSidebar = () => {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            
            if (!sidebar || !toggleBtn) return;
            
            // Only apply collapsed state on desktop
            if (window.innerWidth > 768) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    const icon = toggleBtn.querySelector('i');
                    icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                    toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                }
            }
            
            // Set up event listeners
            toggleBtn.addEventListener('click', toggleSidebar);
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
            }
            
            // Close mobile sidebar when clicking outside
            document.addEventListener('click', function(e) {
                const sidebar = document.getElementById('sidebar');
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                
                if (window.innerWidth <= 768 && 
                    !e.target.closest('#sidebar') && 
                    !e.target.closest('#mobileMenuBtn') &&
                    sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        };
                
        // Handle window resize
        const handleResize = () => {
            const sidebar = document.getElementById('sidebar');
            
            if (window.innerWidth > 768) {
                // Desktop - remove mobile-open class if it exists
                sidebar.classList.remove('mobile-open');
            } else {
                // Mobile - ensure sidebar is hidden by default
                if (!sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('collapsed');
                }
            }
        };
                
        // Initialize on page load
        initializeSidebar();
                
        // Add resize event listener
        window.addEventListener('resize', handleResize);

        // Settings tab functionality
        const settingsTabs = document.querySelectorAll('.settings-tab');
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active class from all tabs
                settingsTabs.forEach(t => t.classList.remove('active', 'bg-blue-600', 'text-white'));
                // Add active class to clicked tab
                this.classList.add('active', 'bg-blue-600', 'text-white');
            });
        });

        // Toggle switch functionality
        const toggleSwitches = document.querySelectorAll('.toggle-checkbox');
        toggleSwitches.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const label = this.nextElementSibling;
                const dot = label.nextElementSibling;
                
                if (this.checked) {
                    label.classList.add('bg-blue-600');
                    dot.classList.add('translate-x-4');
                } else {
                    label.classList.remove('bg-blue-600');
                    dot.classList.remove('translate-x-4');
                }
            });
        });

        // Form submission handling
        const forms = document.querySelectorAll('form.settings-form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const successMessage = document.getElementById('successMessage');
                successMessage.classList.remove('hidden');
                
                // Hide success message after 5 seconds
                setTimeout(() => {
                    successMessage.classList.add('hidden');
                }, 5000);
            });
        });

        // Simulate loading for settings cards
        const settingsCards = document.querySelectorAll('.settings-card');
        settingsCards.forEach(card => {
            // Add click animation
            card.addEventListener('click', function() {
                this.classList.add('transform', '-translate-y-2');
                setTimeout(() => {
                    this.classList.remove('transform', '-translate-y-2');
                }, 300);
            });
        });
    });
</script>
@endpush
@endsection