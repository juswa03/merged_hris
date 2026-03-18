@extends('admin.layouts.app')

@section('title', 'Backup Manager')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Backup Manager</h2>
            <p class="text-sm text-gray-500 mt-1">Create and download database or full system backups.</p>
        </div>
        <button onclick="location.reload()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 rounded-lg"><i class="fas fa-database text-blue-600"></i></div>
                <span class="text-xs text-gray-500">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
            <p class="text-xs text-gray-500">backups recorded</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-green-50 rounded-lg"><i class="fas fa-check-circle text-green-600"></i></div>
                <span class="text-xs text-gray-500">Completed</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['completed']) }}</p>
            <p class="text-xs text-gray-500">finished successfully</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-red-50 rounded-lg"><i class="fas fa-times-circle text-red-600"></i></div>
                <span class="text-xs text-gray-500">Failed</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['failed']) }}</p>
            <p class="text-xs text-gray-500">need attention</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 rounded-lg"><i class="fas fa-hdd text-indigo-600"></i></div>
                <span class="text-xs text-gray-500">Disk Used</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">
                {{ $stats['total_size'] ? number_format($stats['total_size'] / 1048576, 1) . ' MB' : '0 MB' }}
            </p>
            <p class="text-xs text-gray-500">across completed backups</p>
        </div>
    </div>

    {{-- Create backup --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fas fa-cloud-download-alt text-blue-600"></i> Create Backup
        </h3>
        <form method="POST" action="{{ route('admin.backups.create') }}" class="flex flex-col md:flex-row md:items-center gap-3">
            @csrf
            <div class="flex-1">
                <label class="text-xs text-gray-500">Backup type</label>
                <select name="type" class="mt-1 w-full border rounded-lg px-3 py-2">
                    <option value="database">Database only</option>
                    <option value="storage">Storage (public)</option>
                    <option value="full">Full (database + storage)</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg flex items-center gap-2">
                <i class="fas fa-play"></i> Run Backup
            </button>
        </form>
        <p class="text-xs text-gray-500 mt-2">
            Disk free: {{ number_format($diskFree / 1073741824, 1) }} GB / {{ number_format($diskTotal / 1073741824, 1) }} GB
        </p>
    </div>

    {{-- Backup list --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Backup History</h3>
            <span class="text-xs text-gray-500">Showing latest backups</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="px-4 py-2 text-left">File</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">Size</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Created</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-800">{{ $backup->filename }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full {{ $backup->type === 'full' ? 'bg-indigo-100 text-indigo-700' : ($backup->type === 'storage' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ ucfirst($backup->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-700">{{ $backup->size_human }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full {{ $backup->status === 'completed' ? 'bg-green-100 text-green-700' : ($backup->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($backup->status) }}
                            </span>
                            @if($backup->notes)
                                <p class="text-xs text-red-500 mt-1">{{ $backup->notes }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-gray-600">{{ $backup->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                            @if($backup->status === 'completed' && $backup->fileExists())
                                <a href="{{ route('admin.backups.download', $backup) }}" class="text-blue-600 hover:underline text-sm">Download</a>
                            @endif
                            <form action="{{ route('admin.backups.destroy', $backup) }}" method="POST" class="inline" onsubmit="return confirm('Delete this backup record and file?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">No backups yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $backups->links() }}</div>
    </div>
</div>
@endsection
