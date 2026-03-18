@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header title="Personal Data Sheet" description="Review and manage employee PDS submissions">
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.pds.create') }}" variant="primary" icon="fas fa-plus">
                Add PDS Record
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif
    @if(session('info'))
        <x-admin.alert type="info" dismissible class="mb-6">{{ session('info') }}</x-admin.alert>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <x-admin.gradient-stat-card
            title="Total Records"
            :value="$stats['total']"
            icon="fas fa-file-alt"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Submitted"
            :value="$stats['submitted']"
            icon="fas fa-paper-plane"
            gradientFrom="yellow-500"
            gradientTo="yellow-600"
        />
        <x-admin.gradient-stat-card
            title="Under Review"
            :value="$stats['under_review']"
            icon="fas fa-search"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
        <x-admin.gradient-stat-card
            title="Verified"
            :value="$stats['verified']"
            icon="fas fa-check-circle"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Rejected"
            :value="$stats['rejected']"
            icon="fas fa-times-circle"
            gradientFrom="red-500"
            gradientTo="red-600"
        />
    </div>

    {{-- Filters --}}
    <x-admin.card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.pds.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Name or Employee ID…"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Departments</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}" @selected(request('department') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.pds.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                    Reset
                </a>
            </div>
        </form>
    </x-admin.card>

    {{-- Table --}}
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Action</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pdsList as $pds)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">
                                {{ $pds->employee->full_name ?? '—' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $pds->employee->employee_id ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $pds->employee->department->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusVariant = match($pds->status) {
                                    'verified'     => 'success',
                                    'submitted'    => 'warning',
                                    'under_review' => 'info',
                                    'rejected'     => 'danger',
                                    default        => 'default',
                                };
                            @endphp
                            <x-admin.badge :variant="$statusVariant">
                                {{ $pds->getStatusDisplay() }}
                            </x-admin.badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pds->submitted_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pds->last_action_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-admin.action-button
                                href="{{ route('admin.pds.show-detail', $pds) }}"
                                variant="secondary"
                                size="sm"
                                icon="fas fa-eye"
                            >
                                View
                            </x-admin.action-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-admin.empty-state
                                icon="fas fa-file-alt"
                                title="No PDS records found"
                                message="No records match your current filters."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>

        @if($pdsList->hasPages())
            <x-slot name="footer">
                {{ $pdsList->links() }}
            </x-slot>
        @endif
    </x-admin.card>

</div>
@endsection
