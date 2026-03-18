@extends('admin.layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                    <p class="text-sm text-gray-600 mt-1">View user account information</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @if($user->id !== auth()->id())
                <button onclick="deleteUser()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
                <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 text-4xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-semibold text-gray-900">
                    {{ $user->employee ? $user->employee->full_name : 'No Employee Linked' }}
                </h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $user->role->name === 'Super Admin' ? 'bg-purple-100 text-purple-800' :
                           ($user->role->name === 'Admin' ? 'bg-indigo-100 text-indigo-800' :
                           ($user->role->name === 'HR' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                        <i class="fas fa-shield-alt mr-1"></i> {{ $user->role->name }}
                    </span>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' :
                           ($user->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        <i class="fas fa-circle mr-1"></i> {{ ucfirst($user->status) }}
                    </span>
                    @if($user->employee)
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-link mr-1"></i> Linked to Employee
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">User ID</label>
                    <p class="mt-1 text-gray-900">{{ $user->id }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Email</label>
                    <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Role</label>
                    <p class="mt-1 text-gray-900">{{ $user->role->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Status</label>
                    <p class="mt-1 text-gray-900">{{ ucfirst($user->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Activity Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Account Created</label>
                    <p class="mt-1 text-gray-900">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['account_age'] }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Last Login</label>
                    <p class="mt-1 text-gray-900">
                        @if($user->last_logged_at)
                            {{ $user->last_logged_at->format('M d, Y h:i A') }}
                            <p class="text-xs text-gray-500">{{ $user->last_logged_at->diffForHumans() }}</p>
                        @else
                            <span class="text-gray-500">Never logged in</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Last Updated</label>
                    <p class="mt-1 text-gray-900">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Linked Employee Information -->
        @if($user->employee)
        <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Linked Employee Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Employee Name</label>
                    <p class="mt-1 text-gray-900">{{ $user->employee->full_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Department</label>
                    <p class="mt-1 text-gray-900">{{ $user->employee->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Position</label>
                    <p class="mt-1 text-gray-900">{{ $user->employee->position->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Employee Number</label>
                    <p class="mt-1 text-gray-900">{{ $user->employee->employee_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Job Status</label>
                    <p class="mt-1 text-gray-900">{{ $user->employee->jobStatus->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Contact Number</label>
                    <p class="mt-1 text-gray-900">{{ $user->employee->contact_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 md:col-span-2">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        This user account is not linked to any employee record. You can link it from the edit page.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Role Description -->
    @if($user->role->description)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Role Description</h3>
        <p class="text-gray-700">{{ $user->role->description }}</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
function deleteUser() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch('/admin/users/{{ $user->id }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.users.index") }}';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user');
        });
    }
}
</script>
@endpush
@endsection
