@props(['responsive' => true])

<div class="{{ $responsive ? 'overflow-x-auto' : '' }} bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        {{ $slot }}
    </table>
</div>
