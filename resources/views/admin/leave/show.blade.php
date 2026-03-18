@extends('admin.layouts.app')

@section('title', 'Leave Application Details')

@section('content')
<div class="mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leave Application Details</h1>
                <p class="text-gray-600 mt-2">Review and manage employee leave application</p>
            </div>
            <a href="{{ route('admin.leave.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Employee Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Employee Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Name</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $leave->user->first_name }} {{ $leave->user->last_name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Position</label>
                        <p class="mt-1 text-gray-900">{{ $leave->position }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Department</label>
                        <p class="mt-1 text-gray-900">{{ $leave->department }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Filing Date</label>
                        <p class="mt-1 text-gray-900">{{ $leave->filing_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Leave Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Leave Type</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $leave->getLeaveTypeDisplay() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Number of Days</label>
                        <p class="mt-1 text-gray-900 font-medium text-lg">{{ $leave->days ?? 1 }} days</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Start Date</label>
                        <p class="mt-1 text-gray-900">{{ $leave->start_date->format('M d, Y') }}</p>
                    </div>
                    @if($leave->end_date && $leave->end_date !== $leave->start_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">End Date</label>
                        <p class="mt-1 text-gray-900">{{ $leave->end_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Reason</label>
                    <p class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $leave->reason }}</p>
                </div>
            </div>

            <!-- Certification Section -->
            @if(!$leave->certifiedBy)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-yellow-900 mb-2">
                    <i class="fas fa-hourglass-half mr-2"></i>Awaiting Certification
                </h3>
                <p class="text-sm text-yellow-800 mb-4">
                    This leave application is pending HR certification. Please verify the leave credits and provide Section 7.A certification.
                </p>
                <a href="{{ route('admin.leave.certify', $leave) }}" 
                   class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition">
                    <i class="fas fa-certificate mr-2"></i>Certify Leave Credits (Section 7.A)
                </a>
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-green-900 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>Section 7.A - HR Certification Complete
                </h3>
                <div class="text-sm text-green-800 space-y-2">
                    <p><strong>Certified by:</strong> {{ $leave->certifiedBy->first_name }} {{ $leave->certifiedBy->last_name }}</p>
                    <p><strong>Certified on:</strong> {{ $leave->certified_at->format('M d, Y \a\t H:i') }}</p>
                    
                    @if($leave->vacation_earned !== null || $leave->sick_earned !== null)
                    <div class="mt-3 pt-3 border-t border-green-200">
                        <p><strong>Leave Credits Verified:</strong></p>
                        <table class="w-full mt-2 text-xs">
                            <tr class="bg-green-100">
                                <td class="border border-green-200 p-2 font-medium">Leave Type</td>
                                <td class="border border-green-200 p-2 font-medium">Total Earned</td>
                                <td class="border border-green-200 p-2 font-medium">Less This App</td>
                                <td class="border border-green-200 p-2 font-medium">Balance</td>
                            </tr>
                            @if($leave->vacation_earned !== null)
                            <tr>
                                <td class="border border-green-200 p-2">Vacation Leave</td>
                                <td class="border border-green-200 p-2">{{ $leave->vacation_earned }}</td>
                                <td class="border border-green-200 p-2">{{ $leave->vacation_less }}</td>
                                <td class="border border-green-200 p-2 font-medium">{{ $leave->vacation_balance }}</td>
                            </tr>
                            @endif
                            @if($leave->sick_earned !== null)
                            <tr>
                                <td class="border border-green-200 p-2">Sick Leave</td>
                                <td class="border border-green-200 p-2">{{ $leave->sick_earned }}</td>
                                <td class="border border-green-200 p-2">{{ $leave->sick_less }}</td>
                                <td class="border border-green-200 p-2 font-medium">{{ $leave->sick_balance }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recommendation Status -->
            @if($leave->status === 'pending_recommendation')
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-hourglass-half mr-2"></i>Awaiting Supervisor Recommendation
                </h3>
                <p class="text-sm text-blue-800">
                    This application is now awaiting recommendation from the Department Head/Supervisor (Section 7.B).
                </p>
            </div>
            @elseif($leave->recommendedBy)
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-900 mb-3">
                    <i class="fas fa-thumbs-up mr-2"></i>Section 7.B - Supervisor Recommendation Complete
                </h3>
                <div class="text-sm text-purple-800 space-y-1">
                    <p><strong>Recommended by:</strong> {{ $leave->recommendedBy->first_name }} {{ $leave->recommendedBy->last_name }}</p>
                    <p><strong>Recommended on:</strong> {{ $leave->recommended_at->format('M d, Y \a\t H:i') }}</p>
                    <p><strong>Recommendation:</strong> 
                        @if($leave->recommendation_status === 'for_approval')
                            <span class="text-green-700 font-medium">For Approval</span>
                        @elseif($leave->recommendation_status === 'for_disapproval')
                            <span class="text-red-700 font-medium">For Disapproval</span>
                        @endif
                    </p>
                    @if($leave->recommendation_reason)
                    <p class="mt-2"><strong>Reason:</strong> {{ $leave->recommendation_reason }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Application Status</h3>
                
                <div class="mb-4">
                    @if($leave->status === 'pending')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-hourglass-half mr-2"></i> Pending Certification
                        </span>
                    @elseif($leave->status === 'pending_recommendation')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-check-double mr-2"></i> Pending Recommendation
                        </span>
                    @elseif($leave->status === 'pending_president_approval')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-crown mr-2"></i> Pending President Approval
                        </span>
                    @elseif($leave->status === 'approved')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i> Approved
                        </span>
                    @elseif($leave->status === 'rejected')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i> Rejected
                        </span>
                    @endif
                </div>

                <hr class="my-4">

                <!-- Workflow Timeline -->
                <div class="space-y-4 text-sm">
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 {{ $leave->certifiedBy ? 'bg-green-500' : 'bg-gray-300' }} text-white text-xs rounded-full mr-3 flex-shrink-0 mt-1">
                            <i class="fas {{ $leave->certifiedBy ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Section 7.A - HR Certification</p>
                            <p class="text-xs text-gray-600">{{ $leave->certifiedBy ? 'Completed' : 'In Progress (Your Action)' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 {{ $leave->recommendedBy ? 'bg-green-500' : 'bg-gray-300' }} text-white text-xs rounded-full mr-3 flex-shrink-0 mt-1">
                            <i class="fas {{ $leave->recommendedBy ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Section 7.B - Supervisor Recommendation</p>
                            <p class="text-xs text-gray-600">{{ $leave->recommendedBy ? 'Completed' : 'Pending' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 {{ in_array($leave->status, ['approved', 'rejected']) ? 'bg-green-500' : 'bg-gray-300' }} text-white text-xs rounded-full mr-3 flex-shrink-0 mt-1">
                            <i class="fas {{ in_array($leave->status, ['approved', 'rejected']) ? 'fa-check' : 'fa-crown' }}"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Section 7.C & 7.D - President Approval</p>
                            <p class="text-xs text-gray-600">{{ in_array($leave->status, ['approved', 'rejected']) ? 'Completed' : 'Pending' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Quick Info</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="block text-gray-600 font-medium">Submitted</label>
                        <p class="text-gray-900">{{ $leave->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 font-medium">Leave Type</label>
                        <p class="text-gray-900">{{ $leave->getLeaveTypeDisplay() }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 font-medium">Days</label>
                        <p class="text-gray-900">{{ $leave->days ?? 1 }} days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
