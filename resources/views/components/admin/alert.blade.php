@props([
    'type' => 'info', // success, error, warning, info
    'title' => null,
    'dismissible' => false,
    'icon' => null
])

@php
    $typeClasses = [
        'success' => 'bg-green-50 border-green-400 text-green-800',
        'error' => 'bg-red-50 border-red-400 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-400 text-yellow-800',
        'info' => 'bg-blue-50 border-blue-400 text-blue-800',
    ];

    $iconClasses = [
        'success' => 'text-green-400',
        'error' => 'text-red-400',
        'warning' => 'text-yellow-400',
        'info' => 'text-blue-400',
    ];

    $defaultIcons = [
        'success' => 'fas fa-check-circle',
        'error' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle',
    ];

    $alertClass = $typeClasses[$type] ?? $typeClasses['info'];
    $iconClass = $iconClasses[$type] ?? $iconClasses['info'];
    $iconName = $icon ?? $defaultIcons[$type];
@endphp

<div class="rounded-md border-l-4 p-4 {{ $alertClass }} {{ $dismissible ? 'relative' : '' }}"
     @if($dismissible) x-data="{ show: true }" x-show="show" @endif>
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="{{ $iconName }} {{ $iconClass }}"></i>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium mb-1">{{ $title }}</h3>
            @endif
            <div class="text-sm">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <button @click="show = false" class="inline-flex rounded-md p-1.5 hover:bg-black hover:bg-opacity-10 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    </div>
</div>
