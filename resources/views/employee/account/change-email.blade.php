@extends('employee.layouts.app')
@section('title', 'Change Email')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employee.account.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-chevron-left mr-2"></i>Back to Account Settings
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Change Email Address</h1>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors in your submission:</h3>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Email Change Form -->
    <form action="{{ route('employee.account.email.update') }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <!-- Current Email -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Current Email Address</label>
                <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
            </div>

            <!-- Info Box -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    After changing your email, you'll need to verify the new address. A verification link will be sent to your new email.
                </p>
            </div>

            <!-- New Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-1">New Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-900 mb-1">Confirm Password</label>
                <p class="text-sm text-gray-600 mb-2">For security, please confirm your password to change your email address.</p>
                <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-between pt-6 border-t">
                <a href="{{ route('employee.account.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-envelope mr-2"></i>Update Email
                </button>
            </div>
        </div>
    </form>
</main>
@endsection
