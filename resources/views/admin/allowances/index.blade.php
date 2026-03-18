@extends('admin.layouts.app')

@section('title', 'Allowance Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Allowance Management"
        description="Manage employee allowances and assignments"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.allowances.create') }}" variant="primary" icon="fas fa-plus">
                Add Allowance
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Allowances" :value="$totalAllowances" icon="fas fa-plus-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Employees with Allowances" :value="$totalEmployeesWithAllowances" icon="fas fa-users" gradientFrom="blue-500" gradientTo="blue-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.allowances.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1 md:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search allowance name..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <select name="type" class="block w-full md:w-48 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-search">Search</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.allowances.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Allowances Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($allowances as $allowance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $allowance->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-admin.badge variant="success">{{ ucfirst($allowance->type) }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ₱{{ number_format($allowance->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <x-admin.badge :variant="$allowance->employees->count() > 0 ? 'info' : 'default'">
                            {{ $allowance->employees->count() }} employee(s)
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center gap-2">
                            <x-admin.action-button :href="route('admin.allowances.show', $allowance->id)" variant="info" icon="fas fa-eye" iconOnly size="sm" title="View"/>
                            <x-admin.action-button :href="route('admin.allowances.assign', $allowance->id)" variant="success" icon="fas fa-user-plus" iconOnly size="sm" title="Assign to Employees"/>
                            <x-admin.action-button :href="route('admin.allowances.edit', $allowance->id)" variant="warning" icon="fas fa-edit" iconOnly size="sm" title="Edit"/>
                            <form action="{{ route('admin.allowances.destroy', $allowance->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this allowance?');">
                                @csrf
                                @method('DELETE')
                                <x-admin.action-button type="submit" variant="danger" icon="fas fa-trash" iconOnly size="sm" title="Delete"/>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-admin.empty-state
                            icon="fas fa-plus-circle"
                            title="No allowances found"
                            message="Start by creating your first allowance"
                            actionText="Create Allowance"
                            :actionLink="route('admin.allowances.create')"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($allowances->hasPages())
        <x-slot name="footer">{{ $allowances->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>

@endsection
