@props([
    'variant' => 'default', // default, success, warning, danger, info, primary
    'size' => 'md', // sm, md, lg
    'dot' => false
])

@php
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'primary' => 'bg-blue-600 text-white',
    ];

    $dotClasses = [
        'default' => 'bg-gray-400',
        'success' => 'bg-green-400',
        'warning' => 'bg-yellow-400',
        'danger' => 'bg-red-400',
        'info' => 'bg-blue-400',
        'primary' => 'bg-white',
    ];

    $sizeClasses = [
        'sm' => 'text-xs px-2 py-0.5',
        'md' => 'text-xs px-2.5 py-0.5',
        'lg' => 'text-sm px-3 py-1',
    ];

    $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $dotClass = $dotClasses[$variant] ?? $dotClasses['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full $variantClass $sizeClass"]) }}>
    @if($dot)
        <svg class="mr-1.5 h-2 w-2 {{ $dotClass }}" fill="currentColor" viewBox="0 0 8 8">
            <circle cx="4" cy="4" r="3" />
        </svg>
    @endif
    {{ $slot }}
</span>
