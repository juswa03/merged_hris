@extends('employee.layouts.app')
@section('title', 'Account Settings')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Account Settings</h1>
        <p class="text-gray-600 mt-2">Manage your account information, security settings, and preferences</p>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Account Settings Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                        <p class="text-sm text-gray-500">Update your personal information</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Manage your name, email, phone number, and profile photo.</p>
                <a href="{{ route('employee.account.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Password Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lock text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Password</h3>
                        <p class="text-sm text-gray-500">Change your account password</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Update your password to keep your account secure.</p>
                <a href="{{ route('employee.account.password.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-key mr-2"></i>
                    Change Password
                </a>
            </div>
        </div>

        <!-- Email Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Email Address</h3>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Change your email address associated with your account.</p>
                <a href="{{ route('employee.account.email.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-envelope-open mr-2"></i>
                    Change Email
                </a>
            </div>
        </div>

        <!-- Two-Factor Authentication Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-3xl {{ auth()->user()->two_factor_confirmed_at ? 'text-green-600' : 'text-gray-400' }}"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Two-Factor Authentication</h3>
                        <p class="text-sm {{ auth()->user()->two_factor_confirmed_at ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ auth()->user()->two_factor_confirmed_at ? '✓ Enabled' : '⚠ Not Enabled' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Add an extra layer of security to your account.</p>
                <a href="{{ route('employee.account.two-factor.edit') }}" class="inline-flex items-center px-4 py-2 {{ auth()->user()->two_factor_confirmed_at ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg">
                    <i class="fas {{ auth()->user()->two_factor_confirmed_at ? 'fa-times' : 'fa-check' }} mr-2"></i>
                    {{ auth()->user()->two_factor_confirmed_at ? 'Disable 2FA' : 'Enable 2FA' }}
                </a>
            </div>
        </div>

        <!-- Activity Log Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-history text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Activity Log</h3>
                        <p class="text-sm text-gray-500">View your login history</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Review all login activity and sessions on your account.</p>
                <a href="{{ route('employee.account.activity') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-list mr-2"></i>
                    View Activity
                </a>
            </div>
        </div>

        <!-- Sessions Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-laptop text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Active Sessions</h3>
                        <p class="text-sm text-gray-500">Manage your devices</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">View and manage all active sessions and devices.</p>
                <a href="{{ route('employee.account.sessions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-cogs mr-2"></i>
                    Manage Sessions
                </a>
            </div>
        </div>

        <!-- Preferences Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-sliders-h text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Preferences</h3>
                        <p class="text-sm text-gray-500">Notification and display settings</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Customize notifications, theme, and language preferences.</p>
                <a href="{{ route('employee.account.preferences') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-sliders-h mr-2"></i>
                    Preferences
                </a>
            </div>
        </div>

        <!-- Delete Account Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition border-l-4 border-red-600">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-trash text-3xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Delete Account</h3>
                        <p class="text-sm text-red-600">Permanent action</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">Permanently delete your account and all associated data.</p>
                <a href="{{ route('employee.account.delete.edit') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Delete Account
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
