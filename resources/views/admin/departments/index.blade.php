@extends('layouts.app')

@section('title', 'Department Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Department Management"
        description="Manage organization departments and structure"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('departments.create') }}" variant="primary" icon="fas fa-plus">
                Add Department
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards with Gradients -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Departments -->
        <div class="group bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-white/95 text-sm font-medium uppercase tracking-wide">Total Departments</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_departments'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-xl">
                        <i class="fas fa-building text-3xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="group bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-white/95 text-sm font-medium uppercase tracking-wide">Total Employees</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_employees'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-xl">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Departments -->
        <div class="group bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-white/95 text-sm font-medium uppercase tracking-wide">Active Departments</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['departments_with_employees'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-xl">
                        <i class="fas fa-check-circle text-3xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty Departments -->
        <div class="group bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-white/95 text-sm font-medium uppercase tracking-wide">Empty Departments</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['empty_departments'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-xl">
                        <i class="fas fa-inbox text-3xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <form action="{{ route('departments.index') }}" method="GET">
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
                                :href="route('departments.show', $department->id)"
                                variant="info"
                                icon="fas fa-eye"
                                iconOnly
                                size="sm"
                                title="View Details"
                            />
                            <x-admin.action-button
                                :href="route('departments.edit', $department->id)"
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
                            :actionLink="route('departments.create')"
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
        fetch(`/departments/${id}`, {
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
