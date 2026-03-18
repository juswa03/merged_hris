@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <x-admin.page-header 
        title="Notification Center" 
        description="View and manage your system notifications and announcements."
    >
        <x-slot name="actions">
            @if(isset($unreadCount) && $unreadCount > 0)
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="inline-block">
                    @csrf
                    <x-admin.action-button type="submit" variant="secondary" icon="check-double">
                        Mark All Read
                    </x-admin.action-button>
                </form>
            @endif

            <x-admin.action-button 
                x-data 
                @click="$dispatch('open-modal-sendNotification')"
                variant="primary" 
                icon="paper-plane"
            >
                Send Notification
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    {{-- Stats / Alert --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($unreadCount) && $unreadCount > 0 && !session('success'))
        <div class="rounded-md bg-blue-50 p-4 border border-blue-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-bell text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">
                        You have <span class="font-bold">{{ $unreadCount }}</span> unread notification(s).
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <x-admin.card>
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                @php
                    $data     = $notification->data;
                    $isRead   = !is_null($notification->read_at);
                    $type     = $data['type'] ?? 'info';
                    
                    $iconClass = match($type) {
                        'success' => 'fa-check-circle text-green-500',
                        'warning' => 'fa-exclamation-triangle text-yellow-500',
                        'danger'  => 'fa-times-circle text-red-500',
                        default   => 'fa-info-circle text-blue-500',
                    };

                    $bgClass = match($type) {
                        'success' => 'bg-green-100',
                        'warning' => 'bg-yellow-100',
                        'danger'  => 'bg-red-100',
                        default   => 'bg-blue-100',
                    };
                @endphp
                <li class="relative p-4 hover:bg-gray-50 transition-colors duration-150 {{ $isRead ? '' : 'bg-blue-50/50' }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 pt-0.5">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full {{ $bgClass }}">
                                <i class="fas {{ $iconClass }}"></i>
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $data['title'] ?? 'Notification' }}
                                </p>
                                <div class="ml-2 flex flex-shrink-0 items-center gap-3">
                                    <p class="text-xs text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                    
                                     {{-- Delete Action --}}
                                    <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-gray-600">
                                    {{ $data['message'] ?? '' }}
                                </p>
                            </div>
                            
                            {{-- Actions/Links --}}
                            <div class="mt-2 flex items-center gap-4">
                                @if(!empty($data['link']))
                                    <a href="{{ $data['link'] }}" class="text-xs font-medium text-blue-600 hover:text-blue-500 inline-flex items-center">
                                        View details <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                                    </a>
                                @endif

                                @if(!$isRead)
                                    <form method="POST" action="{{ route('admin.notifications.mark-read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-700">
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <i class="fas fa-bell-slash text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-gray-500 text-sm">You're all caught up!</p>
                </div>
            @endforelse
        </ul>
        
        @if($notifications->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-admin.card>
</div>

{{-- Send Notification Modal --}}
<x-admin.modal name="sendNotification" title="Send New Notification" maxWidth="lg">
    <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-4" x-data="{ target: 'all' }">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" required maxlength="200"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                   placeholder="e.g., System Maintenance">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Message <span class="text-red-500">*</span></label>
            <textarea name="message" required rows="3" maxlength="1000"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      placeholder="Enter the notification details..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="info">Info (Blue)</option>
                    <option value="success">Success (Green)</option>
                    <option value="warning">Warning (Yellow)</option>
                    <option value="danger">Danger (Red)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Recipients</label>
                <select name="target" x-model="target"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="all">All Users</option>
                    <option value="admins">Admins Only</option>
                    <option value="single">Single User</option>
                </select>
            </div>
        </div>

        {{-- Conditional User ID Input --}}
        <div x-show="target === 'single'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">
            <label class="block text-sm font-medium text-gray-700">User ID</label>
            <input type="number" name="user_id" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                   placeholder="Enter User ID">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Link URL (Optional)</label>
            <input type="url" name="link" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                   placeholder="https://...">
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 mt-4">
            <x-admin.action-button type="button" variant="secondary" @click="$dispatch('close-modal-sendNotification')">
                Cancel
            </x-admin.action-button>
            <x-admin.action-button type="submit" variant="primary" icon="paper-plane">
               Send Notification
            </x-admin.action-button>
        </div>
    </form>
</x-admin.modal>
@endsection
