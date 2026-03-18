@extends('admin.layouts.app')

@section('title', 'Queue Monitor')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Queue Worker Monitor</h2>
            <p class="text-sm text-gray-500 mt-1">Watch pending and failed jobs, retry or clear failures.</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.queue-monitor.retry-all') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">
                    <i class="fas fa-redo"></i> Retry All
                </button>
            </form>
            <form method="POST" action="{{ route('admin.queue-monitor.clear-all') }}" onsubmit="return confirm('Clear all failed jobs?')">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm px-4 py-2 rounded-lg">
                    <i class="fas fa-trash"></i> Clear Failed
                </button>
            </form>
            <button onclick="location.reload()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="queue-stats">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="p-2 bg-indigo-50 rounded-lg"><i class="fas fa-layer-group text-indigo-600"></i></div>
                <span class="text-xs text-gray-500">Pending</span>
            </div>
            <p class="text-2xl font-bold text-gray-800" data-stat="pending">{{ number_format($stats['pending']) }}</p>
            <p class="text-xs text-gray-500">jobs waiting in queue</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="p-2 bg-red-50 rounded-lg"><i class="fas fa-exclamation-triangle text-red-600"></i></div>
                <span class="text-xs text-gray-500">Failed</span>
            </div>
            <p class="text-2xl font-bold text-gray-800" data-stat="failed">{{ number_format($stats['failed']) }}</p>
            <p class="text-xs text-gray-500">need manual action</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="p-2 bg-green-50 rounded-lg"><i class="fas fa-bolt text-green-600"></i></div>
                <span class="text-xs text-gray-500">Processed Today</span>
            </div>
            <p class="text-2xl font-bold text-gray-800" data-stat="processed_today">{{ number_format($stats['processed_today']) }}</p>
            <p class="text-xs text-gray-500">jobs handled</p>
        </div>
    </div>

    {{-- Pending jobs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Pending Jobs</h3>
            <span class="text-xs text-gray-500">Latest 100</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="px-4 py-2 text-left">Job</th>
                        <th class="px-4 py-2 text-left">Queue</th>
                        <th class="px-4 py-2 text-left">Attempts</th>
                        <th class="px-4 py-2 text-left">Available</th>
                        <th class="px-4 py-2 text-left">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingJobs as $job)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-800">{{ $job->display_name }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $job->queue ?? 'default' }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $job->attempts_count }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $job->available_at_human ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $job->created_at_human ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Queue is clear.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Failed jobs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Failed Jobs</h3>
            <span class="text-xs text-gray-500">Latest 100</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="px-4 py-2 text-left">Job</th>
                        <th class="px-4 py-2 text-left">Failed</th>
                        <th class="px-4 py-2 text-left">Error</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($failedJobs as $job)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-800">{{ $job->display_name }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $job->failed_at_human }}</td>
                        <td class="px-4 py-2 text-red-500 text-xs">{{ $job->exception_short }}</td>
                        <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.queue-monitor.retry', $job->uuid) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:underline text-sm">Retry</button>
                            </form>
                            <form method="POST" action="{{ route('admin.queue-monitor.delete-failed', $job->uuid) }}" class="inline" onsubmit="return confirm('Delete this failed job?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">No failed jobs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const statEls = {
        pending: document.querySelector('[data-stat="pending"]'),
        failed: document.querySelector('[data-stat="failed"]'),
        processed_today: document.querySelector('[data-stat="processed_today"]'),
    };

    async function refreshStats() {
        try {
            const res = await fetch('{{ route('admin.queue-monitor.stats') }}');
            if (!res.ok) return;
            const data = await res.json();
            Object.keys(statEls).forEach(key => {
                if (statEls[key]) statEls[key].textContent = new Intl.NumberFormat().format(data[key] ?? 0);
            });
        } catch (e) {
            // ignore fetch errors; UI still works
        }
    }

    setInterval(refreshStats, 30000);
</script>
@endpush
@endsection
