<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-1 text-gray-600 hover:text-blue-600">
        <i class="fas fa-bell text-xl"></i>

    </button>
    
    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 w-72 md:w-96 bg-white rounded-md shadow-lg py-1 z-50"
        style="display: none;"
    >
        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="text-sm font-medium">Notifications</h3>
            <button 
                id="markAllAsRead"
                class="text-xs text-blue-600 hover:text-blue-800"
            >
                Mark all as read
            </button>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
        
        </div>
        
        <div class="px-4 py-2 border-t text-center">
            <a 
                {{-- href="{{ route('notifications.index') }}"  --}}
                class="text-sm text-blue-600 hover:text-blue-800"
            >
                View all notifications
            </a>
        </div>
    </div>
</div>