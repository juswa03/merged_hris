@extends('admin.layouts.app')

@section('title', 'Login Sessions')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Login Sessions Monitor</h2>
            <p class="text-sm text-gray-500 mt-1">Track and manage active user login sessions.</p>
        </div>
        <form method="POST" action="{{ route('admin.login-sessions.revoke-all') }}"
              onsubmit="return confirm('Revoke ALL active sessions except your own?')">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg">
                <i class="fas fa-ban"></i> Revoke All Active
            </button>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Active Now', 'value' => $stats['active'], 'icon' => 'circle', 'color' => 'green'],
            ['label' => 'Logins Today', 'value' => $stats['today'], 'icon' => 'calendar-day', 'color' => 'blue'],
            ['label' => 'Revoked', 'value' => $stats['revoked'], 'icon' => 'ban', 'color' => 'red'],
            ['label' => 'Total Records', 'value' => $stats['total'], 'icon' => 'list', 'color' => 'gray'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
            <div class="p-2 rounded-lg bg-{{ $card['color'] }}-50">
                <i class="fas fa-{{ $card['icon'] }} text-{{ $card['color'] }}-500 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($card['value']) }}</p>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Search User</label>
                <input type="text" name="user" value="{{ request('user') }}"
                    placeholder="Name or email…"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none w-48">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="loggedout" {{ request('status') === 'loggedout' ? 'selected' : '' }}>Logged Out</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->anyFilled(['user','status']))
            <a href="{{ route('admin.login-sessions.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">
                Clear
            </a>
            @endif
        </form>
    </div>

    {{-- Sessions Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">IP Address</th>
                        <th class="px-4 py-3 text-left">Browser</th>
                        <th class="px-4 py-3 text-left">Login Time</th>
                        <th class="px-4 py-3 text-left">Last Activity</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sessions as $session)
                    @php
                        $status = $session->status_label;
                        $badge = match($status) {
                            'Active'     => 'bg-green-100 text-green-700',
                            'Revoked'    => 'bg-red-100 text-red-700',
                            'Expired'    => 'bg-gray-100 text-gray-600',
                            'Logged Out' => 'bg-blue-100 text-blue-700',
                            default      => 'bg-gray-100 text-gray-600',
                        };
                        $isCurrentUser = $session->user_id === auth()->id();
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isCurrentUser ? 'bg-blue-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs">
                                    {{ strtoupper(substr($session->user->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $session->user->first_name ?? '' }} {{ $session->user->last_name ?? '' }}
                                        @if($isCurrentUser)
                                        <span class="text-xs text-blue-500">(you)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $session->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $session->ip_address }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            <i class="fas fa-globe mr-1 text-gray-400"></i>{{ $session->browser }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ $session->logged_in_at?->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ $session->last_activity_at?->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $badge }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($status === 'Active' && !$isCurrentUser)
                                <form method="POST" action="{{ route('admin.login-sessions.revoke', $session) }}"
                                      onsubmit="return confirm('Force-logout this session?')">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 px-2 py-1 rounded-lg">
                                        <i class="fas fa-ban mr-1"></i>Revoke
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.login-sessions.destroy', $session) }}"
                                      onsubmit="return confirm('Delete this session record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-gray-400 hover:text-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            <i class="fas fa-desktop text-3xl mb-2 block"></i>
                            No sessions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sessions->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
