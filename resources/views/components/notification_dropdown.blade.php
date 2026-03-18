<div class="relative" x-data="notificationDropdown()">
    <button @click="toggle()" class="relative p-1 text-gray-600 hover:text-blue-600">
        <i class="fas fa-bell text-xl"></i>
        <span x-show="unreadCount > 0" class="notification-counter absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold rounded-full px-1" x-text="unreadCount"></span>
    </button>
    
    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 w-72 md:w-96 bg-white rounded-md shadow-lg py-1 z-50"
        style="display: none;"
    >
        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="text-sm font-medium">Notifications</h3>
            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" @submit.prevent="markAll()">
                @csrf
                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Mark all as read</button>
            </form>
        </div>
        
        <div class="max-h-96 overflow-y-auto" x-ref="list">
            <template x-if="items.length === 0">
                <div class="p-6 text-center text-gray-400 text-sm">No notifications</div>
            </template>
            <template x-for="item in items" :key="item.id">
                <div class="px-4 py-3 border-b last:border-b-0 hover:bg-gray-50 flex gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="badgeClass(item.type)">
                        <i :class="iconClass(item.type)"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800" x-text="item.title"></div>
                        <div class="text-xs text-gray-600" x-text="item.message"></div>
                        <div class="text-[10px] text-gray-400 mt-1" x-text="item.time"></div>
                        <a x-show="item.link" :href="item.link" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <form x-show="!item.read" method="POST" :action="'{{ url('/admin/notifications') }}/' + item.id + '/mark-read'" @submit.prevent="markRead(item.id)">
                            @csrf
                            <button type="submit" class="text-[11px] text-blue-600 hover:underline">Mark</button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
        
        <div class="px-4 py-2 border-t text-center">
            <a href="{{ route('admin.notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        items: [],
        unreadCount: 0,
        toggle() {
            this.open = !this.open;
            if (this.open) this.load();
        },
        badgeClass(type) {
            return {
                info: 'bg-blue-100 text-blue-700',
                success: 'bg-green-100 text-green-700',
                warning: 'bg-yellow-100 text-yellow-700',
                danger: 'bg-red-100 text-red-700',
            }[type || 'info'];
        },
        iconClass(type) {
            return {
                success: 'fas fa-check',
                warning: 'fas fa-exclamation',
                danger: 'fas fa-times',
                info: 'fas fa-info',
            }[type || 'info'] + ' text-sm';
        },
        async load() {
            const recent = await fetch('{{ route('admin.notifications.recent') }}').then(r => r.json());
            const count  = await fetch('{{ route('admin.notifications.unread-count') }}').then(r => r.json());
            this.items = recent;
            this.unreadCount = count.count || 0;
        },
        async markAll() {
            await fetch('{{ route('admin.notifications.mark-all-read') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            this.unreadCount = 0;
            this.items = this.items.map(i => ({ ...i, read: true }));
        },
        async markRead(id) {
            await fetch('{{ url('/admin/notifications') }}/' + id + '/mark-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            this.items = this.items.map(i => i.id === id ? { ...i, read: true } : i);
            this.unreadCount = Math.max(0, this.unreadCount - 1);
        }
    }
}
</script>