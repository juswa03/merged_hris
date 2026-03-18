@extends('admin.layouts.app')

@section('title', 'Holiday Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Holiday Management"
        description="Manage public and special non-working holidays"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.holidays.create') }}" variant="primary" icon="fas fa-plus">
                Add Holiday
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Flash Messages -->
    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <!-- Holidays Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($holidays as $holiday)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-9 w-9 rounded-full flex items-center justify-center
                                {{ $holiday->type === 'regular' ? 'bg-red-100' : 'bg-yellow-100' }}">
                                <i class="fas fa-calendar-day text-sm
                                    {{ $holiday->type === 'regular' ? 'text-red-600' : 'text-yellow-600' }}"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $holiday->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $holiday->date->format('F d, Y') }}
                        <div class="text-xs text-gray-400">{{ $holiday->date->format('l') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($holiday->type === 'regular')
                            <x-admin.badge variant="danger">Regular</x-admin.badge>
                        @else
                            <x-admin.badge variant="warning">Special</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($holiday->is_paid)
                            <x-admin.badge variant="success">Paid</x-admin.badge>
                        @else
                            <x-admin.badge variant="default">Unpaid</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                        {{ $holiday->remarks ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-1">
                            <x-admin.action-button
                                href="{{ route('admin.holidays.edit', $holiday) }}"
                                variant="info"
                                icon="fas fa-edit"
                                :iconOnly="true"
                                size="sm"
                                title="Edit"
                            />
                            <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($holiday->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <x-admin.action-button
                                    type="submit"
                                    variant="danger"
                                    icon="fas fa-trash"
                                    :iconOnly="true"
                                    size="sm"
                                    title="Delete"
                                />
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-admin.empty-state
                            icon="fas fa-calendar-times"
                            title="No holidays found"
                            message="Add your first holiday to get started"
                            actionText="Add Holiday"
                            actionLink="{{ route('admin.holidays.create') }}"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>

        @if($holidays->hasPages())
        <x-slot name="footer">
            {{ $holidays->links() }}
        </x-slot>
        @endif
    </x-admin.card>
</div>
@endsection
