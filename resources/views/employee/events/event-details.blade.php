@extends('layouts.employee')

@section('title', 'Event Details')

@section('content')
<main class="p-4 md:p-6">
    <div class="container mx-auto px-4">
        <div class="flex items-center mb-6">
            <a href="{{ route('employee.events') }}" class="text-blue-600 hover:text-blue-800 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold">Event Details</h1>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start">
                    <div class="p-3 rounded-full {{ $event->icon_bg_class }} mr-4">
                        <i class="{{ $event->icon }} text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ $event->title }}</h2>
                        <p class="text-gray-600">{{ $event->type }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Details</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Description</p>
                            <p class="mt-1">{{ $event->description ?? 'No description provided' }}</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Date</p>
                                <p class="mt-1">{{ $event->start_time->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Time</p>
                                <p class="mt-1">{{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Location</p>
                                <p class="mt-1">{{ $event->location }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Your Status</p>
                                <p class="mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $participantStatus === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                           ($participantStatus === 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($participantStatus ?? 'pending') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Update Status</h3>
                    <form action="{{ route('employee.events.status', $event) }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="status-confirmed" name="status" value="confirmed" 
                                    {{ $participantStatus === 'confirmed' ? 'checked' : '' }}
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                <label for="status-confirmed" class="ml-2 block text-sm text-gray-700">
                                    Confirm Attendance
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="status-declined" name="status" value="declined" 
                                    {{ $participantStatus === 'declined' ? 'checked' : '' }}
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                <label for="status-declined" class="ml-2 block text-sm text-gray-700">
                                    Decline Event
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection