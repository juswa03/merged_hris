<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIPSU HRMIS - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/logos/uni_logo.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
    <style>
        .sidebar {
            transition: all 0.3s ease;
            width: 250px;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .logo-text {
            display: none;
        }
        
        .sidebar.collapsed .submenu {
            display: none !important;
        }
        
        .sidebar.collapsed .nav-item:hover .submenu {
            display: block !important;
            position: fixed;
            left: 70px;
            background: #1e40af;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            min-width: 200px;
        }
        
        .sidebar.collapsed .nav-item:hover .submenu::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 20px;
            width: 0;
            height: 0;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-right: 5px solid #1e40af;
        }
        
        .main-content {
            transition: all 0.3s ease;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
        
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .submenu.open {
            max-height: 500px;
            transition: max-height 0.3s ease-in;
        }
        
        .submenu-item {
            position: relative;
            padding-left: 1rem;
        }

        
        .nav-item.has-submenu > a .nav-arrow {
            transition: transform 0.2s ease;
        }
        
        .nav-item.has-submenu.open > a .nav-arrow {
            transform: rotate(90deg);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar.collapsed ~ .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        /* Employee specific styles */
        .quick-action-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .quick-action-card.check-in {
            border-left-color: #10b981;
        }
        
        .quick-action-card.check-out {
            border-left-color: #ef4444;
        }
        
        .quick-action-card.payslip {
            border-left-color: #8b5cf6;
        }
        
        .quick-action-card.biometric {
            border-left-color: #f59e0b;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-present {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-absent {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-late {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-on-leave {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .biometric-scanner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .attendance-timeline {
            position: relative;
        }
        
        .attendance-timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Mobile menu button -->
        <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-800 text-white rounded-lg shadow-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-blue-800 text-white fixed h-full overflow-y-auto">
            <div class="p-4 flex items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logos/uni_logo.png') }}" alt="BIPSU Logo" 
                        class="h-12 w-auto object-contain max-w-full transition-all duration-300">
                    <span class="logo-text text-xl font-bold whitespace-nowrap">eHRMIS</span>
                </div>
            </div>
            
            <!-- Employee Quick Info -->
            <div class="px-4 py-3 border-t border-blue-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ auth()->user()->employee->photo_url ?? asset('images\icons\nikol.jpg') }}" 
                         alt="Profile" 
                         style="background-color: lightblue"
                         class="w-10 h-10 rounded-full border-2 border-blue-600">
                    <div class="nav-text">
                        <p class="font-semibold text-sm">{{ auth()->user()->employee->first_name ?? auth()->user()->name }}</p>
                        <p class="text-blue-200 text-xs">{{ auth()->user()->employee->position->name ?? 'Employee' }}</p>
                    </div>
                </div>
            </div>

            <nav class="mt-4">
                <!-- Dashboard -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route ('dashboard')}}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>

                <!-- DTR & Attendance -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-calendar-alt mr-3 w-5"></i>
                        <span class="nav-text">My Attendance</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route ('employee.dtr.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Daily Time Record</span>
                        </a>
                        <a href="{{ route('my-attendance') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Attendance History</span>
                        </a>
                        <a href="" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Request Adjustment</span>
                        </a>
                    </div>
                </div>

                <!-- Biometric -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-fingerprint mr-3 w-5"></i>
                        <span class="nav-text">Biometric</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        {{-- <a href="" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Enroll Biometric</span>
                        </a> --}}
                        <a href="{{ route('employee.biometric.status') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Biometric Status</span>
                        </a>
                        <a href="{{ route ('employee.biometric.logs') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Biometric Logs</span>
                        </a>
                    </div>
                </div>

                <!-- Payroll -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-money-bill-wave mr-3 w-5"></i>
                        <span class="nav-text">My Payroll</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route ('employee.payroll.payslips') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payslips</span>
                        </a>
                        <a href="{{ route ('employee.payroll.history') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll History</span>
                        </a>
                        <a href="{{ route ('employee.payroll.deductions') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Deductions & Benefits</span>
                        </a>
                        <a href="{{ route ('employee.payroll.tax-info') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Tax Information</span>
                        </a>
                    </div>
                </div>

                <!-- Profile -->
                <div class="nav-item px-4 py-1">
                    <a href="" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user mr-3 w-5"></i>
                        <span class="nav-text">My Profile</span>
                    </a>
                </div>

                <!-- Documents -->
                <div class="nav-item px-4 py-1">
                    <a href="" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-alt mr-3 w-5"></i>
                        <span class="nav-text">My Documents</span>
                    </a>
                </div>
            </nav>
            
            <!-- Sidebar Toggle -->
            <div class="p-4 mt-auto">
                <button id="toggleSidebar" class="w-full flex items-center justify-center py-2 bg-blue-700 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                    <span class="nav-text ml-2">Collapse</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center p-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">@yield('title')</h1>
                        @hasSection('subtitle')
                            <p class="text-gray-600 text-sm mt-1">@yield('subtitle')</p>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Quick Check-in/out Status -->
                        @if(View::hasSection('attendance-status'))
                            @yield('attendance-status')
                        @else
                            @php
                                // Get employee and latest DTR entry safely
                                $employee = auth()->user()->employee ?? null;
                                $status = $employee?->latestDtrEntry?->status ?? 'loading';

                                // Define color themes per status
                                $themes = [
                                    'present'   => ['bg' => 'bg-green-100', 'dot' => 'bg-green-500', 'text' => 'text-green-700', 'label' => 'Present today'],
                                    'absent'    => ['bg' => 'bg-red-100', 'dot' => 'bg-red-500', 'text' => 'text-red-700', 'label' => 'Absent today'],
                                ];
                                
                                $theme = $status? $themes['present'] : $theme['absent'];
                            @endphp

                            <div class="hidden md:flex items-center space-x-2 px-3 py-2 rounded-lg {{ $theme['bg'] }}">
                                <div class="w-2 h-2 rounded-full {{ $theme['dot'] }} animate-pulse"></div>
                                <span class="text-sm {{ $theme['text'] }}">
                                    {{ $theme['label'] }}
                                </span>
                            </div>
                        @endif


                        <!-- Notifications -->
                        @include('components.notification_dropdown')
                        
                        <!-- Profile Dropdown -->
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer space-x-2 bg-gray-50 hover:bg-gray-100 rounded-lg px-3 py-2 transition-colors">
                                
                                <img src="{{ auth()->user()->employee->photo_url ?? asset('images\icons\nikol.jpg') }}" 
                                     alt="Profile" 
                                     class="w-8 h-8 rounded-full border">
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->employee->first_name ?? auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->employee->employee_id ?? 'Employee' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 hidden border">
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-3 w-4"></i> My Profile
                                </a>
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-3 w-4"></i> Settings
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600">
                                        <i class="fas fa-sign-out-alt mr-3 w-4"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Quick Action Cards (shown on dashboard) -->
                @if(Request::is('employee/dashboard'))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="quick-action-card check-in bg-white rounded-lg shadow-sm p-4 border cursor-pointer" onclick="checkIn()">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Check In</p>
                                <p class="text-lg font-semibold text-gray-800">Start Work</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <i class="fas fa-fingerprint text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="quick-action-card check-out bg-white rounded-lg shadow-sm p-4 border cursor-pointer" onclick="checkOut()">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Check Out</p>
                                <p class="text-lg font-semibold text-gray-800">End Work</p>
                            </div>
                            <div class="p-3 bg-red-100 rounded-full">
                                <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <a href="" class="quick-action-card payslip bg-white rounded-lg shadow-sm p-4 border hover:no-underline">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Payslip</p>
                                <p class="text-lg font-semibold text-gray-800">View Salary</p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full">
                                <i class="fas fa-file-invoice-dollar text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </a>

                    <a href="" class="quick-action-card biometric bg-white rounded-lg shadow-sm p-4 border hover:no-underline">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Biometric</p>
                                <p class="text-lg font-semibold text-gray-800">Enroll Now</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <i class="fas fa-fingerprint text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <p class="text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                            <div>
                                <p class="text-red-700 font-medium">Please fix the following errors:</p>
                                <ul class="text-red-600 text-sm mt-1 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @push('scripts')
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

        // Submenu toggle functionality
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                const navItem = this.closest('.nav-item');
                const submenu = navItem.querySelector('.submenu');
                
                // Close other open submenus
                document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
                    if (item !== navItem) {
                        item.classList.remove('open');
                        item.querySelector('.submenu').classList.remove('open');
                    }
                });
                
                // Toggle current submenu
                navItem.classList.toggle('open');
                submenu.classList.toggle('open');
            });
        });

        // Toggle Sidebar Functionality
        const toggleSidebar = () => {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            
            if (!sidebar || !toggleBtn) return;
            
            sidebar.classList.toggle('collapsed');
            
            // Close all submenus when collapsing
            if (sidebar.classList.contains('collapsed')) {
                document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
                    item.classList.remove('open');
                    item.querySelector('.submenu').classList.remove('open');
                });
            }
            
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
    });

    // Initialize dropdowns function
    function initializeDropdowns() {
        // Handle profile dropdown toggle
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (e.target.closest('.dropdown-menu')) return;
                const menu = this.querySelector('.dropdown-menu');
                if (menu) menu.classList.toggle('hidden');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            dropdowns.forEach(dropdown => {
                if (!e.target.closest('.dropdown')) {
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (menu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                    }
                }
            });
        });
    }

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } z-50`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Check In/Out functions (placeholder - update with your actual routes)
    window.checkIn = function() {
        fetch('/employee/check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadAttendanceStatus();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Check-in failed. Please try again.', 'error');
        });
    };

    window.checkOut = function() {
        fetch('/employee/check-out', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadAttendanceStatus();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Check-out failed. Please try again.', 'error');
        });
    };

    function loadAttendanceStatus() {
        fetch('/employee/attendance-status')
            .then(response => response.json())
            .then(data => {
                const statusElement = document.getElementById('attendanceStatus');
                if (statusElement) {
                    statusElement.textContent = data.status;
                    // Update status dot color
                    const dot = statusElement.previousElementSibling;
                    if (dot) {
                        dot.className = 'w-2 h-2 rounded-full ' + 
                            (data.status.includes('Checked in') ? 'bg-green-500' : 
                             data.status.includes('Checked out') ? 'bg-red-500' : 'bg-gray-400');
                    }
                }
            });
    }
</script>

@endpush
    @stack('scripts')
</body>
</html>