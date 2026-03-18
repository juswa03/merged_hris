@extends('admin.layouts.app')

@section('title', 'Department Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Department Management"
        description="Manage organization departments and structure"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.departments.create') }}" variant="primary" icon="fas fa-plus">
                Add Department
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards with Gradients -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Total Departments" :value="$stats['total_departments']" icon="fas fa-building" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Total Employees" :value="$stats['total_employees']" icon="fas fa-users" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Active Departments" :value="$stats['departments_with_employees']" icon="fas fa-check-circle" gradientFrom="purple-500" gradientTo="purple-600"/>
        <x-admin.gradient-stat-card title="Empty Departments" :value="$stats['empty_departments']" icon="fas fa-inbox" gradientFrom="orange-500" gradientTo="orange-600"/>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <form action="{{ route('admin.departments.index') }}" method="GET">
            <div class="max-w-md">
                <x-admin.search-bar
                    name="search"
                    :value="request('search')"
                    placeholder="Search departments..."
                />
            </div>
        </form>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-admin.alert>
    @endif

    <!-- Departments Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Department Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employees
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($departments as $department)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-building text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $department->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ Str::limit($department->description, 60) ?? 'No description' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <x-admin.badge :variant="$department->employees_count > 0 ? 'success' : 'default'">
                            {{ $department->employees_count }} {{ Str::plural('employee', $department->employees_count) }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $department->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button
                                :href="route('admin.departments.show', $department->id)"
                                variant="info"
                                icon="fas fa-eye"
                                iconOnly
                                size="sm"
                                title="View Details"
                            />
                            <x-admin.action-button
                                :href="route('admin.departments.edit', $department->id)"
                                variant="warning"
                                icon="fas fa-edit"
                                iconOnly
                                size="sm"
                                title="Edit"
                            />
                            <x-admin.action-button
                                variant="danger"
                                icon="fas fa-trash"
                                iconOnly
                                size="sm"
                                title="Delete"
                                onclick="deleteDepartment({{ $department->id }}, '{{ $department->name }}')"
                            />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-admin.empty-state
                            icon="fas fa-building"
                            title="No departments found"
                            message="Start by creating your first department"
                            actionText="Create Department"
                            :actionLink="route('admin.departments.create')"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>

        <!-- Pagination -->
        @if($departments->hasPages())
        <x-slot name="footer">
            {{ $departments->links() }}
        </x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function deleteDepartment(id, name) {
    if (confirm(`Are you sure you want to delete the department "${name}"?`)) {
        fetch(`/admin/departments/${id}`, {
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
            alert('An error occurred while deleting the department');
        });
    }
}
</script>
@endpush
@endsection
