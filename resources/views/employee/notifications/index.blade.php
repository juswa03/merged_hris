@extends('employee.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Notifications</h2>
        @if($notifications->total() > 0)
        <form action="{{ route('employee.notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit"
                    class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @forelse($notifications as $notification)
    <div class="bg-white rounded-xl shadow-sm border {{ $notification->read_at ? 'border-gray-100' : 'border-blue-200' }} p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="mt-1 p-2 rounded-full {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }}">
                    <i class="fas fa-bell text-sm {{ $notification->read_at ? 'text-gray-400' : 'text-blue-500' }}"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-800 {{ $notification->read_at ? '' : 'font-semibold' }}">
                        {{ $notification->data['message'] ?? 'You have a new notification.' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            @unless($notification->read_at)
            <form action="{{ route('employee.notifications.mark-read', $notification->id) }}" method="POST">
                @csrf
                <button type="submit" title="Mark as read"
                        class="text-xs text-blue-600 hover:text-blue-800 whitespace-nowrap">
                    Mark read
                </button>
            </form>
            @endunless
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">No notifications yet.</p>
    </div>
    @endforelse

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
