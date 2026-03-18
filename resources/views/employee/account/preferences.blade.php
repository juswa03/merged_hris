@extends('employee.layouts.app')
@section('title', 'Preferences')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employee.account.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-chevron-left mr-2"></i>Back to Account Settings
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Preferences</h1>
        <p class="text-gray-600 mt-2">Customize your account settings and preferences</p>
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

    <form action="{{ route('employee.account.preferences.update') }}" method="POST" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <!-- Notification Preferences -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">
                <i class="fas fa-bell text-blue-600 mr-2"></i>Notification Preferences
            </h3>

            <div class="space-y-4">
                <!-- Email Notifications -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Email Notifications</p>
                        <p class="text-sm text-gray-600">Receive notifications via email</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notification_email" value="1" {{ ($user->preferences['notification_email'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- SMS Notifications -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">SMS Notifications</p>
                        <p class="text-sm text-gray-600">Receive important alerts via SMS</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notification_sms" value="1" {{ ($user->preferences['notification_sms'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Push Notifications -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Push Notifications</p>
                        <p class="text-sm text-gray-600">Receive browser notifications</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notification_push" value="1" {{ ($user->preferences['notification_push'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Display Preferences -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">
                <i class="fas fa-palette text-blue-600 mr-2"></i>Display Preferences
            </h3>

            <div class="space-y-4">
                <!-- Theme -->
                <div>
                    <label for="theme" class="block text-sm font-medium text-gray-900 mb-2">Theme</label>
                    <select name="theme" id="theme" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="light" {{ ($user->preferences['theme'] ?? 'auto') === 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ ($user->preferences['theme'] ?? 'auto') === 'dark' ? 'selected' : '' }}>Dark</option>
                        <option value="auto" {{ ($user->preferences['theme'] ?? 'auto') === 'auto' ? 'selected' : '' }}>Auto (System Default)</option>
                    </select>
                </div>

                <!-- Language -->
                <div>
                    <label for="language" class="block text-sm font-medium text-gray-900 mb-2">Language</label>
                    <select name="language" id="language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="en" {{ ($user->preferences['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                        <option value="es" {{ ($user->preferences['language'] ?? 'en') === 'es' ? 'selected' : '' }}>Spanish</option>
                        <option value="fr" {{ ($user->preferences['language'] ?? 'en') === 'fr' ? 'selected' : '' }}>French</option>
                        <option value="de" {{ ($user->preferences['language'] ?? 'en') === 'de' ? 'selected' : '' }}>German</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <a href="{{ route('employee.account.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Preferences
            </button>
        </div>
    </form>
</main>
@endsection
