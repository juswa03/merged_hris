@extends('admin.layouts.app')

@section('title', 'System Health')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">System Health Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">Real-time overview of server, database, cache, and queue status.</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.system-health.clear-cache') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">
                    <i class="fas fa-broom"></i> Clear Cache
                </button>
            </form>
            <button onclick="location.reload()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Disk --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-hdd text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $diskPercent < 70 ? 'bg-green-100 text-green-700' : ($diskPercent < 90 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ $diskPercent }}%
                </span>
            </div>
            <p class="text-sm text-gray-500">Disk Usage</p>
            <p class="text-lg font-bold text-gray-800 mt-1">
                {{ number_format($diskUsed / 1073741824, 1) }} GB / {{ number_format($diskTotal / 1073741824, 1) }} GB
            </p>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all
                    {{ $diskPercent < 70 ? 'bg-green-500' : ($diskPercent < 90 ? 'bg-yellow-500' : 'bg-red-500') }}"
                    style="width: {{ $diskPercent }}%"></div>
            </div>
        </div>

        {{-- Cache --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <i class="fas fa-memory text-purple-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $cacheStatus === 'Operational' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $cacheStatus }}
                </span>
            </div>
            <p class="text-sm text-gray-500">Cache Status</p>
            <p class="text-lg font-bold text-gray-800 mt-1">{{ config('cache.default') }}</p>
            <p class="text-xs text-gray-400 mt-1">Driver: {{ config('cache.default') }}</p>
        </div>

        {{-- Queue / Pending Jobs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <i class="fas fa-layer-group text-indigo-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $pendingJobs === 0 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $pendingJobs === 0 ? 'Clear' : $pendingJobs . ' pending' }}
                </span>
            </div>
            <p class="text-sm text-gray-500">Pending Jobs</p>
            <p class="text-lg font-bold text-gray-800 mt-1">{{ number_format($pendingJobs) }}</p>
            <p class="text-xs text-gray-400 mt-1">Driver: {{ config('queue.default') }}</p>
        </div>

        {{-- Failed Jobs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-red-50 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $failedJobs === 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $failedJobs === 0 ? 'None' : $failedJobs . ' failed' }}
                </span>
            </div>
            <p class="text-sm text-gray-500">Failed Jobs</p>
            <p class="text-lg font-bold text-gray-800 mt-1">{{ number_format($failedJobs) }}</p>
            @if($failedJobs > 0)
            <form method="POST" action="{{ route('admin.system-health.clear-failed') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-xs text-red-600 hover:underline">Clear all failed jobs</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Database + Log Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Database --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-database text-blue-500"></i> Database
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Connection</span>
                    <span class="font-medium text-gray-800">{{ config('database.default') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Size</span>
                    <span class="font-medium text-gray-800">{{ $dbSizeMb }} MB</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tables</span>
                    <span class="font-medium text-gray-800">{{ $tableCount }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Status</span>
                    <span class="text-green-600 font-medium flex items-center gap-1">
                        <i class="fas fa-circle text-xs"></i> Connected
                    </span>
                </div>
            </div>
        </div>

        {{-- Log File --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-file-alt text-yellow-500"></i> Application Log
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Log Size</span>
                    <span class="font-medium {{ $logSizeKb > 10240 ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $logSizeKb > 1024 ? number_format($logSizeKb / 1024, 1) . ' MB' : $logSizeKb . ' KB' }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Environment</span>
                    <span class="font-medium text-gray-800">{{ $info['env'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Debug Mode</span>
                    <span class="font-medium {{ $info['debug_mode'] ? 'text-yellow-600' : 'text-green-600' }}">
                        {{ $info['debug_mode'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>
            @if($logSizeKb > 0)
            <form method="POST" action="{{ route('admin.system-health.clear-log') }}" class="mt-4"
                  onsubmit="return confirm('Clear the entire laravel.log file?')">
                @csrf
                <button type="submit" class="text-xs bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 px-3 py-1.5 rounded-lg">
                    <i class="fas fa-trash-alt mr-1"></i> Clear Log File
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Server Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-server text-gray-500"></i> Server Information
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">PHP Version</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $info['php_version'] }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Laravel Version</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $info['laravel_version'] }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Timezone</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $info['timezone'] }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Session Driver</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $info['driver_session'] }}</p>
            </div>
        </div>
    </div>

    {{-- Recent Failed Jobs --}}
    @if($recentFailedJobs->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-bug text-red-500"></i> Recent Failed Jobs
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="px-4 py-2 text-left">Queue</th>
                        <th class="px-4 py-2 text-left">Job</th>
                        <th class="px-4 py-2 text-left">Failed At</th>
                        <th class="px-4 py-2 text-left">Exception</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentFailedJobs as $job)
                    @php
                        $payload = json_decode($job->payload, true);
                        $jobName = $payload['displayName'] ?? class_basename($payload['job'] ?? 'Unknown');
                        $exception = str_replace("\n", ' ', substr($job->exception ?? '', 0, 120));
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-600">{{ $job->queue }}</td>
                        <td class="px-4 py-2 font-mono text-xs text-gray-800">{{ $jobName }}</td>
                        <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ $job->failed_at }}</td>
                        <td class="px-4 py-2 text-red-500 text-xs max-w-xs truncate">{{ $exception }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
