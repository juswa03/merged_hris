@extends('president.layouts.app')

@section('title', 'Leave Applications - Presidential Approval')

@section('content')
<div class="mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Leave Applications - Final Approval</h1>
        <p class="text-gray-600 mt-2">Section 7.C & 7.D - Presidential Review and Approval/Disapproval</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-clock text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Approved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Disapproved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['disapproved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] + $stats['approved'] + $stats['disapproved'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Your Role: Final Decision Authority
        </h3>
        <p class="text-sm text-blue-800">
            You are reviewing leave applications that have been certified by HR and recommended by the Department Head. 
            Your decision is final and will determine the approval status of each leave application.
        </p>
    </div>

    <!-- Leaves Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Pending Applications ({{ $leaves->total() }})</h2>
        </div>

        @if($leaves->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Employee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Leave Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Duration</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Days</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Dept. Recommendation</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $leave->user->first_name }} {{ $leave->user->last_name }}</div>
                            <div class="text-xs text-gray-600">{{ $leave->department }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $leave->getLeaveTypeDisplay() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $leave->start_date->format('M d') }} - 
                            @if($leave->end_date && $leave->end_date !== $leave->start_date)
                                {{ $leave->end_date->format('M d, Y') }}
                            @else
                                {{ $leave->start_date->format('M d, Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $leave->days ?? 1 }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($leave->recommendation_status === 'for_approval')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-thumbs-up mr-1"></i>For Approval
                                </span>
                            @elseif($leave->recommendation_status === 'for_disapproval')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-thumbs-down mr-1"></i>For Disapproval
                                </span>
                            @else
                                <span class="text-xs text-gray-600">Not yet recommended</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($leave->status === 'pending_president_approval')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-hourglass-half mr-1"></i>Pending
                                </span>
                            @elseif($leave->status === 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Approved
                                </span>
                            @elseif($leave->status === 'rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('president.leaves.show', $leave) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition"
                                   title="Review and decide">
                                    <i class="fas fa-eye mr-1"></i>Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $leaves->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center">
            <i class="fas fa-check-circle text-green-500 text-5xl mb-4 inline-block"></i>
            <p class="text-gray-600 text-lg">No pending leave applications</p>
            <p class="text-gray-500 text-sm mt-2">All leave applications have been reviewed.</p>
        </div>
        @endif
    </div>
</div>
@endsection
