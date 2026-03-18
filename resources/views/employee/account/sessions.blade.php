@extends('employee.layouts.app')
@section('title', 'Active Sessions')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employee.account.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-chevron-left mr-2"></i>Back to Account Settings
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Active Sessions</h1>
        <p class="text-gray-600 mt-2">Manage all devices logged into your account</p>
    </div>

    <div class="max-w-4xl">
        @if($sessions->count() > 0)
            <div class="space-y-4">
                @foreach($sessions as $session)
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                        <div class="flex items-start justify-between">
                            <div class="flex-grow">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-{{ $session->device_type === 'mobile' ? 'mobile-alt' : 'laptop' }} text-2xl text-blue-600 mr-4"></i>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $session->device_name ?? 'Unknown Device' }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $session->browser ?? 'Unknown Browser' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                    <div>
                                        <p class="text-gray-500">IP Address</p>
                                        <p class="font-medium">{{ $session->ip_address ?? 'Unknown' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Last Activity</p>
                                        <p class="font-medium">{{ $session->last_activity->diffForHumans() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Location</p>
                                        <p class="font-medium">{{ $session->location ?? 'Unknown' }}</p>
                                    </div>
                                </div>

                                @if($session->is_current)
                                    <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full bg-green-100">
                                        <i class="fas fa-check-circle text-green-600 mr-2 text-sm"></i>
                                        <span class="text-xs font-medium text-green-800">Current Session</span>
                                    </div>
                                @endif
                            </div>

                            @if(!$session->is_current)
                                <form action="{{ route('employee.account.sessions.logout', $session->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                        <i class="fas fa-sign-out-alt mr-2"></i>End Session
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    You can end any session to log out that device. If you see any unfamiliar devices, end that session and change your password immediately.
                </p>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-laptop text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-600">No active sessions found</p>
            </div>
        @endif
    </div>
</main>
@endsection
