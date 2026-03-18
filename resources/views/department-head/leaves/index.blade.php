@extends('department-head.layouts.app')

@section('title', 'Leave Recommendations')

@section('content')
<div class="mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leave Applications - Department Recommendations</h1>
                <p class="text-gray-600 mt-2">Review and recommend on employee leave applications (Section 7.B)</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending Recommendation</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <i class="fas fa-hourglass-half text-blue-300 text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Recommended</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['recommended'] ?? 0 }}</p>
                </div>
                <i class="fas fa-check-circle text-green-300 text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6 border-l-4 border-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total (Your Department)</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ $leaves->count() }}</p>
                </div>
                <i class="fas fa-sitemap text-purple-300 text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
            <div>
                <h3 class="font-semibold text-blue-900">Your Role</h3>
                <p class="text-sm text-blue-800 mt-1">
                    As the Department Head/Immediate Supervisor, you are responsible for Section 7.B recommendations.
                    Please review each certified application and provide your recommendation (For Approval or For Disapproval).
                </p>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($leaves->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Leave Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Days</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Certified</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $leave->user->first_name }} {{ $leave->user->last_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $leave->position }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $leave->getLeaveTypeDisplay() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $leave->start_date->format('M d, Y') }}
                                    @if($leave->end_date && $leave->end_date !== $leave->start_date)
                                        — {{ $leave->end_date->format('M d, Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $leave->days ?? 1 }} days</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($leave->certifiedBy)
                                        <span class="text-green-700 font-medium">
                                            <i class="fas fa-check mr-1"></i>{{ $leave->certified_at->format('M d, Y') }}
                                        </span>
                                        <p class="text-xs text-gray-600">By {{ $leave->certifiedBy->first_name }}</p>
                                    @else
                                        <span class="text-gray-400">
                                            <i class="fas fa-times mr-1"></i>Not certified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($leave->status === 'pending_recommendation')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-hourglass-half mr-1"></i> Awaiting Your Recommendation
                                        </span>
                                    @elseif($leave->status === 'pending_president_approval')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-crown mr-1"></i> Forwarded to President
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dept.leaves.show', $leave) }}" 
                                           class="text-blue-600 hover:text-blue-900 inline-flex items-center px-3 py-1 rounded-lg hover:bg-blue-50 transition"
                                           title="View Details">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>

                                        @if($leave->status === 'pending_recommendation')
                                            <a href="{{ route('dept.leaves.recommend-form', $leave) }}" 
                                               class="text-purple-600 hover:text-purple-900 inline-flex items-center px-3 py-1 rounded-lg hover:bg-purple-50 transition font-medium"
                                               title="Provide Recommendation">
                                                <i class="fas fa-thumbs-up mr-1"></i> Recommend
                                            </a>
                                        @elseif($leave->status === 'pending_president_approval')
                                            @if($leave->recommendedBy)
                                                <span class="text-gray-400 text-xs px-2 py-1 bg-gray-100 rounded">
                                                    <i class="fas fa-check mr-1"></i> Recommended
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leaves->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $leaves->links() }}
            </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4 inline-block"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leave Applications to Recommend</h3>
                <p class="text-gray-600">All certified leave applications in your department have been recommended.</p>
            </div>
        @endif
    </div>
</div>
@endsection
