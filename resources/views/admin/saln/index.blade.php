@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header title="SALN" description="Statement of Assets, Liabilities and Net Worth — review and verify employee filings">
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.saln.create') }}" variant="primary" icon="fas fa-plus">
                Add Record
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
            icon="fas fa-file-contract"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Draft"
            :value="$stats['draft']"
            icon="fas fa-file-alt"
            gradientFrom="indigo-500"
            gradientTo="indigo-600"
        />
        <x-admin.gradient-stat-card
            title="Submitted"
            :value="$stats['submitted']"
            icon="fas fa-paper-plane"
            gradientFrom="yellow-500"
            gradientTo="yellow-600"
        />
        <x-admin.gradient-stat-card
            title="Verified"
            :value="$stats['verified']"
            icon="fas fa-check-circle"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Flagged"
            :value="$stats['flagged']"
            icon="fas fa-flag"
            gradientFrom="red-500"
            gradientTo="red-600"
        />
    </div>

    {{-- Filters --}}
    <x-admin.card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.saln.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Name or email…"
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
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.saln.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action By</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($salnList as $saln)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $saln->user->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $saln->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $variant = match($saln->status) {
                                    'verified'    => 'success',
                                    'submitted'   => 'warning',
                                    'flagged'     => 'danger',
                                    'in_progress' => 'info',
                                    default       => 'default',
                                };
                            @endphp
                            <x-admin.badge :variant="$variant">
                                {{ $statuses[$saln->status] ?? ucfirst($saln->status) }}
                            </x-admin.badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $saln->last_action_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $saln->lastActionBy->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-admin.action-button
                                href="{{ route('admin.saln.show-detail', $saln) }}"
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
                        <td colspan="5">
                            <x-admin.empty-state
                                icon="fas fa-file-contract"
                                title="No SALN records found"
                                message="No records match your current filters."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>

        @if($salnList->hasPages())
            <x-slot name="footer">
                {{ $salnList->links() }}
            </x-slot>
        @endif
    </x-admin.card>

</div>
@endsection
