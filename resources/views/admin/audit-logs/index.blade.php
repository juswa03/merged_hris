@extends('admin.layouts.app')
@section('title', 'Audit Trail')
@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header title="Audit Trail" description="Track all create, update, and delete actions across the system"/>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Total Logs" :value="$stats['total']" icon="fas fa-list" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Today" :value="$stats['today']" icon="fas fa-calendar-day" gradientFrom="purple-500" gradientTo="purple-600"/>
        <x-admin.gradient-stat-card title="Created" :value="$stats['created']" icon="fas fa-plus-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Deleted" :value="$stats['deleted']" icon="fas fa-trash" gradientFrom="red-500" gradientTo="red-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-4">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-44">
                <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, model..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Action</label>
                <select name="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Actions</option>
                    @foreach(['created','updated','deleted','viewed'] as $a)
                        <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                <select name="model_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Models</option>
                    @foreach($modelTypes as $m)
                        <option value="{{ $m }}" {{ request('model_type') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">Reset</a>

            <!-- Clear old logs -->
            <form method="POST" action="{{ route('admin.audit-logs.clear') }}" class="flex items-end gap-2 ml-auto" onsubmit="return confirm('Delete matching old logs?')">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Clear older than</label>
                    <div class="flex gap-1">
                        <input type="number" name="older_than_days" value="90" min="7" class="w-20 border border-gray-300 rounded-lg px-2 py-2 text-sm">
                        <span class="flex items-center text-xs text-gray-500">days</span>
                    </div>
                </div>
                <button type="submit" class="px-3 py-2 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50">
                    <i class="fas fa-broom mr-1"></i>Clear
                </button>
            </form>
        </form>
    </x-admin.card>

    <x-admin.card>
        <x-admin.table-wrapper>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Record</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-gray-400">{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <div class="font-medium">{{ $log->user_name ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $log->actionBadgeClass() }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-800">{{ $log->model_type }}</div>
                            <div class="text-xs text-gray-500">{{ $log->model_label }} @if($log->model_id)<span class="text-gray-300">#{{ $log->model_id }}</span>@endif</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 text-xs">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-shield-alt text-3xl mb-3 block"></i>No audit logs found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrapper>
        <div class="p-4">{{ $logs->links() }}</div>
    </x-admin.card>
</div>
@endsection
