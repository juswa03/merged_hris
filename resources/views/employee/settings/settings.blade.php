@extends('employee.layouts.app')

@section('title', 'My Settings')

@section('content')
<main class="p-6">
    <!-- Settings Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500">
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab active rounded-t-lg bg-blue-600 text-white">
                    <i class="fas fa-user mr-2"></i>
                    Profile
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-lock mr-2"></i>
                    Security
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-flex items-center justify-center p-4 settings-tab rounded-t-lg hover:text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Time Preferences
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

    <!-- Profile Settings -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Profile Card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex flex-col items-center">
                <div class="relative mb-4">
                    <img class="w-32 h-32 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=random" alt="Profile photo">
                    <button class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-gray-600">{{ auth()->user()->position ?? 'Employee' }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ auth()->user()->department ?? 'No department assigned' }}</p>
                
                <div class="mt-4 w-full">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Profile Completion</h4>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 75%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">75% complete</p>
                </div>
            </div>
        </div>

        <!-- Personal Information Form -->
        <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-id-card mr-2 text-blue-600"></i>
                Personal Information
            </h3>
            <form class="settings-form space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="firstName" name="firstName" value="{{ auth()->user()->first_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                    </div>
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="lastName" name="lastName" value="{{ auth()->user()->last_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="phone" name="phone" value="{{ auth()->user()->phone ?? '+63 999 999 9999' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                    </div>
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">{{ auth()->user()->address ?? 'No address provided' }}</textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-user-cog mr-2 text-blue-600"></i>
            Account Settings
        </h3>
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-700">Password</h4>
                    <p class="text-sm text-gray-500">Last changed 3 months ago</p>
                </div>
                <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-key mr-2"></i>
                        Change Password
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-700">Two-Factor Authentication</h4>
                    <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                </div>
                <div class="flex justify-end">
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" id="twoFactorToggle" class="toggle-checkbox sr-only">
                            <div class="toggle-label block w-10 h-6 bg-gray-200 rounded-full"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-700">Off</span>
                    </label>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-700">Account Deletion</h4>
                    <p class="text-sm text-gray-500">Permanently delete your account and all data</p>
                </div>
                <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Preferences -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-bell mr-2 text-blue-600"></i>
            Notification Preferences
        </h3>
        <form class="settings-form space-y-4">
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Email Notifications</h4>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">System Notifications</span>
                            <div class="relative">
                                <input type="checkbox" id="systemEmailToggle" class="toggle-checkbox sr-only" checked>
                                <div class="toggle-label block w-10 h-6 bg-blue-600 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-4"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Leave Request Updates</span>
                            <div class="relative">
                                <input type="checkbox" id="leaveEmailToggle" class="toggle-checkbox sr-only" checked>
                                <div class="toggle-label block w-10 h-6 bg-blue-600 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-4"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Payroll Updates</span>
                            <div class="relative">
                                <input type="checkbox" id="payrollEmailToggle" class="toggle-checkbox sr-only">
                                <div class="toggle-label block w-10 h-6 bg-gray-200 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="pt-2">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">In-App Notifications</h4>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">New Announcements</span>
                            <div class="relative">
                                <input type="checkbox" id="announcementAppToggle" class="toggle-checkbox sr-only" checked>
                                <div class="toggle-label block w-10 h-6 bg-blue-600 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-4"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Task Assignments</span>
                            <div class="relative">
                                <input type="checkbox" id="taskAppToggle" class="toggle-checkbox sr-only" checked>
                                <div class="toggle-label block w-10 h-6 bg-blue-600 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-4"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Schedule Changes</span>
                            <div class="relative">
                                <input type="checkbox" id="scheduleAppToggle" class="toggle-checkbox sr-only">
                                <div class="toggle-label block w-10 h-6 bg-gray-200 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
</main>
    
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Initialize toggle switches based on their checked state
        toggleSwitches.forEach(toggle => {
            const label = toggle.nextElementSibling;
            const dot = label.nextElementSibling;
            
            if (toggle.checked) {
                label.classList.add('bg-blue-600');
                dot.classList.add('translate-x-4');
            }
        });
    });
</script>
@endpush
@endsection