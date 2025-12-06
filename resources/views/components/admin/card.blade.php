@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'noBorder' => false
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow rounded-lg' . ($noBorder ? '' : ' border border-gray-200')]) }}>
    @if($title)
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @if($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endisset
</div>
