@extends('admin.layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Role Management"
        description="Manage user roles and permissions"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.roles.create') }}" variant="primary" icon="fas fa-plus">
                Add Role
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Roles" :value="$stats['total_roles']" icon="fas fa-user-tag" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Roles with Users" :value="$stats['roles_with_users']" icon="fas fa-users" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Total Users" :value="$stats['total_users']" icon="fas fa-users-cog" gradientFrom="purple-500" gradientTo="purple-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form action="{{ route('admin.roles.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1 md:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Search roles...">
            </div>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-search">Search</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.roles.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Roles Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ Str::limit($role->description, 60) ?? 'No description' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <x-admin.badge :variant="$role->users_count > 0 ? 'success' : 'default'">
                            {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $role->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button :href="route('admin.roles.show', $role->id)" variant="info" icon="fas fa-eye" iconOnly size="sm" title="View Details"/>
                            <x-admin.action-button :href="route('admin.roles.edit', $role->id)" variant="warning" icon="fas fa-edit" iconOnly size="sm" title="Edit"/>
                            <x-admin.action-button variant="danger" icon="fas fa-trash" iconOnly size="sm" title="Delete" onclick="deleteRole({{ $role->id }}, '{{ addslashes($role->name) }}')"/>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-admin.empty-state
                            icon="fas fa-user-tag"
                            title="No roles found"
                            message="Start by creating your first role"
                            actionText="Create Role"
                            :actionLink="route('admin.roles.create')"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($roles->hasPages())
        <x-slot name="footer">{{ $roles->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function deleteRole(id, name) {
    if (confirm(`Are you sure you want to delete the role "${name}"?`)) {
        fetch(`/admin/roles/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) { location.reload(); } else { alert(data.message); }
        })
        .catch(() => alert('An error occurred while deleting the role'));
    }
}
</script>
@endpush
@endsection
