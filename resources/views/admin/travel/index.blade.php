@extends('admin.layouts.app')

@section('title', 'Travel Authority Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Travel Authority Management"
        description="Manage and process employee travel authority requests"
    >
        <x-slot name="actions">
            <x-admin.action-button :href="route('admin.travel.export')" variant="success" icon="fas fa-download">
                Export
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TA No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Travel Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($travels as $travel)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $travel->travel_authority_no ?? 'Pending' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $travel->user->first_name }} {{ $travel->user->last_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $travel->destination }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $travel->inclusive_date_of_travel->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-admin.badge :variant="$travel->status === 'approved' ? 'success' : ($travel->status === 'rejected' ? 'danger' : ($travel->status === 'pending' ? 'warning' : 'info'))">
                            {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <x-admin.action-button :href="route('admin.travel.show', $travel)" variant="info" icon="fas fa-eye" size="sm">
                            View/Approve
                        </x-admin.action-button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-admin.empty-state
                            icon="fas fa-plane"
                            title="No travel authorities found"
                            message="No travel authority requests have been submitted yet"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if($travels->hasPages())
        <x-slot name="footer">{{ $travels->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>
@endsection