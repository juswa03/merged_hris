@props(['title', 'description' => null, 'actions' => null])

<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
            @if($description)
                <p class="mt-2 text-sm text-gray-600">{{ $description }}</p>
            @endif
        </div>
        @if($actions)
            <div class="flex flex-wrap gap-2">
                {!! $actions !!}
            </div>
        @endif
    </div>
</div>
