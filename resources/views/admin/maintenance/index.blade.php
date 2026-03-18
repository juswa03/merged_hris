@extends('admin.layouts.app')

@section('title', 'Maintenance Mode')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Maintenance Mode Manager</h2>
            <p class="text-sm text-gray-500 mt-1">Control system availability for regular users. Admins are never blocked.</p>
        </div>
        {{-- Toggle Button --}}
        <form method="POST" action="{{ route('admin.maintenance.toggle') }}"
              onsubmit="return confirm('{{ $settings->is_active ? 'Disable maintenance mode?' : 'Enable maintenance mode? Regular users will see the maintenance page immediately.' }}')">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 text-sm font-medium px-5 py-2.5 rounded-xl shadow-sm transition-colors
                    {{ $settings->is_active
                        ? 'bg-green-600 hover:bg-green-700 text-white'
                        : 'bg-red-600 hover:bg-red-700 text-white' }}">
                <i class="fas {{ $settings->is_active ? 'fa-circle-check' : 'fa-power-off' }}"></i>
                {{ $settings->is_active ? 'Disable Maintenance' : 'Enable Maintenance' }}
            </button>
        </form>
    </div>

    {{-- Status Banner --}}
    <div class="rounded-xl p-4 flex items-center gap-4 border
        {{ $settings->is_active
            ? 'bg-red-50 border-red-200'
            : 'bg-green-50 border-green-200' }}">
        <div class="text-2xl">{{ $settings->is_active ? '🔴' : '🟢' }}</div>
        <div>
            <p class="font-semibold {{ $settings->is_active ? 'text-red-700' : 'text-green-700' }}">
                Maintenance Mode is currently <strong>{{ $settings->is_active ? 'ACTIVE' : 'INACTIVE' }}</strong>
            </p>
            @if($settings->is_active && $settings->scheduled_end_at)
            <p class="text-sm text-red-600 mt-0.5">
                Scheduled to end: {{ $settings->scheduled_end_at->format('M d, Y h:i A') }}
            </p>
            @endif
        </div>
    </div>

    {{-- Settings Form --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
            <i class="fas fa-sliders text-blue-500"></i> Maintenance Page Settings
        </h3>

        <form method="POST" action="{{ route('admin.maintenance.update') }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Page Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $settings->title) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    placeholder="e.g. Scheduled Maintenance" required>
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Message to Users <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="4"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none resize-none"
                    placeholder="We are currently performing scheduled maintenance…" required>{{ old('message', $settings->message) }}</textarea>
                @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Whitelisted IPs
                        <span class="text-gray-400 font-normal">(comma-separated)</span>
                    </label>
                    <input type="text" name="whitelisted_ips"
                        value="{{ old('whitelisted_ips', $settings->whitelisted_ips) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none font-mono"
                        placeholder="127.0.0.1, 192.168.1.100">
                    <p class="text-xs text-gray-400 mt-1">Your current IP: <strong>{{ request()->ip() }}</strong></p>
                    @error('whitelisted_ips')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Scheduled End Date/Time
                        <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="datetime-local" name="scheduled_end_at"
                        value="{{ old('scheduled_end_at', $settings->scheduled_end_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Auto-disables when this time is reached.</p>
                    @error('scheduled_end_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl shadow-sm">
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Live Preview --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-eye text-purple-500"></i> Page Preview
        </h3>
        <div class="rounded-xl overflow-hidden border border-gray-200" style="min-height:220px; background: linear-gradient(135deg,#1e3a8a,#3b82f6); display:flex; align-items:center; justify-content:center;">
            <div style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:1.25rem;padding:2rem;max-width:400px;width:90%;text-align:center;color:#fff;">
                <div style="font-size:2.5rem;margin-bottom:1rem">🔧</div>
                <div style="font-size:1.2rem;font-weight:700;margin-bottom:0.75rem" id="preview-title">{{ $settings->title }}</div>
                <div style="font-size:0.9rem;opacity:0.9;line-height:1.6" id="preview-message">{{ $settings->message }}</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Live preview update
document.querySelector('[name="title"]').addEventListener('input', function () {
    document.getElementById('preview-title').textContent = this.value || 'System Maintenance';
});
document.querySelector('[name="message"]').addEventListener('input', function () {
    document.getElementById('preview-message').textContent = this.value;
});
</script>
@endpush
