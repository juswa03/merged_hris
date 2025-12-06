@props([
    'title',
    'value',
    'icon' => 'fas fa-chart-line',
    'gradientFrom' => 'blue-500',
    'gradientTo' => 'blue-600',
    'description' => null
])

<div class="group bg-gradient-to-br from-{{ $gradientFrom }} to-{{ $gradientTo }} rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-white/95 text-sm font-medium uppercase tracking-wide">{{ $title }}</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $value }}</p>
                @if($description)
                    <p class="text-white/90 text-xs mt-1">
                        {{ $description }}
                    </p>
                @endif
                @if($slot->isNotEmpty())
                    <div class="text-white/90 text-xs mt-1">
                        {{ $slot }}
                    </div>
                @endif
            </div>
            <div class="bg-white/20 p-4 rounded-xl">
                <i class="{{ $icon }} text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>
