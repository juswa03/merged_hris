@extends('admin.layouts.app')

@section('title', 'Position Management')

@section('content')
@php $isHR = request()->routeIs('hr.*'); @endphp
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Position Management"
        description="{{ $isHR ? 'View organizational positions and job titles' : 'Manage organizational positions and job titles' }}"
    >
        @if(!$isHR)
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.positions.create') }}" variant="primary" icon="fas fa-plus">
                Add Position
            </x-admin.action-button>
        </x-slot>
        @endif
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Positions" :value="$stats['total_positions']" icon="fas fa-briefcase" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Active Positions" :value="$stats['active_positions']" icon="fas fa-check-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Filled Positions" :value="$stats['filled_positions']" icon="fas fa-user-check" gradientFrom="purple-500" gradientTo="purple-600"/>
        <x-admin.gradient-stat-card title="Vacant Positions" :value="$stats['vacant_positions']" icon="fas fa-inbox" gradientFrom="yellow-500" gradientTo="yellow-600"/>
        <x-admin.gradient-stat-card title="Total Employees" :value="$stats['total_employees']" icon="fas fa-users" gradientFrom="indigo-500" gradientTo="indigo-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form action="{{ $isHR ? route('hr.positions.index') : route('admin.positions.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1 md:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Search positions...">
            </div>
            <select name="level" class="block w-full md:w-48 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Levels</option>
                @foreach(['Entry Level', 'Mid Level', 'Senior Level', 'Executive', 'Managerial'] as $level)
                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                @endforeach
            </select>
            <select name="status" class="block w-full md:w-48 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-search">Search</x-admin.action-button>
            <x-admin.action-button href="{{ $isHR ? route('hr.positions.index') : route('admin.positions.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Positions Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Range</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Grade</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($positions as $position)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-briefcase text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $position->name }}</div>
                                @if($position->title && $position->title !== $position->name)
                                    <div class="text-sm text-gray-500">{{ $position->title }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($position->level)
                            <x-admin.badge :variant="$position->level === 'Executive' ? 'primary' : ($position->level === 'Managerial' ? 'info' : ($position->level === 'Senior Level' ? 'info' : ($position->level === 'Mid Level' ? 'success' : 'default')))">
                                {{ $position->level }}
                            </x-admin.badge>
                        @else
                            <span class="text-sm text-gray-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($position->min_salary || $position->max_salary)
                            ₱{{ number_format($position->min_salary ?? 0, 2) }} - ₱{{ number_format($position->max_salary ?? 0, 2) }}
                        @else
                            <span class="text-gray-500">Not set</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($position->salary_grade)
                            <x-admin.badge variant="warning">SG-{{ $position->salary_grade }}</x-admin.badge>
                        @else
                            <span class="text-sm text-gray-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <x-admin.badge :variant="$position->employees_count > 0 ? 'success' : 'default'">
                            {{ $position->employees_count }} {{ Str::plural('employee', $position->employees_count) }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($isHR)
                        <x-admin.badge :variant="$position->is_active ? 'success' : 'danger'">
                            {{ $position->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                        @else
                        <button onclick="toggleStatus({{ $position->id }})" class="cursor-pointer">
                            <x-admin.badge :variant="$position->is_active ? 'success' : 'danger'">
                                {{ $position->is_active ? 'Active' : 'Inactive' }}
                            </x-admin.badge>
                        </button>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button :href="$isHR ? route('hr.positions.show', $position->id) : route('admin.positions.show', $position->id)" variant="info" icon="fas fa-eye" iconOnly size="sm" title="View Details"/>
                            @if(!$isHR)
                            <x-admin.action-button :href="route('admin.positions.edit', $position->id)" variant="warning" icon="fas fa-edit" iconOnly size="sm" title="Edit"/>
                            <x-admin.action-button variant="danger" icon="fas fa-trash" iconOnly size="sm" title="Delete" onclick="deletePosition({{ $position->id }}, '{{ addslashes($position->name) }}')"/>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        @if($isHR)
                        <x-admin.empty-state
                            icon="fas fa-briefcase"
                            title="No positions found"
                            message="Contact an administrator to create positions"
                        />
                        @else
                        <x-admin.empty-state
                            icon="fas fa-briefcase"
                            title="No positions found"
                            message="Start by creating your first position"
                            actionText="Create Position"
                            :actionLink="route('admin.positions.create')"
                        />
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($positions->hasPages())
        <x-slot name="footer">{{ $positions->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function deletePosition(id, name) {
    if (confirm(`Are you sure you want to delete the position "${name}"?`)) {
        fetch(`/admin/positions/${id}`, {
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
        .catch(() => alert('An error occurred while deleting the position'));
    }
}

function toggleStatus(id) {
    fetch(`/admin/positions/${id}/toggle-status`, {
        method: 'POST',
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
    .catch(() => alert('An error occurred while updating the status'));
}
</script>
@endpush
@endsection
