@extends('admin.layouts.app')
@section('title', 'Audit Log Detail')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-admin.page-header title="Audit Log Detail">
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.audit-logs.index') }}" variant="secondary" icon="fas fa-arrow-left">Back</x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <x-admin.card>
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Event Information</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Action</dt>
                    <dd><span class="px-2 py-1 text-xs font-medium rounded-full {{ $auditLog->actionBadgeClass() }}">{{ ucfirst($auditLog->action) }}</span></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Model</dt>
                    <dd class="font-medium">{{ $auditLog->model_type }} #{{ $auditLog->model_id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Record</dt>
                    <dd class="font-medium text-gray-800">{{ $auditLog->model_label ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Timestamp</dt>
                    <dd>{{ $auditLog->created_at->format('M d, Y h:i:s A') }}</dd>
                </div>
            </dl>
        </x-admin.card>

        <x-admin.card>
            <h3 class="text-sm font-semibold text-gray-700 mb-4">User & Request</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">User</dt>
                    <dd class="font-medium">{{ $auditLog->user_name ?? 'System' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">IP Address</dt>
                    <dd class="font-mono">{{ $auditLog->ip_address ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 flex-shrink-0 mr-4">User Agent</dt>
                    <dd class="text-xs text-gray-400 text-right break-all">{{ $auditLog->user_agent ?? '—' }}</dd>
                </div>
            </dl>
        </x-admin.card>
    </div>

    @if($auditLog->action === 'updated')
    <x-admin.card class="mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Changes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-40">Field</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Before</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">After</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($auditLog->changed_fields as $field => $newVal)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-600">{{ str_replace('_', ' ', $field) }}</td>
                        <td class="px-4 py-2 text-red-600 line-through text-xs">{{ $auditLog->old_values[$field] ?? '—' }}</td>
                        <td class="px-4 py-2 text-green-700 text-xs">{{ $newVal ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-4 text-gray-400 text-center">No field changes recorded</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
    @elseif($auditLog->action === 'created' && $auditLog->new_values)
    <x-admin.card class="mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Created Values</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($auditLog->new_values as $field => $val)
            <div class="bg-green-50 rounded p-2">
                <div class="text-xs text-gray-500">{{ str_replace('_', ' ', $field) }}</div>
                <div class="text-sm font-medium text-gray-800 truncate">{{ $val ?? '—' }}</div>
            </div>
            @endforeach
        </div>
    </x-admin.card>
    @elseif($auditLog->action === 'deleted' && $auditLog->old_values)
    <x-admin.card class="mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Deleted Record Data</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($auditLog->old_values as $field => $val)
            <div class="bg-red-50 rounded p-2">
                <div class="text-xs text-gray-500">{{ str_replace('_', ' ', $field) }}</div>
                <div class="text-sm font-medium text-gray-800 truncate">{{ $val ?? '—' }}</div>
            </div>
            @endforeach
        </div>
    </x-admin.card>
    @endif
</div>
@endsection
