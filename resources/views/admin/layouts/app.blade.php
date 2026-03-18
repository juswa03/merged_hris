<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BIPSU HRMIS - @yield('title')</title>
    <link rel="icon" href="{{ asset('images/logos/uni_logo.png') }}" type="image/png">

    {{-- Vite: Compiles Tailwind CSS and JS locally --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Table sticky columns for better button visibility */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        table thead th:last-child,
        table tbody td:last-child {
            position: sticky;
            right: 0;
            z-index: 10;
        }

        table tbody td:last-child {
            background-color: white;
            box-shadow: -4px 0 8px rgba(0, 0, 0, 0.05);
        }

        table tbody tr:hover td:last-child {
            background-color: #f9fafb;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.08);
        }

        /* Button visibility improvements */
        button, .button {
            position: relative;
            z-index: 10;
        }

        button:focus, .button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        .sidebar {
            transition: all 0.3s ease;
            width: 250px;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .sidebar-header {
            display: none;
        }

        .sidebar-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #93c5fd; /* blue-300 */
            padding: 1.5rem 1rem 0.5rem 1.5rem;
            font-weight: 600;
        }

        /* HR sidebar section labels (used in hr/layouts/partials/sidebar.blade.php) */
        .sidebar-section {
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6ee7b7; /* emerald-300 — matches HR layout */
            padding: 0.75rem 1.5rem 0.25rem;
            font-weight: 600;
        }
        .sidebar.collapsed .sidebar-section { display: none; }
        
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
        
        /* Prevent logo from squishing */
        .sidebar-logo {
            min-width: 40px;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-logo {
            min-width: 40px;
            padding: 0.5rem;
        }

        .sidebar-logo-container {
            width: 180px;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-logo-container {
            width: 40px;
        }

        .sidebar-logo-container img {
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-logo-container img {
            height: 36px;
            width: auto;
        }

        @media (max-width: 768px) {
            .sidebar-logo-container {
                width: 180px;
            }
            
            .sidebar.mobile-open .sidebar-logo-container {
                width: 180px;
            }
        }

        /* Custom styles for attendance system */
        .biometric-placeholder {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .attendance-card {
            transition: all 0.3s ease;
        }

        .attendance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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

        .status-leave {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .qr-scanner {
            position: relative;
            width: 300px;
            height: 300px;
            margin: 0 auto;
            border: 3px dashed #3b82f6;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .qr-scanner video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .qr-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Mobile menu button (hidden on desktop) -->
        <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-800 text-white rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        @if(auth()->check() && auth()->user()->isHR())
            {{-- HR Staff users see the HR sidebar (with hr.* routes only) --}}
            @include('hr.layouts.partials.sidebar')
        @else
        <div id="sidebar" class="sidebar bg-blue-800 text-white fixed h-full overflow-y-auto">
            <div class="p-4 flex items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logos/uni_logo.png') }}" alt="BIPSU Logo" 
                        class="h-12 w-auto object-contain max-w-full transition-all duration-300">
                    <span class="logo-text text-xl font-bold whitespace-nowrap">eHRMIS</span>
                </div>
            </div>
                        <nav class="mt-6">
                <!-- Dashboard -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>

                <div class="sidebar-header">Employee Management</div>

                <!-- Employees -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-users mr-3 w-5"></i>
                        <span class="nav-text">Employees</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.employees.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Employee List</span>
                        </a>
                        <a href="{{ route('admin.employees.create') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Create New Employee</span>
                        </a>
                         <a href="{{ route('admin.pds.show') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Personal Data Sheet</span>
                        </a>
                        <a href="{{ route('admin.saln.show') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">SALN</span>
                        </a>
                    </div>
                </div>

                <!-- Travel & Requests -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('admin.travel.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plane mr-3 w-5"></i>
                        <span class="nav-text">Travel Orders</span>
                    </a>
                </div>

                <div class="sidebar-header">Time & Attendance</div>

                <!-- Attendance & DTR -->
                 <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-clock mr-3 w-5"></i>
                        <span class="nav-text">Attendance & DTR</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.attendance.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Attendance Log</span>
                        </a>
                        <a href="{{ route('admin.attendance.create') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Manual Entry</span>
                        </a>
                        <a href="{{ route('admin.dtr.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Daily Time Records</span>
                        </a>
                        <a href="{{ route('admin.dtr.export') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Export CS Form 48</span>
                        </a>
                         <a href="{{ route('admin.attendance.reports') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Attendance Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Leaves & Schedules -->
                 <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-calendar-check mr-3 w-5"></i>
                        <span class="nav-text">Leaves & Schedules</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.leave.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Leave Requests</span>
                        </a>
                        <a href="{{ route('admin.leave-balance.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Leave Balances</span>
                        </a>
                         <a href="{{ route('admin.work-schedules.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Work Schedules</span>
                        </a>
                        <a href="{{ route('admin.holidays.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Holidays</span>
                        </a>
                    </div>
                </div>

                <!-- Biometrics -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-fingerprint mr-3 w-5"></i>
                        <span class="nav-text">Biometrics</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.biometric.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Enroll Biometric</span>
                        </a>
                        <a href="{{ route('admin.biometric.enrolled') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Enrolled Employees</span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-header">Payroll & Finance</div>

                <!-- Payroll -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-money-bill-wave mr-3 w-5"></i>
                        <span class="nav-text">Payroll</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.payroll.generation.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Generate Payroll</span>
                        </a>
                        <a href="{{ route('admin.payroll.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll Records</span>
                        </a>
                        <a href="{{ route('admin.payroll.payslips') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payslips</span>
                        </a>
                        <a href="{{ route('admin.payroll.periods.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll Periods</span>
                        </a>
                         <a href="{{ route('admin.payroll.reports.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll Reports</span>
                        </a>
                    </div>
                </div>

                 <!-- Financial Config -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-coins mr-3 w-5"></i>
                        <span class="nav-text">Financial Config</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.salaries.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Salary Overview</span>
                        </a>
                        <a href="{{ route('admin.salary-grades.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Salary Grades</span>
                        </a>
                        <a href="{{ route('admin.allowances.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Allowances</span>
                        </a>
                        <a href="{{ route('admin.payroll.settings.index', ['tab' => 'deductions']) }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Deductions</span>
                        </a>
                         <a href="{{ route('admin.salaries.bulk-adjust-form') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Bulk Adjustment</span>
                        </a>
                        <a href="{{ route('admin.payroll.tax-reports.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Tax Reports</span>
                        </a>
                    </div>
                </div>
                
                <div class="sidebar-header">Performance</div>

                 <!-- Performance -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-trophy mr-3 w-5"></i>
                        <span class="nav-text">Performance</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.performance.reviews.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Reviews</span>
                        </a>
                        <a href="{{ route('admin.performance.goals.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Goals</span>
                        </a>
                        <a href="{{ route('admin.performance.criteria.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Criteria</span>
                        </a>
                        <a href="{{ route('admin.performance.analytics') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Analytics</span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-header">Organization</div>

                <!-- Organization -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-sitemap mr-3 w-5"></i>
                        <span class="nav-text">Org Structure</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.org-chart.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Organization Chart</span>
                        </a>
                        <a href="{{ route('admin.departments.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Departments</span>
                        </a>
                        <a href="{{ route('admin.positions.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Positions</span>
                        </a>
                        <a href="{{ route('admin.job-statuses.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Job Statuses</span>
                        </a>
                         <a href="{{ route('admin.employment-types.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Employment Types</span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-header">System</div>

                <!-- Reports & Audit -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                       <i class="fas fa-chart-bar mr-3 w-5"></i>
                        <span class="nav-text">Reports & Logs</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('admin.report-builder.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Report Builder</span>
                        </a>
                         <a href="{{ route('admin.audit-logs.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Audit Trail</span>
                        </a>
                        <a href="{{ route('admin.payroll.audit-history') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll Audit</span>
                        </a>
                         <a href="{{ route('admin.notifications.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Notifications</span>
                        </a>
                    </div>
                </div>

                <!-- Administration -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-cog mr-3 w-5"></i>
                        <span class="nav-text">Administration</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">User Management</span>
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Role Management</span>
                        </a>
                        <a href="{{ route('admin.roles.permissions') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Permission Manager</span>
                        </a>
                        <div class="border-t border-blue-800 my-1"></div>
                        @endif
                        <a href="{{ route('admin.login-sessions.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Login Sessions</span>
                        </a>
                        <a href="{{ route('admin.system-health.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">System Health</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.maintenance.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Maintenance Mode</span>
                        </a>
                        <a href="{{ route('admin.backups.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Backup Manager</span>
                        </a>
                        <a href="{{ route('admin.queue-monitor.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Queue Monitor</span>
                        </a>
                         <a href="{{ route('admin.payroll.analytics.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">System Analytics</span>
                        </a>
                        @endif
                        <div class="border-t border-blue-800 my-1"></div>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.settings.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">System Settings</span>
                        </a>
                        @endif
                    </div>
                </div>
            </nav>
            
            <!-- Sidebar Toggle Button -->
            <div class="p-4 mt-auto">
                <button id="toggleSidebar" class="w-full flex items-center justify-center py-2 bg-blue-700 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                    <span class="nav-text ml-2">Collapse</span>
                </button>
            </div>
        </div>
        @endif {{-- end HR/Admin sidebar conditional --}}

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('title')</h1>
                    
                    <div class="flex items-center space-x-4">
                        @include('components.notification_dropdown')
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer">
                                <img 
                                    src="{{ auth()->user()->profile_photo_url }}" 
                                    alt="Profile" 
                                    class="w-8 h-8 rounded-full"
                                    onerror="this.onerror=null; this.src='/images/icons/user-icon.webp';"
                                >

                                <span class="ml-2 text-gray-700">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </span>
                                <i class="fas fa-chevron-down ml-1 text-gray-600 text-xs"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">Profile</a>
                                <a href="" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700">Settings</a>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>{{ session('error') }}</p>
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

        // Poll for new notifications every 60 seconds (fallback if Pusher not available)
        // setInterval(() => {
        //     fetch('/notifications/count')
        //         .then(response => response.json())
        //         .then(data => {
        //             updateNotificationCount(data.count);
        //         });
        // }, 60000);
        
        function updateNotificationCount(count) {
            const counter = document.querySelector('.notification-counter');
            if (counter) {
                if (count > 0) {
                    counter.textContent = count;
                    counter.classList.remove('hidden');
                } else {
                    counter.classList.add('hidden');
                }
            }
        }
        
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
    </script>
    @endpush
    @stack('scripts')
</body>
</html>