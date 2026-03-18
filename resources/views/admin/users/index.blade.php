@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="User Management"
        description="Manage system users and access control"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.users.create') }}" variant="primary" icon="fas fa-plus">
                Add User
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Users" :value="$stats['total_users']" icon="fas fa-users" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Active Users" :value="$stats['active_users']" icon="fas fa-user-check" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Inactive Users" :value="$stats['inactive_users']" icon="fas fa-user-slash" gradientFrom="red-500" gradientTo="red-600"/>
        <x-admin.gradient-stat-card title="Linked Employees" :value="$stats['users_with_employees']" icon="fas fa-link" gradientFrom="purple-500" gradientTo="purple-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1 md:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Search users...">
            </div>
            <select name="role_id" class="block w-full md:w-48 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <select name="status" class="block w-full md:w-48 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-search">Search</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.users.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Users Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Linked Employee</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
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
                                <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        @if($user->last_logged_at)
                            <div class="text-xs text-gray-500">Last login: {{ $user->last_logged_at->diffForHumans() }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <x-admin.badge :variant="$user->role->name === 'Super Admin' ? 'primary' : ($user->role->name === 'Admin' ? 'info' : 'default')">
                            {{ $user->role->name }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($user->employee)
                            <x-admin.badge variant="success"><i class="fas fa-link mr-1"></i> Linked</x-admin.badge>
                        @else
                            <x-admin.badge variant="default">Not Linked</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <button onclick="toggleStatus({{ $user->id }})" class="cursor-pointer">
                            <x-admin.badge :variant="$user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'danger' : 'warning')">
                                {{ ucfirst($user->status) }}
                            </x-admin.badge>
                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button :href="route('admin.users.show', $user->id)" variant="info" icon="fas fa-eye" iconOnly size="sm" title="View Details"/>
                            <x-admin.action-button :href="route('admin.users.edit', $user->id)" variant="warning" icon="fas fa-edit" iconOnly size="sm" title="Edit"/>
                            <x-admin.action-button variant="danger" icon="fas fa-trash" iconOnly size="sm" title="Delete" onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->email) }}')"/>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-admin.empty-state
                            icon="fas fa-users"
                            title="No users found"
                            message="Start by creating your first user"
                            actionText="Create User"
                            :actionLink="route('admin.users.create')"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($users->hasPages())
        <x-slot name="footer">{{ $users->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function deleteUser(id, email) {
    if (confirm(`Are you sure you want to delete the user "${email}"?`)) {
        fetch(`/admin/users/${id}`, {
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
                location.reload();
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

function toggleStatus(id) {
    fetch(`/admin/users/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status');
    });
}
</script>
@endpush
@endsection
