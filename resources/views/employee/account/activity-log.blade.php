@extends('employee.layouts.app')
@section('title', 'Activity Log')
@section('content')
<main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employee.account.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-chevron-left mr-2"></i>Back to Account Settings
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Activity Log</h1>
        <p class="text-gray-600 mt-2">Review all login activity on your account</p>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Login Activity</h3>
            </div>

            @if($loginHistory->count() > 0)
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Date & Time</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">IP Address</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Device</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($loginHistory as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $activity->created_at->format('M d, Y - h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $activity->ip_address ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <i class="fas fa-{{ $activity->user_agent ? 'desktop' : 'mobile' }} mr-2"></i>
                                        {{ Str::limit($activity->user_agent ?? 'Unknown', 30) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $activity->status === 'success' ? '✓ Success' : '✗ Failed' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($loginHistory->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $loginHistory->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600">No login activity found</p>
                </div>
            @endif
        </div>

        <!-- Info Box -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                This log shows all successful login attempts to your account. If you see any suspicious activity, please change your password immediately.
            </p>
        </div>
    </div>
</main>
@endsection
