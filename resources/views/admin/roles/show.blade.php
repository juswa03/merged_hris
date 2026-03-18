@extends('admin.layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Role Details</h1>
                    <p class="text-sm text-gray-600 mt-1">View role information and assigned users</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <button onclick="deleteRole()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Role Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
                <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-blue-600 text-4xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-semibold text-gray-900">{{ $role->name }}</h2>
                <p class="text-gray-600 mt-2">{{ $role->description ?? 'No description provided' }}</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $role->users_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        <i class="fas fa-users mr-1"></i> {{ $role->users_count }} {{ Str::plural('User', $role->users_count) }}
                    </span>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        <i class="fas fa-calendar mr-1"></i> Created {{ $role->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Assigned Users -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Assigned Users</h3>
                    <p class="text-sm text-gray-600">Users who have been assigned this role</p>
                </div>

                @if($role->users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Login
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($role->users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->employee ? $user->employee->full_name : 'No Employee Linked' }}
                                            </div>
                                            @if($user->employee)
                                            <div class="text-xs text-gray-500">
                                                {{ $user->employee->department->name ?? 'No Department' }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' :
                                           ($user->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    @if($user->last_logged_at)
                                        {{ $user->last_logged_at->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="View User">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No users assigned</p>
                        <p class="text-gray-400 text-sm mt-2">This role has not been assigned to any users yet</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Role Information Sidebar -->
        <div class="lg:col-span-1">
            <!-- Role Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Role ID</label>
                        <p class="mt-1 text-gray-900">{{ $role->id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Role Name</label>
                        <p class="mt-1 text-gray-900">{{ $role->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Total Users</label>
                        <p class="mt-1 text-gray-900">{{ $role->users_count }}</p>
                    </div>
                </div>
            </div>

            <!-- Activity Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Created</label>
                        <p class="mt-1 text-gray-900">{{ $role->created_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs text-gray-500">{{ $role->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Last Updated</label>
                        <p class="mt-1 text-gray-900">{{ $role->updated_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs text-gray-500">{{ $role->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteRole() {
    if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
        fetch('/admin/roles/{{ $role->id }}', {
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
                window.location.href = '{{ route("admin.roles.index") }}';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the role');
        });
    }
}
</script>
@endpush
@endsection
