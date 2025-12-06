@props([
    'title',
    'value',
    'icon' => 'fas fa-chart-line',
    'iconColor' => 'blue',
    'trend' => null,
    'trendUp' => true,
    'link' => null
])

@php
    $iconColorClasses = [
        'blue' => 'bg-blue-100 text-blue-600',
        'green' => 'bg-green-100 text-green-600',
        'yellow' => 'bg-yellow-100 text-yellow-600',
        'red' => 'bg-red-100 text-red-600',
        'purple' => 'bg-purple-100 text-purple-600',
        'indigo' => 'bg-indigo-100 text-indigo-600',
        'orange' => 'bg-orange-100 text-orange-600',
    ];

    $iconClass = $iconColorClasses[$iconColor] ?? $iconColorClasses['blue'];
@endphp

<div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md {{ $iconClass }}">
                    <i class="{{ $icon }} text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $value }}
                        </div>
                        @if($trend)
                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $trendUp ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas fa-arrow-{{ $trendUp ? 'up' : 'down' }} mr-1"></i>
                                {{ $trend }}
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    @if($link)
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ $link }}" class="font-medium text-blue-600 hover:text-blue-500">
                    View details <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    @endif
</div>
