@extends('admin.layouts.app')

@section('title', 'Travel Authority Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Travel Authority Details</h1>
            <a href="{{ route('admin.travel.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
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
                        <label class="block text-sm font-medium text-gray-600">Employee</label>
                        <p class="mt-1 text-gray-900">
                            {{ $travel->user->first_name }} {{ $travel->user->last_name }}
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
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Inclusive Date of Travel</label>
                        <p class="mt-1 text-gray-900">
                            {{ $travel->inclusive_date_of_travel->format('F d, Y') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Transportation</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            {{ $travel->transportation ? str_replace('_', ' ', $travel->transportation) : 'Not specified' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Estimated Expenses</label>
                        <p class="mt-1 text-gray-900 capitalize">
                            {{ str_replace('_', ' ', $travel->estimated_expenses) }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Source of Funds</label>
                        <p class="mt-1 text-gray-900">{{ $travel->source_of_funds ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Purpose</label>
                <p class="mt-1 text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $travel->purpose }}</p>
            </div>
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
                        $nextApproval = $travel->getNextApprovalStep();
                        $currentStageNum = $nextApproval ? $nextApproval['order'] : null;
                        $canApprove = $nextApproval && $travel->canBeApprovedBy(auth()->user());
                    @endphp
                    <div class="absolute top-5 left-0 w-full h-1 bg-gray-200">
                        <div class="h-full bg-blue-500 transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    
                    <!-- Stage Circles -->
                    <div class="flex justify-between relative z-10">
                        @php
                            $stages = [
                                1 => ['label' => 'Recommending', 'type' => 'recommending_approval', 'role' => 'Department Head'],
                                2 => ['label' => 'Allotment', 'type' => 'allotment_available', 'role' => 'Finance Officer'],
                                3 => ['label' => 'Funds', 'type' => 'funds_available', 'role' => 'Accountant'],
                                4 => ['label' => 'President', 'type' => 'final_approval', 'role' => 'University President'],
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
                                    @elseif($stageNum === $currentStageNum) bg-blue-500 text-white
                                    @else bg-white border-2 border-gray-300 text-gray-600 @endif">
                                    @if($isApproved)
                                        <i class="fas fa-check"></i>
                                    @elseif($isRejected)
                                        <i class="fas fa-times"></i>
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
                        $canCurrentUserApprove = $stageNum === $currentStageNum && $canApprove;
                    @endphp
                    
                    <div class="border rounded-lg p-4 
                        @if($isApproved) border-green-300 bg-green-50
                        @elseif($isRejected) border-red-300 bg-red-50
                        @elseif($canCurrentUserApprove) border-blue-300 bg-blue-50
                        @else border-gray-300 bg-gray-50 @endif">
                        
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Stage Header -->
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                        @if($isApproved) bg-green-500 text-white
                                        @elseif($isRejected) bg-red-500 text-white
                                        @elseif($canCurrentUserApprove) bg-blue-500 text-white
                                        @else bg-gray-400 text-white @endif">
                                        <span class="font-semibold">{{ $stageNum }}</span>
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
                                                <span class="text-gray-900">
                                                    {{ $approval->approver->first_name }} {{ $approval->approver->last_name }}
                                                </span>
                                                @if($approval->approver_role)
                                                    <span class="text-gray-600">({{ $approval->approver_role }})</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if($isApproved && $approval->approved_at)
                                            <div>
                                                <span class="font-medium text-gray-700">Approved:</span>
                                                <span class="text-gray-900">{{ $approval->approved_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        @elseif($isRejected && $approval->rejected_at)
                                            <div>
                                                <span class="font-medium text-gray-700">Rejected:</span>
                                                <span class="text-gray-900">{{ $approval->rejected_at->format('M d, Y g:i A') }}</span>
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
                                            <div>
                                                <span class="font-medium text-gray-700">Signature:</span>
                                                <span class="text-gray-900">{{ $approval->e_signature }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-600 italic">Awaiting previous approvals...</p>
                                @endif
                            </div>

                            <!-- Status Badge & Action Buttons -->
                            <div class="ml-4 flex flex-col items-end gap-2">
                                @if($isApproved)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </span>
                                @elseif($isRejected)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @elseif($canCurrentUserApprove)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap mb-2">
                                        <i class="fas fa-star mr-1"></i> Your Turn
                                    </span>
                                    <div class="flex gap-2">
                                        <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm whitespace-nowrap"
                                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $stageNum }}">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm whitespace-nowrap"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $stageNum }}">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Approve Modal -->
                    @if($canCurrentUserApprove)
                    <div class="modal fade" id="approveModal{{ $stageNum }}" tabindex="-1" aria-labelledby="approveLabel{{ $stageNum }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.travel.approve', $travel) }}" method="POST">
                                    @csrf
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="approveLabel{{ $stageNum }}">✓ Approve - {{ $stageInfo['label'] }} Stage</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info" role="alert">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Approval Notice:</strong> By approving, you confirm that all requirements for this stage have been met.
                                        </div>
                                        <div class="mb-3">
                                            <label for="comments{{ $stageNum }}" class="form-label">Comments (Optional)</label>
                                            <textarea name="comments" id="comments{{ $stageNum }}" class="form-control" rows="3"
                                                placeholder="Add any comments about this approval..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="esignature{{ $stageNum }}" class="form-label">Your E-Signature <span class="text-muted">(Your name or initials)</span></label>
                                            <input type="text" name="e_signature" id="esignature{{ $stageNum }}" class="form-control" required
                                                placeholder="Type your name or initials as your signature"
                                                value="{{ auth()->user()->name }}">
                                            <small class="text-muted">This signature will be recorded in the travel authority PDF</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check mr-1"></i> Approve Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Reject Modal -->
                    @if($canCurrentUserApprove)
                    <div class="modal fade" id="rejectModal{{ $stageNum }}" tabindex="-1" aria-labelledby="rejectLabel{{ $stageNum }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.travel.approve', $travel) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-danger">
                                        <h5 class="modal-title text-danger" id="rejectLabel{{ $stageNum }}">Reject - {{ $stageInfo['label'] }} Stage</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <strong>Warning:</strong> Rejecting this request will stop the approval workflow. The employee will need to resubmit.
                                        </div>
                                        <div class="mb-3">
                                            <label for="rejection_reason{{ $stageNum }}" class="form-label">
                                                <strong>Reason for Rejection</strong> <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="rejection_reason" id="rejection_reason{{ $stageNum }}" class="form-control" rows="4"
                                                placeholder="Please provide a detailed reason for rejection..." required minlength="10"></textarea>
                                            <small class="form-text text-muted">Minimum 10 characters required</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="reject_comments{{ $stageNum }}" class="form-label">Additional Comments (Optional)</label>
                                            <textarea name="comments" id="reject_comments{{ $stageNum }}" class="form-control" rows="2"
                                                placeholder="Any additional information..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-ban mr-1"></i> Reject Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Current Status Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Status Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-600 font-medium">Current Status</p>
                    <p class="text-lg font-semibold text-blue-900 mt-1">
                        @if($travel->status === 'approved')
                            ✓ Fully Approved
                        @elseif($travel->status === 'rejected')
                            ✗ Rejected
                        @elseif($travel->status === 'submitted')
                            ⏳ In Progress
                        @else
                            {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                        @endif
                    </p>
                </div>
                
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-600 font-medium">Approvals Completed</p>
                    <p class="text-lg font-semibold text-green-900 mt-1">{{ $completedStages }}/4</p>
                </div>

                <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <p class="text-sm text-purple-600 font-medium">Submitted By</p>
                    <p class="text-lg font-semibold text-purple-900 mt-1">
                        {{ $travel->creator?->first_name ?? 'System' }}
                    </p>
                </div>
            </div>

            @if($travel->submitted_at)
            <div class="mt-4 p-3 bg-gray-100 rounded text-sm">
                <strong>Submission Time:</strong> {{ $travel->submitted_at->format('M d, Y g:i A') }}
            </div>
            @endif

            @if($travel->completed_at)
            <div class="mt-2 p-3 bg-green-100 rounded text-sm">
                <strong>Completion Time:</strong> {{ $travel->completed_at->format('M d, Y g:i A') }}
            </div>
            @endif
        </div>

        <!-- Rejection Alert (if applicable) -->
        @if($travel->hasRejection())
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-red-900">Request Rejected</h3>
                    @if($travel->rejection_reason)
                    <p class="text-red-800 mt-1">
                        <strong>Reason:</strong> {{ $travel->rejection_reason }}
                    </p>
                    @endif
                    <p class="text-red-800 text-sm mt-2">
                        The employee must submit a new travel authority request to proceed.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection