@extends('employee.layouts.app')
@section('title', 'Delete Account')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employee.account.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-chevron-left mr-2"></i>Back to Account Settings
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Delete Account</h1>
        <p class="text-gray-600 mt-2">This action cannot be undone</p>
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

    <div class="max-w-2xl">
        <!-- Warning Box -->
        <div class="p-6 mb-6 bg-red-50 border-l-4 border-red-600 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600 mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-red-800 mb-2">Warning: Permanent Deletion</h3>
                    <p class="text-red-700 mb-3">
                        Deleting your account is a permanent action that cannot be undone. Once your account is deleted:
                    </p>
                    <ul class="list-disc list-inside text-red-700 space-y-2">
                        <li>All your personal information will be permanently deleted</li>
                        <li>All attendance and payroll records will be archived</li>
                        <li>You will not be able to access your account anymore</li>
                        <li>All your documents and files will be removed</li>
                        <li>You won't be able to recover any data</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Confirmation Info -->
        <div class="p-6 mb-6 bg-yellow-50 border-l-4 border-yellow-600 rounded-lg">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                If you're having issues with your account, please contact the HR department before deleting your account. We may be able to help resolve your concerns.
            </p>
        </div>

        <!-- Delete Form -->
        <form action="{{ route('employee.account.delete') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6" onsubmit="return validateDeletion();">
            @csrf
            @method('DELETE')

            <!-- Password Confirmation -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-900 mb-1">Confirm Your Password</label>
                <p class="text-sm text-gray-600 mb-2">For security, please enter your password to confirm account deletion.</p>
                <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmation Checkbox -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="confirmation" value="1" id="confirmation" class="mt-1 w-4 h-4 text-red-600 border border-gray-300 rounded focus:ring-2 focus:ring-red-500" required>
                    <span class="ml-3 text-sm text-gray-700">
                        I understand that deleting my account is permanent and cannot be reversed. I want to proceed with deleting my account.
                    </span>
                </label>
                @error('confirmation')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-between pt-6 border-t">
                <a href="{{ route('employee.account.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Keep My Account
                </a>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Delete My Account
                </button>
            </div>
        </form>
    </div>
</main>

<script>
function validateDeletion() {
    const password = document.getElementById('password');
    const confirmation = document.getElementById('confirmation');

    if (!password.value) {
        alert('Please enter your password to confirm.');
        return false;
    }

    if (!confirmation.checked) {
        alert('Please confirm that you understand this action is permanent.');
        return false;
    }

    return confirm('Are you absolutely certain you want to delete your account? This cannot be undone.');
}
</script>
@endsection
