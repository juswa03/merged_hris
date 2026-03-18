@extends('employee.layouts.app')

@section('title', 'Travel Authority Details')

@section('content')
<div class="mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Travel Authority Details</h1>
            <a href="{{ route('employees.travel') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <!-- Travel Authority Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Travel Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Travel Authority No.</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $travel->travel_authority_no ?? 'Pending Assignment' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Designation</label>
                        <p class="mt-1 text-gray-900">{{ $travel->designation }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Destination</label>
                        <p class="mt-1 text-gray-900">{{ $travel->destination }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Travel Type</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            @if($travel->travel_type)
                                {{ str_replace('_', ' ', $travel->travel_type) }}
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Duration Type</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            @if($travel->duration_type)
                                {{ str_replace('_', ' ', $travel->duration_type) }}
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Inclusive Date of Travel</label>
                        <p class="mt-1 text-gray-900">
                            {{ $travel->inclusive_date_of_travel->format('F d, Y') }}
                        </p>
                    </div>

                    @if($travel->duration_type === 'multiple_days')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Travel Period</label>
                        <p class="mt-1 text-gray-900">
                            {{ $travel->start_date->format('M d, Y') }} to {{ $travel->end_date->format('M d, Y') }}
                        </p>
                    </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Transportation</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            @if($travel->transportation)
                                {{ str_replace('_', ' ', $travel->transportation) }}
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Estimated Expenses</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            @if($travel->estimated_expenses)
                                {{ str_replace('_', ' ', $travel->estimated_expenses) }}
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Source of Funds</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            @if($travel->source_of_funds)
                                @if($travel->source_of_funds === 'mooe')
                                    MOOE (Maintenance and Other Operating Expenses)
                                @elseif($travel->source_of_funds === 'personal')
                                    Personal Funds
                                @elseif($travel->source_of_funds === 'other')
                                    Other Sources
                                @else
                                    {{ str_replace('_', ' ', $travel->source_of_funds) }}
                                @endif
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Purpose</label>
                <p class="mt-1 text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $travel->purpose }}</p>
            </div>

            @if($travel->other_funds_specification)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Other Funds Specification</label>
                <p class="mt-1 text-gray-900">{{ $travel->other_funds_specification }}</p>
            </div>
            @endif
        </div>

        <!-- 5-Stage Approval Workflow -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">5-Stage Approval Workflow</h2>
            
            <!-- Workflow Progress Bar -->
            <div class="mb-8">
                <div class="relative">
                    <!-- Progress Line -->
                    @php
                        $completedStages = $travel->approvals->where('status', 'approved')->count();
                        $progressPercent = ($completedStages / 4) * 100;
                    @endphp
                    <div class="absolute top-5 left-0 w-full h-1 bg-gray-200">
                        <div class="h-full bg-blue-500 transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    
                    <!-- Stage Circles -->
                    <div class="flex justify-between relative z-10">
                        @php
                            $stages = [
                                1 => ['label' => 'Finance Officer', 'type' => 'finance_officer_approval', 'role' => 'Finance Officer'],
                                2 => ['label' => 'Accountant', 'type' => 'accountant_approval', 'role' => 'Accountant'],
                                3 => ['label' => 'Dept Head', 'type' => 'dept_head_approval', 'role' => 'Department Head'],
                                4 => ['label' => 'President', 'type' => 'president_approval', 'role' => 'University President'],
                            ];
                        @endphp
                        
                        @foreach($stages as $stageNum => $stageInfo)
                            @php
                                $approval = $travel->approvals->where('order', $stageNum)->first();
                                $isApproved = $approval && $approval->status === 'approved';
                                $isRejected = $approval && $approval->status === 'rejected';
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold mb-2
                                    @if($isApproved) bg-green-500 text-white
                                    @elseif($isRejected) bg-red-500 text-white
                                    @else bg-white border-2 border-gray-300 text-gray-600 @endif">
                                    @if($isApproved)
                                        <i class="fas fa-check text-sm"></i>
                                    @elseif($isRejected)
                                        <i class="fas fa-times text-sm"></i>
                                    @else
                                        {{ $stageNum }}
                                    @endif
                                </div>
                                <span class="text-xs font-medium text-gray-700 text-center">{{ $stageInfo['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Stage Details -->
            <div class="space-y-4">
                @foreach($stages as $stageNum => $stageInfo)
                    @php
                        $approval = $travel->approvals->where('order', $stageNum)->first();
                        $isApproved = $approval && $approval->status === 'approved';
                        $isRejected = $approval && $approval->status === 'rejected';
                        $isPending = !$isApproved && !$isRejected;
                    @endphp
                    
                    <div class="border rounded-lg p-4 
                        @if($isApproved) border-green-300 bg-green-50
                        @elseif($isRejected) border-red-300 bg-red-50
                        @else border-gray-300 bg-gray-50 @endif">
                        
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Stage Header -->
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                        @if($isApproved) bg-green-500 text-white
                                        @elseif($isRejected) bg-red-500 text-white
                                        @else bg-gray-400 text-white @endif">
                                        <span class="font-semibold text-sm">{{ $stageNum }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg">{{ $stageInfo['label'] }} Approval</h3>
                                        <p class="text-sm text-gray-600">{{ $stageInfo['role'] }}</p>
                                    </div>
                                </div>

                                <!-- Approval Details -->
                                @if($approval)
                                    <div class="mt-4 space-y-2 text-sm">
                                        @if($approval->approver)
                                            <div>
                                                <span class="font-medium text-gray-700">Approver:</span>
                                                <span class="text-gray-900 ml-1">
                                                    {{ $approval->approver->first_name }} {{ $approval->approver->last_name }}
                                                </span>
                                                @if($approval->approver_role)
                                                    <span class="text-gray-600 ml-1">({{ $approval->approver_role }})</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if($isApproved && $approval->approved_at)
                                            <div>
                                                <span class="font-medium text-gray-700">Approved:</span>
                                                <span class="text-gray-900 ml-1">{{ $approval->approved_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        @elseif($isRejected && $approval->rejected_at)
                                            <div>
                                                <span class="font-medium text-gray-700">Rejected:</span>
                                                <span class="text-gray-900 ml-1">{{ $approval->rejected_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        @endif

                                        @if($approval->comments)
                                            <div class="mt-3 p-3 bg-white rounded border border-gray-200">
                                                <span class="font-medium text-gray-700">Comments:</span>
                                                <p class="text-gray-900 mt-1">{{ $approval->comments }}</p>
                                            </div>
                                        @endif

                                        @if($isRejected && $approval->rejection_reason)
                                            <div class="mt-3 p-3 bg-white rounded border border-red-200">
                                                <span class="font-medium text-red-700">Rejection Reason:</span>
                                                <p class="text-red-900 mt-1">{{ $approval->rejection_reason }}</p>
                                            </div>
                                        @endif

                                        @if($approval->e_signature)
                                            <div class="mt-3 p-3 bg-gray-50 rounded border border-gray-200">
                                                <span class="font-medium text-gray-700 block mb-2">Electronic Signature:</span>
                                                <img src="{{ Storage::url($approval->signature_path) }}" alt="Signature" class="max-h-24 max-w-xs border border-gray-300 rounded p-2 bg-white">
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-600 italic text-sm">Awaiting previous approvals...</p>
                                @endif
                            </div>

                            <!-- Status Badge -->
                            <div class="ml-4">
                                @if($isApproved)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i> Approved
                                    </span>
                                @elseif($isRejected)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1 text-xs"></i> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1 text-xs"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Overall Status -->
            <div class="mt-6 p-4 border rounded-lg 
                @if($travel->status == 'approved') border-green-200 bg-green-50
                @elseif($travel->status == 'rejected') border-red-200 bg-red-50
                @else border-yellow-200 bg-yellow-50 @endif">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">Overall Status</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            @if($travel->status == 'pending')
                                Your travel authority is pending initial approval
                            @elseif($travel->status == 'recommending_approval')
                                Recommending approval completed, pending financial approvals
                            @elseif($travel->status == 'approved')
                                Your travel authority has been fully approved
                            @elseif($travel->status == 'rejected')
                                Your travel authority has been rejected
                            @endif
                        </p>
                    </div>
                    <span class="px-4 py-2 rounded-full font-semibold text-sm
                        @if($travel->status == 'approved') bg-green-100 text-green-800
                        @elseif($travel->status == 'rejected') bg-red-100 text-red-800
                        @elseif($travel->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end gap-3">
            <!-- Preview PDF Button (only if fully approved) -->
            @if($travel->isFullyApproved())
            <a href="{{ route('employees.travel.preview', $travel) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-eye mr-2"></i> Preview PDF
            </a>
            
            <!-- Download PDF Button (only if fully approved) -->
            <a href="{{ route('employees.travel.download', $travel) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-download mr-2"></i> Download PDF
            </a>
            @endif

            <!-- Cancel Button (only if not yet approved) -->
            @if($travel->status !== 'approved' && !$travel->hasRejection())
            <form action="{{ route('employees.travel.destroy', $travel) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg" 
                        onclick="return confirm('Are you sure you want to cancel this travel authority?')">
                    <i class="fas fa-trash mr-2"></i> Cancel
                </button>
            </form>
            @endif

            <!-- Back Button -->
            <a href="{{ route('employees.travel') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <!-- Rejection Alert (if rejected) -->
        @if($travel->hasRejection())
        <div class="mt-8 p-4 bg-red-50 border border-red-300 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-red-900">Request Rejected</h3>
                    <p class="text-red-800 mt-1">
                        Your travel authority was rejected. You need to submit a new request with corrections.
                    </p>
                    @if($travel->rejection_reason)
                    <p class="text-red-800 mt-2">
                        <strong>Rejection Reason:</strong> {{ $travel->rejection_reason }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
@endpush
@endsection