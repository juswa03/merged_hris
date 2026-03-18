@extends('employee.layouts.app')
@section('title', 'Two-Factor Authentication')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employee.account.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-chevron-left mr-2"></i>Back to Account Settings
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Two-Factor Authentication</h1>
        <p class="text-gray-600 mt-2">Add an extra layer of security to your account</p>
    </div>

    <div class="max-w-2xl">
        @if(auth()->user()->two_factor_confirmed_at)
            <!-- Disable 2FA Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center p-4 mb-6 bg-green-50 border border-green-200 rounded-lg">
                    <i class="fas fa-check-circle text-2xl text-green-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-green-800">Two-Factor Authentication is Enabled</p>
                        <p class="text-sm text-green-700">Your account is protected with 2FA</p>
                    </div>
                </div>

                <form action="{{ route('employee.account.two-factor.disable') }}" method="POST" onsubmit="return confirm('Are you sure you want to disable 2FA? This will reduce your account security.');">
                    @csrf
                    
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Disabling 2FA will make your account less secure. We recommend keeping it enabled.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-900 mb-1">Confirm Password</label>
                        <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-times mr-2"></i>Disable 2FA
                    </button>
                </form>
            </div>
        @else
            <!-- Enable 2FA Section -->
            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <i class="fas fa-shield-alt text-2xl text-yellow-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-yellow-800">Two-Factor Authentication is Not Enabled</p>
                        <p class="text-sm text-yellow-700">Improve your account security by enabling 2FA</p>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Two-factor authentication requires you to verify your identity using your phone or authenticator app when logging in.
                    </p>
                </div>

                <form action="{{ route('employee.account.two-factor.enable') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-900 mb-1">Confirm Password</label>
                        <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Enable 2FA
                    </button>
                </form>

                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-3">What you'll need:</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-mobile-alt text-gray-400 mr-2"></i>A smartphone or authenticator app</li>
                        <li><i class="fas fa-qrcode text-gray-400 mr-2"></i>QR code to scan with your authenticator</li>
                        <li><i class="fas fa-lock text-gray-400 mr-2"></i>Backup codes in case you lose access</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</main>
@endsection
