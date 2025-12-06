@props([
    'icon' => 'fas fa-inbox',
    'title' => 'No data found',
    'message' => '',
    'action' => null,
    'actionText' => null,
    'actionLink' => null
])

<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
        <i class="{{ $icon }} text-3xl text-gray-400"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $title }}</h3>
    @if($message)
        <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">{{ $message }}</p>
    @endif
    @if($action)
        <div>
            {{ $action }}
        </div>
    @elseif($actionLink && $actionText)
        <a href="{{ $actionLink }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-150">
            <i class="fas fa-plus mr-2"></i>
            {{ $actionText }}
        </a>
    @endif
</div>
