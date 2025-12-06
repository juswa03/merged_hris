@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'iconOnly' => false,
    'href' => null
])

@php
    $variantClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white border-transparent',
        'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300',
        'success' => 'bg-green-600 hover:bg-green-700 text-white border-transparent',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white border-transparent',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white border-transparent',
        'info' => 'bg-blue-500 hover:bg-blue-600 text-white border-transparent',
    ];

    $sizeClasses = [
        'sm' => $iconOnly ? 'p-1.5' : 'px-3 py-1.5 text-sm',
        'md' => $iconOnly ? 'p-2' : 'px-4 py-2 text-sm',
        'lg' => $iconOnly ? 'p-3' : 'px-6 py-3 text-base',
    ];

    $variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];

    $baseClasses = 'inline-flex items-center justify-center border font-medium rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass"]) }}>
        @if($icon)
            <i class="{{ $icon }} {{ $iconOnly ? '' : 'mr-2' }}"></i>
        @endif
        @unless($iconOnly)
            {{ $slot }}
        @endunless
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => "$baseClasses $variantClass $sizeClass"]) }}>
        @if($icon)
            <i class="{{ $icon }} {{ $iconOnly ? '' : 'mr-2' }}"></i>
        @endif
        @unless($iconOnly)
            {{ $slot }}
        @endunless
    </button>
@endif
