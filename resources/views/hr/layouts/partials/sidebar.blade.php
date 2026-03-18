        <div id="sidebar" class="sidebar bg-blue-800 text-white fixed h-full overflow-y-auto">
            <div class="p-4 flex items-center border-b border-blue-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logos/uni_logo.png') }}" alt="BIPSU Logo"
                         class="h-12 w-auto object-contain max-w-full transition-all duration-300">
                    <div class="logo-text">
                        <span class="text-xl font-bold whitespace-nowrap">eHRMIS</span>
                        <p class="text-xs text-blue-300">HR Staff Portal</p>
                    </div>
                </div>
            </div>

            <nav class="mt-4 pb-4">

                <!-- Dashboard -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('hr.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                        <span class="nav-text font-medium">Dashboard</span>
                    </a>
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     WORKFORCE RECORDS
                     HR maintains 201 files, PDS, SALN, and org references.
                ════════════════════════════════════════════════════════════ --}}
                <div class="sidebar-section">Workforce Records</div>

                <!-- Employee Masterlist -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-id-card mr-3 w-5"></i>
                        <span class="nav-text">Employee Records</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.employees.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Masterlist</span>
                        </a>
                        <a href="{{ route('hr.employees.create') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Onboard Employee</span>
                        </a>
                    </div>
                </div>

                <!-- Personnel Documents -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-folder-open mr-3 w-5"></i>
                        <span class="nav-text">Personnel Documents</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.pds.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Personal Data Sheet (CS 212)</span>
                        </a>
                        <a href="{{ route('hr.saln.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">SALN</span>
                        </a>
                    </div>
                </div>

                <!-- Organizational Structure (reference / read-only for HR) -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-sitemap mr-3 w-5"></i>
                        <span class="nav-text">Organizational Structure</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.departments.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Departments</span>
                        </a>
                        <a href="{{ route('hr.positions.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Positions / Plantilla</span>
                        </a>
                        <a href="{{ route('hr.org-chart.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Organization Chart</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     ATTENDANCE & TIME
                     Daily attendance tracking, DTR, biometric enrollment,
                     and official holiday calendar maintenance.
                ════════════════════════════════════════════════════════════ --}}
                <div class="sidebar-section">Attendance & Time</div>

                <!-- Attendance Monitoring -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-clock mr-3 w-5"></i>
                        <span class="nav-text">Attendance Monitoring</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.attendance.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Attendance Log</span>
                        </a>
                        <a href="{{ route('hr.attendance.create') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Manual Time Entry</span>
                        </a>
                    </div>
                </div>

                <!-- Daily Time Records (DTR) -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-calendar-check mr-3 w-5"></i>
                        <span class="nav-text">Daily Time Records</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.dtr.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">View / Edit DTR</span>
                        </a>
                        <a href="{{ route('hr.dtr.export') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Print CS Form 48</span>
                        </a>
                    </div>
                </div>

                <!-- Biometric Enrollment -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-fingerprint mr-3 w-5"></i>
                        <span class="nav-text">Biometric Enrollment</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.biometric.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Enroll Employee</span>
                        </a>
                        <a href="{{ route('hr.biometric.enrolled') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Enrolled Employees</span>
                        </a>
                    </div>
                </div>

                <!-- Holiday Calendar -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('hr.holidays.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-days mr-3 w-5"></i>
                        <span class="nav-text">Holiday Calendar</span>
                    </a>
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     LEAVE ADMINISTRATION
                     Leave application processing, leave credit management,
                     and travel order approval — all absences from duty.
                ════════════════════════════════════════════════════════════ --}}
                <div class="sidebar-section">Leave Administration</div>

                <!-- Leave Applications -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-calendar-alt mr-3 w-5"></i>
                        <span class="nav-text">Leave Applications</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.leave.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">All Leave Requests</span>
                        </a>
                        <a href="{{ route('hr.leave.create') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">File Leave Application</span>
                        </a>
                        <a href="{{ route('hr.leave-balance.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Leave Credits & Balances</span>
                        </a>
                    </div>
                </div>

                <!-- Travel Orders -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-plane-departure mr-3 w-5"></i>
                        <span class="nav-text">Travel Orders</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.travel.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">All Travel Orders</span>
                        </a>
                        <a href="{{ route('hr.travel.create') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Issue Travel Order</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     PAYROLL PROCESSING
                     Payroll preparation, payslip generation, salary schedule
                     reference, allowance assignments, and government reports
                     (PhilHealth, GSIS, Pag-IBIG, BIR).
                ════════════════════════════════════════════════════════════ --}}
                <div class="sidebar-section">Payroll Processing</div>

                <!-- Payroll -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-money-bill-wave mr-3 w-5"></i>
                        <span class="nav-text">Payroll</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.payroll.periods.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Pay Periods</span>
                        </a>
                        <a href="{{ route('hr.payroll.generation.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Generate Payroll</span>
                        </a>
                        <a href="{{ route('hr.payroll.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll Register</span>
                        </a>
                        <a href="{{ route('hr.payroll.payslips') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payslips</span>
                        </a>
                    </div>
                </div>

                <!-- Payroll Reports (Gov't Remittances) -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-file-invoice-dollar mr-3 w-5"></i>
                        <span class="nav-text">Remittances & Reports</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.payroll.reports.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Payroll Reports</span>
                        </a>
                        <a href="{{ route('hr.payroll.tax-reports.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">BIR / Tax Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Benefits & Allowances -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-hand-holding-usd mr-3 w-5"></i>
                        <span class="nav-text">Benefits & Allowances</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.allowances.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Allowance Assignment</span>
                        </a>
                        <a href="{{ route('hr.salaries.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Salary Overview</span>
                        </a>
                        <a href="{{ route('hr.salary-grades.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Salary Schedule (DBM)</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     PERFORMANCE MANAGEMENT
                     SPMS-aligned: performance reviews (IPCR/OPCR), individual
                     goals, and evaluation criteria management.
                ════════════════════════════════════════════════════════════ --}}
                <div class="sidebar-section">Performance Management</div>

                <!-- Performance Evaluation -->
                <div class="nav-item has-submenu px-4 py-1">
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors submenu-toggle">
                        <i class="fas fa-star-half-alt mr-3 w-5"></i>
                        <span class="nav-text">Performance Evaluation</span>
                        <i class="fas fa-chevron-right ml-auto nav-arrow text-xs"></i>
                    </a>
                    <div class="submenu bg-blue-900 rounded-lg mt-1">
                        <a href="{{ route('hr.performance.reviews.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Performance Reviews</span>
                        </a>
                        <a href="{{ route('hr.performance.goals.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Individual Goals (IPCR)</span>
                        </a>
                        <a href="{{ route('hr.performance.criteria.index') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Evaluation Criteria</span>
                        </a>
                        <a href="{{ route('hr.performance.analytics') }}" class="submenu-item block px-4 py-2 hover:bg-blue-700 rounded transition-colors">
                            <span class="nav-text">Analytics</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     REPORTS & COMPLIANCE
                     Custom report generation and read-only activity log for
                     HR compliance and audit purposes.
                ════════════════════════════════════════════════════════════ --}}
                <div class="sidebar-section">Reports & Compliance</div>

                <!-- HR Report Builder -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('hr.report-builder.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-alt mr-3 w-5"></i>
                        <span class="nav-text">HR Report Builder</span>
                    </a>
                </div>

                <!-- Activity Log (read-only — for compliance) -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('hr.audit-logs.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-history mr-3 w-5"></i>
                        <span class="nav-text">Activity Log</span>
                    </a>
                </div>

                <!-- Notifications -->
                <div class="nav-item px-4 py-1">
                    <a href="{{ route('hr.notifications.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-bell mr-3 w-5"></i>
                        <span class="nav-text">Notifications</span>
                    </a>
                </div>

            </nav>

            <!-- Sidebar Toggle -->
            <div class="p-4 mt-auto border-t border-blue-700">
                <button id="toggleSidebar" class="w-full flex items-center justify-center py-2 bg-blue-700 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                    <span class="nav-text ml-2">Collapse</span>
                </button>
            </div>
        </div>
