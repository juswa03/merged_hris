@extends('employee.layouts.app')

@section('title', 'Leave Application Details')

@section('content')
<div class="mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section with Status -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leave Application Details</h1>
                <p class="text-gray-600 mt-1">CS Form No. 6, Revised 2020</p>
            </div>
            @if($leave->status === 'pending')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-hourglass-half mr-2"></i>
                        <strong>Status:</strong> Pending Review
                    </p>
                </div>
            @elseif($leave->status === 'approved')
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Status:</strong> Approved
                    </p>
                </div>
            @elseif($leave->status === 'rejected')
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Status:</strong> Rejected
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- PART 1: OFFICE/DEPARTMENT -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b-2 border-blue-200 flex items-center">
            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">1</span>
            Office / Department
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-medium">Department / Office / Division</p>
                <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->department }}</p>
            </div>
        </div>
    </div>

    <!-- PART 2: EMPLOYEE NAME -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b-2 border-blue-200 flex items-center">
            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">2</span>
            Name
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-medium">Last Name</p>
                <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->user->last_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">First Name</p>
                <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->user->first_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Middle Name</p>
                <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->user->personalInformation?->middle_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- PART 3: DATE OF FILING -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b-2 border-blue-200 flex items-center">
            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">3</span>
            Date of Filing
        </h2>
        <div>
            <p class="text-sm text-gray-600 font-medium">Filing Date</p>
            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->filing_date->format('F d, Y') }}</p>
        </div>
    </div>

    <!-- PART 4: POSITION -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b-2 border-blue-200 flex items-center">
            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">4</span>
            Position
        </h2>
        <div>
            <p class="text-sm text-gray-600 font-medium">Official Position Title</p>
            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->position }}</p>
        </div>
    </div>

    <!-- PART 5: SALARY -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b-2 border-blue-200 flex items-center">
            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">5</span>
            Salary
        </h2>
        <div>
            <p class="text-sm text-gray-600 font-medium">Monthly / Daily Salary Rate</p>
            <p class="text-lg text-gray-900 font-semibold mt-1">₱ {{ number_format($leave->salary, 2) }}</p>
        </div>
    </div>

    <!-- PART 6: DETAILS OF APPLICATION -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 pb-4 border-b-2 border-blue-200 flex items-center">
            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">6</span>
            Details of Application
        </h2>

        <!-- 6.A - Type of Leave -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">A</span>
                Type of Leave to Be Availed Of
            </h3>
            <div>
                <p class="text-sm text-gray-600 font-medium">Leave Type</p>
                <p class="text-lg text-gray-900 font-semibold mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $leave->getLeaveTypeDisplay() }}
                    </span>
                </p>
            </div>
        </div>

        <!-- 6.B - Details of Leave -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">B</span>
                Details of Leave
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($leave->type === 'vacation')
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Destination</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1">
                            @if($leave->leave_location)
                                {{ $leave->leave_location }}
                            @elseif($leave->abroad_specify)
                                {{ $leave->abroad_specify }} (Abroad)
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>
                @endif

                @if($leave->type === 'special_privilege')
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Type of Special Leave Privilege</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1">
                            {{ $leave->getSlpTypeDisplay() ?? 'Not specified' }}
                        </p>
                    </div>
                @endif

                @if($leave->type === 'sick')
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Type of Sickness</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1 capitalize">
                            {{ str_replace('_', ' ', $leave->sick_type ?? 'Not specified') }}
                        </p>
                    </div>
                    @if($leave->hospital_illness)
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Hospital Illness</p>
                            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->hospital_illness }}</p>
                        </div>
                    @endif
                    @if($leave->outpatient_illness)
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Outpatient Illness</p>
                            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->outpatient_illness }}</p>
                        </div>
                    @endif
                @endif

                @if($leave->type === 'maternity')
                    @if($leave->maternity_delivery_date)
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Expected/Actual Delivery Date</p>
                            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->maternity_delivery_date->format('F d, Y') }}</p>
                        </div>
                    @endif
                    @if($leave->is_miscarriage)
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Miscarriage</p>
                            <p class="text-lg text-gray-900 font-semibold mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Yes
                                </span>
                            </p>
                        </div>
                    @endif
                @endif

                @if($leave->type === 'paternity')
                    @if($leave->paternity_delivery_count)
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Delivery Count</p>
                            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->paternity_delivery_count }}</p>
                        </div>
                    @endif
                @endif

                @if($leave->type === 'study')
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Study Purpose</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1 capitalize">
                            {{ str_replace('_', ' ', $leave->study_purpose ?? 'Not specified') }}
                        </p>
                    </div>
                    @if($leave->other_purpose_specify)
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Other Purpose Details</p>
                            <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->other_purpose_specify }}</p>
                        </div>
                    @endif
                @endif

                @if($leave->type === 'emergency')
                    @if($leave->emergency_details)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 font-medium">Emergency Details</p>
                            <p class="text-gray-900 mt-1">{{ $leave->emergency_details }}</p>
                        </div>
                    @endif
                @endif

                @if($leave->other_leave_details)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 font-medium">Additional Details</p>
                        <p class="text-gray-900 mt-1">{{ $leave->other_leave_details }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- 6.C - Number of Working Days Applied For -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">C</span>
                Number of Working Days Applied For
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Duration Type</p>
                    <p class="text-lg text-gray-900 font-semibold mt-1 capitalize">
                        {{ str_replace('_', ' ', $leave->duration_type ?? 'Full Day') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-medium">Start Date</p>
                    <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->start_date->format('F d, Y') }}</p>
                </div>

                @if($leave->end_date)
                    <div>
                        <p class="text-sm text-gray-600 font-medium">End Date</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1">{{ $leave->end_date->format('F d, Y') }}</p>
                    </div>
                @endif

                <div class="md:col-span-3 bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-900">
                        <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                        <strong>Total Days Applied:</strong> {{ $leave->days ?? 1 }} days
                    </p>
                </div>
            </div>
        </div>

        <!-- 6.D - Commutation -->
        <div>
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">D</span>
                Commutation
            </h3>

            <div>
                <p class="text-sm text-gray-600 font-medium">Commutation Status</p>
                <p class="text-lg text-gray-900 font-semibold mt-1">
                    @if($leave->commutation === 'requested')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            Requested
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Not Requested
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Signature Section -->
    @if($leave->electronic_signature_path)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-pen-fancy text-blue-600 mr-3"></i>
                Electronic Signature
            </h2>
            <div class="border border-gray-300 rounded-lg p-6 bg-gray-50 flex items-center justify-center" style="min-height: 150px;">
                <img src="{{ Storage::url($leave->electronic_signature_path) }}" 
                     alt="Signature" 
                     class="max-h-32 max-w-full object-contain">
            </div>
            <p class="text-sm text-gray-600 mt-4">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                Signature uploaded on {{ $leave->created_at->format('F d, Y \a\t g:i A') }}
            </p>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mb-8 space-x-4">
        <a href="{{ route('employees.leaves') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-6 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>

        @if($leave->status === 'pending')
            <div class="space-x-4">
                <a href="{{ route('employees.leaves.edit', $leave->id) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-medium px-6 py-2 rounded-lg transition inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form action="{{ route('employees.leaves.destroy', $leave->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-2 rounded-lg transition inline-flex items-center">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        @elseif($leave->president_status === 'approved')
            <div class="space-x-4">
                <a href="{{ route('employees.leaves.preview-pdf', $leave) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition inline-flex items-center">
                    <i class="fas fa-eye mr-2"></i> Preview PDF
                </a>
                <a href="{{ route('employees.leaves.download-pdf', $leave) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2 rounded-lg transition inline-flex items-center">
                    <i class="fas fa-download mr-2"></i> Download PDF
                </a>
            </div>
        @endif
    </div>

    <!-- 3-Stage Approval Workflow -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">3-Stage Approval Workflow</h2>
        
        <!-- Workflow Progress Bar -->
        <div class="mb-8">
            <div class="relative">
                <!-- Progress Line -->
                @php
                    $completedStages = 0;
                    if ($leave->certified_at) $completedStages++;
                    if ($leave->recommended_at) $completedStages++;
                    if ($leave->approved_by_president_at) $completedStages++;
                    $progressPercent = ($completedStages / 3) * 100;
                @endphp
                <div class="absolute top-5 left-0 w-full h-1 bg-gray-200">
                    <div class="h-full bg-blue-500 transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                </div>
                
                <!-- Stage Circles -->
                <div class="flex justify-between relative z-10">
                    @php
                        $stages = [
                            1 => ['label' => 'HR Certification', 'section' => '7.A'],
                            2 => ['label' => 'Dept Head Recommendation', 'section' => '7.B'],
                            3 => ['label' => 'President Approval', 'section' => '7.C & 7.D'],
                        ];
                    @endphp
                    
                    @foreach($stages as $stageNum => $stageInfo)
                        @php
                            $isCompleted = false;
                            if ($stageNum === 1 && $leave->certified_at) $isCompleted = true;
                            elseif ($stageNum === 2 && $leave->recommended_at) $isCompleted = true;
                            elseif ($stageNum === 3 && $leave->approved_by_president_at) $isCompleted = true;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold mb-2
                                @if($isCompleted) bg-green-500 text-white
                                @else bg-white border-2 border-gray-300 text-gray-600 @endif">
                                @if($isCompleted)
                                    <i class="fas fa-check text-sm"></i>
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
            <!-- Stage 1: HR Certification (Section 7.A) -->
            @php
                $isCertified = $leave->certified_at !== null;
            @endphp
            <div class="border rounded-lg p-4 @if($isCertified) border-green-300 bg-green-50 @else border-gray-300 bg-gray-50 @endif">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                @if($isCertified) bg-green-500 text-white @else bg-gray-400 text-white @endif">
                                <span class="font-semibold text-sm">1</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg">HR Certification <span class="text-xs text-gray-600 font-normal">(Section 7.A)</span></h3>
                                <p class="text-sm text-gray-600">Certification by HR Manager</p>
                            </div>
                        </div>

                        @if($isCertified)
                            <div class="mt-4 space-y-2 text-sm pl-13">
                                @if($leave->certifiedBy)
                                    <div>
                                        <span class="font-medium text-gray-700">Certified by:</span>
                                        <span class="text-gray-900 ml-1">{{ $leave->certifiedBy->first_name }} {{ $leave->certifiedBy->last_name }}</span>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-medium text-gray-700">Certified on:</span>
                                    <span class="text-gray-900 ml-1">{{ $leave->certified_at->format('M d, Y g:i A') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 text-sm text-gray-600 pl-13">
                                <i class="fas fa-hourglass-half mr-2"></i> Awaiting HR certification...
                            </div>
                        @endif
                    </div>
                    <span class="px-3 py-1 rounded text-xs font-semibold
                        @if($isCertified) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                        @if($isCertified) ✓ Certified @else Pending @endif
                    </span>
                </div>
            </div>

            <!-- Stage 2: Department Head Recommendation (Section 7.B) -->
            @php
                $isRecommended = $leave->recommended_at !== null;
            @endphp
            <div class="border rounded-lg p-4 @if($isRecommended) border-green-300 bg-green-50 @else border-gray-300 bg-gray-50 @endif">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                @if($isRecommended) bg-green-500 text-white @else bg-gray-400 text-white @endif">
                                <span class="font-semibold text-sm">2</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg">Department Head Recommendation <span class="text-xs text-gray-600 font-normal">(Section 7.B)</span></h3>
                                <p class="text-sm text-gray-600">Recommendation by Department Head/Supervisor</p>
                            </div>
                        </div>

                        @if($isRecommended)
                            <div class="mt-4 space-y-2 text-sm pl-13">
                                @if($leave->recommendedBy)
                                    <div>
                                        <span class="font-medium text-gray-700">Recommended by:</span>
                                        <span class="text-gray-900 ml-1">{{ $leave->recommendedBy->first_name }} {{ $leave->recommendedBy->last_name }}</span>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-medium text-gray-700">Recommended on:</span>
                                    <span class="text-gray-900 ml-1">{{ $leave->recommended_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Recommendation:</span>
                                    <span class="text-gray-900 ml-1 capitalize">
                                        @if($leave->recommendation_status === 'for_approval')
                                            <span class="text-green-700">For Approval</span>
                                        @else
                                            <span class="text-red-700">For Disapproval</span>
                                        @endif
                                    </span>
                                </div>
                                @if($leave->recommendation_reason)
                                    <div class="mt-3 p-2 bg-white rounded border border-gray-200">
                                        <span class="font-medium text-gray-700">Reason:</span>
                                        <p class="text-gray-900 mt-1 text-xs">{{ $leave->recommendation_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-3 text-sm text-gray-600 pl-13">
                                <i class="fas fa-hourglass-half mr-2"></i> Awaiting department head recommendation...
                            </div>
                        @endif
                    </div>
                    <span class="px-3 py-1 rounded text-xs font-semibold
                        @if($isRecommended) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                        @if($isRecommended) ✓ Recommended @else Pending @endif
                    </span>
                </div>
            </div>

            <!-- Stage 3: President Approval (Section 7.C & 7.D) -->
            @php
                $isApproved = $leave->approved_by_president_at !== null;
            @endphp
            <div class="border rounded-lg p-4 
                @if($leave->president_status === 'approved') border-green-300 bg-green-50
                @elseif($leave->president_status === 'disapproved') border-red-300 bg-red-50
                @else border-gray-300 bg-gray-50 @endif">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                @if($leave->president_status === 'approved') bg-green-500 text-white
                                @elseif($leave->president_status === 'disapproved') bg-red-500 text-white
                                @else bg-gray-400 text-white @endif">
                                <span class="font-semibold text-sm">3</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg">President Final Approval <span class="text-xs text-gray-600 font-normal">(Section 7.C & 7.D)</span></h3>
                                <p class="text-sm text-gray-600">Final decision by University President</p>
                            </div>
                        </div>

                        @if($isApproved)
                            <div class="mt-4 space-y-2 text-sm pl-13">
                                <div>
                                    <span class="font-medium text-gray-700">Approved by:</span>
                                    <span class="text-gray-900 ml-1">
                                        @if($leave->approvedByPresident)
                                            {{ $leave->approvedByPresident->first_name }} {{ $leave->approvedByPresident->last_name }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Decision Date:</span>
                                    <span class="text-gray-900 ml-1">{{ $leave->approved_by_president_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Decision:</span>
                                    <span class="ml-1 capitalize
                                        @if($leave->president_status === 'approved') text-green-700 font-semibold
                                        @else text-red-700 font-semibold @endif">
                                        @if($leave->president_status === 'approved')
                                            ✓ APPROVED
                                        @else
                                            ✗ DISAPPROVED
                                        @endif
                                    </span>
                                </div>
                                @if($leave->with_pay_days > 0 || $leave->without_pay_days > 0)
                                    <div class="mt-3 p-2 bg-white rounded border border-gray-200">
                                        <span class="font-medium text-gray-700">Approved Days:</span>
                                        <div class="text-gray-900 mt-1 text-xs">
                                            @if($leave->with_pay_days > 0)
                                                <div>• With Pay: <strong>{{ $leave->with_pay_days }}</strong> days</div>
                                            @endif
                                            @if($leave->without_pay_days > 0)
                                                <div>• Without Pay: <strong>{{ $leave->without_pay_days }}</strong> days</div>
                                            @endif
                                            @if($leave->others_specify)
                                                <div>• Other: <strong>{{ $leave->others_specify }}</strong></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($leave->disapproved_reason)
                                    <div class="mt-3 p-2 bg-white rounded border border-red-200">
                                        <span class="font-medium text-red-700">Reason for Disapproval:</span>
                                        <p class="text-gray-900 mt-1 text-xs">{{ $leave->disapproved_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-3 text-sm text-gray-600 pl-13">
                                <i class="fas fa-hourglass-half mr-2"></i> Awaiting president's final decision...
                            </div>
                        @endif
                    </div>
                    <span class="px-3 py-1 rounded text-xs font-semibold
                        @if($leave->president_status === 'approved') bg-green-100 text-green-800
                        @elseif($leave->president_status === 'disapproved') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        @if($leave->president_status === 'approved') ✓ Approved
                        @elseif($leave->president_status === 'disapproved') ✗ Disapproved
                        @else Pending @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
