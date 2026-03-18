<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BIPSU HRMIS – HR Staff – @yield('title')</title>
    <link rel="icon" href="{{ asset('images/logos/uni_logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .sidebar {
            transition: all 0.3s ease;
            width: 250px;
        }
        .sidebar.collapsed { width: 70px; }
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .logo-text { display: none; }
        .sidebar.collapsed .submenu { display: none !important; }
        .sidebar.collapsed .nav-item:hover .submenu {
            display: block !important;
            position: fixed;
            left: 70px;
            background: #065f46;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,.1);
            z-index: 1000;
            min-width: 200px;
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
        .submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .submenu.open { max-height: 600px; transition: max-height 0.3s ease-in; }
        .submenu-item { position: relative; padding-left: 1rem; }
        .nav-item.has-submenu > a .nav-arrow { transition: transform 0.2s ease; }
        .nav-item.has-submenu.open > a .nav-arrow { transform: rotate(90deg); }
        .sidebar-section {
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6ee7b7;
            padding: 0.75rem 1.5rem 0.25rem;
            font-weight: 600;
        }
        .sidebar.collapsed .sidebar-section { display: none; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); position: fixed; z-index: 50; height: 100vh; }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar.collapsed { transform: translateX(-100%); }
            .main-content { margin-left: 0; width: 100%; }
            .sidebar.collapsed ~ .main-content { margin-left: 0; width: 100%; }
        }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; }
        table thead th:last-child, table tbody td:last-child {
            position: sticky; right: 0; z-index: 10;
        }
        table tbody td:last-child {
            background-color: white;
            box-shadow: -4px 0 8px rgba(0,0,0,.05);
        }
        table tbody tr:hover td:last-child {
            background-color: #f9fafb;
            box-shadow: -4px 0 12px rgba(0,0,0,.08);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-800 text-white rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- HR Sidebar (blue — university theme) -->
        @include('hr.layouts.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('title')</h1>
                    <div class="flex items-center space-x-4">
                        @include('components.notification_dropdown')
                        <div class="dropdown relative">
                            <div class="flex items-center cursor-pointer">
                                <img src="{{ auth()->user()->profile_photo_url }}"
                                     alt="Profile" class="w-8 h-8 rounded-full"
                                     onerror="this.onerror=null; this.src='/images/icons/user-icon.webp';">
                                <div class="ml-2">
                                    <span class="text-gray-700 text-sm font-medium">
                                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                    </span>
                                    <p class="text-xs text-blue-600">HR Staff</p>
                                </div>
                                <i class="fas fa-chevron-down ml-1 text-gray-600 text-xs"></i>
                            </div>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dropdown
            const dropdown = document.querySelector('.dropdown');
            if (dropdown) {
                dropdown.addEventListener('click', function (e) {
                    if (e.target.closest('.dropdown-menu')) return;
                    this.querySelector('.dropdown-menu').classList.toggle('hidden');
                });
            }
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.dropdown')) {
                    const open = document.querySelector('.dropdown-menu:not(.hidden)');
                    if (open) open.classList.add('hidden');
                }
            });

            // Submenus
            document.querySelectorAll('.submenu-toggle').forEach(toggle => {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const navItem = this.closest('.nav-item');
                    const submenu = navItem.querySelector('.submenu');
                    document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
                        if (item !== navItem) {
                            item.classList.remove('open');
                            item.querySelector('.submenu').classList.remove('open');
                        }
                    });
                    navItem.classList.toggle('open');
                    submenu.classList.toggle('open');
                });
            });

            // Sidebar toggle
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            const mobileBtn = document.getElementById('mobileMenuBtn');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    if (sidebar.classList.contains('collapsed')) {
                        document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
                            item.classList.remove('open');
                            item.querySelector('.submenu').classList.remove('open');
                        });
                        toggleBtn.querySelector('i').classList.replace('fa-chevron-left', 'fa-chevron-right');
                        toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                    } else {
                        toggleBtn.querySelector('i').classList.replace('fa-chevron-right', 'fa-chevron-left');
                        toggleBtn.querySelector('.nav-text').textContent = 'Collapse';
                    }
                    localStorage.setItem('hrSidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }

            if (mobileBtn) {
                mobileBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('mobile-open');
                    if (sidebar.classList.contains('mobile-open')) sidebar.classList.remove('collapsed');
                });
            }

            // Restore state
            if (window.innerWidth > 768 && localStorage.getItem('hrSidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                if (toggleBtn) {
                    toggleBtn.querySelector('i').classList.replace('fa-chevron-left', 'fa-chevron-right');
                    toggleBtn.querySelector('.nav-text').textContent = 'Expand';
                }
            }
        });
    </script>
</body>
</html>
