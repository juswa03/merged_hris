@extends('president.layouts.app')

@section('title', 'Approve/Disapprove Leave - Section 7.C & 7.D')

@section('content')
<div class="mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leave Application Review</h1>
                <p class="text-gray-600 mt-2">Section 7.C & 7.D - Presidential Approval/Disapproval (Final Stage)</p>
            </div>
            <a href="{{ route('president.leaves') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
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

            <!-- Certification Summary -->
            @if($leave->certifiedBy)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-green-900 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>Section 7.A - HR Certification ✓
                </h3>
                <div class="text-sm text-green-800 space-y-2">
                    <p><strong>Certified by:</strong> {{ $leave->certifiedBy->first_name }} {{ $leave->certifiedBy->last_name }}</p>
                    <p><strong>Certified on:</strong> {{ $leave->certified_at->format('M d, Y \a\t H:i') }}</p>
                </div>
            </div>
            @endif

            <!-- Recommendation Summary -->
            @if($leave->recommendedBy)
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-purple-900 mb-3">
                    <i class="fas fa-thumbs-up mr-2"></i>Section 7.B - Supervisor Recommendation ✓
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

            <!-- Final Approval Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Section 7.C & 7.D - Presidential Decision</h2>
                
                <form action="{{ route('president.leaves.approve', $leave) }}" method="POST" x-data="presidentialApprovalForm()">
                    @csrf

                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Based on HR certification and supervisor recommendation, please provide your final decision on this leave application.
                        </p>
                    </div>

                    <!-- Decision Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Final Decision <span class="text-red-500">*</span></label>
                        
                        <div class="space-y-3">
                            <!-- Approve -->
                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="decisionStatus === 'approved' ? 'bg-green-50 border-green-500' : ''">
                                <input type="radio" name="president_status" value="approved" required
                                       x-model="decisionStatus"
                                       @change="showDisapprovalReason = false"
                                       class="mt-1 mr-3">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        <i class="fas fa-check-circle mr-2 text-green-600"></i>Approve Leave
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        The leave application is approved. The employee is authorized to take this leave.
                                    </p>
                                </div>
                            </label>

                            <!-- Disapprove -->
                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="decisionStatus === 'disapproved' ? 'bg-red-50 border-red-500' : ''">
                                <input type="radio" name="president_status" value="disapproved" required
                                       x-model="decisionStatus"
                                       @change="showDisapprovalReason = true"
                                       class="mt-1 mr-3">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        <i class="fas fa-times-circle mr-2 text-red-600"></i>Disapprove Leave
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        The leave application is not approved. The employee is not authorized to take this leave.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Days Breakdown (if approved) -->
                    <div x-show="decisionStatus === 'approved'" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h3 class="font-semibold text-green-900 mb-4">Approved Leave Breakdown</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="with_pay_days" class="block text-sm font-medium text-gray-700 mb-2">
                                    With Pay Days <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="with_pay_days" id="with_pay_days" step="0.5" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    x-model.number="withPayDays"
                                    @change="calculateTotalDays()"
                                    value="{{ old('with_pay_days', 0) }}">
                            </div>

                            <div>
                                <label for="without_pay_days" class="block text-sm font-medium text-gray-700 mb-2">
                                    Without Pay Days <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="without_pay_days" id="without_pay_days" step="0.5" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    x-model.number="withoutPayDays"
                                    @change="calculateTotalDays()"
                                    value="{{ old('without_pay_days', 0) }}">
                            </div>

                            <div>
                                <label for="others_specify" class="block text-sm font-medium text-gray-700 mb-2">
                                    Other Category (Optional)
                                </label>
                                <input type="text" name="others_specify" id="others_specify"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    placeholder="e.g., Special arrangement"
                                    value="{{ old('others_specify', '') }}">
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-white rounded border border-green-200">
                            <p class="text-sm text-gray-700"><strong>Total Approved Days:</strong> <span x-text="totalDays || '0'"></span> days</p>
                        </div>
                    </div>

                    <!-- Disapproval Reason -->
                    <div x-show="showDisapprovalReason" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <label for="disapproved_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Disapproval <span class="text-red-500">*</span>
                        </label>
                        <textarea name="disapproved_reason" id="disapproved_reason" rows="4"
                            :required="showDisapprovalReason"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Please provide detailed reason for disapproval...">{{ old('disapproved_reason', '') }}</textarea>
                    </div>

                    <!-- E-Signature -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">Presidential Signature</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                E-Signature <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:bg-gray-100 transition"
                                 @click="$refs.signatureFileInput.click()"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="handleDrop($event)"
                                 :class="isDragging ? 'border-blue-500 bg-blue-50' : ''">
                                <i class="fas fa-signature text-4xl text-gray-400 mb-2 inline-block"></i>
                                <p class="font-medium text-gray-800">Click to upload signature</p>
                                <p class="text-sm text-gray-500 mt-1">or drag and drop</p>
                                <p class="text-xs text-gray-400 mt-2">PNG, JPG (Max 5MB - Will auto-compress for optimization)</p>
                            </div>
                            <input type="file" 
                                   x-ref="signatureFileInput" 
                                   @change="handleFileUpload($event)" 
                                   accept="image/*"
                                   class="hidden"
                                   required>
                        </div>

                        <!-- Signature Preview -->
                        <div x-show="signatureDisplayed" class="mt-4 p-4 bg-white rounded border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-gray-700">Signature Preview</label>
                                <button type="button" 
                                        @click="clearSignature()" 
                                        class="text-sm text-red-600 hover:text-red-700 font-medium">
                                    <i class="fas fa-times mr-1"></i> Change
                                </button>
                            </div>
                            <div class="flex items-center justify-center bg-gray-50 p-4 rounded border border-gray-200"
                                 style="min-height: 100px;">
                                <img id="signaturePreview" 
                                     :src="signatureData" 
                                     class="max-h-24 max-w-full object-contain"
                                     alt="Signature">
                            </div>
                        </div>

                        <!-- File info -->
                        <div x-show="fileName" class="mt-3 p-3 bg-green-50 border border-green-200 rounded">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>File:</strong> <span x-text="fileName"></span>
                            </p>
                        </div>

                        <input type="hidden" name="president_signature" id="president_signature" :value="signatureData">
                    </div>

                    <!-- Certification Checkbox -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="certify_approval" required
                                   class="mt-1 mr-3 rounded border-gray-300 focus:ring-blue-500">
                            <div>
                                <p class="font-medium text-blue-900">I approve this decision</p>
                                <p class="text-sm text-blue-800 mt-1">
                                    I have reviewed this leave application along with HR certification and supervisor recommendation. 
                                    I hereby provide my final decision in my capacity as University President.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('president.leaves') }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2"></i>Back without saving
                        </a>
                        <div class="space-x-3">
                            <button type="reset" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                                Reset
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg" 
                                    :disabled="isSubmitting || !isFormValid()"
                                    @click="validateBeforeSubmit()">
                                <span x-show="!isSubmitting">
                                    <i class="fas fa-crown mr-2"></i>Submit Final Decision
                                </span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Application Status</h3>
                
                <div class="mb-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-hourglass-half mr-2"></i> Awaiting Your Decision
                    </span>
                </div>

                <hr class="my-4">

                <!-- Workflow Timeline -->
                <div class="space-y-4 text-sm">
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-500 text-white text-xs rounded-full mr-3 flex-shrink-0 mt-1">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Section 7.A - HR Certification</p>
                            <p class="text-xs text-gray-600">Completed</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-500 text-white text-xs rounded-full mr-3 flex-shrink-0 mt-1">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Section 7.B - Supervisor Recommendation</p>
                            <p class="text-xs text-gray-600">Completed</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white text-xs rounded-full mr-3 flex-shrink-0 mt-1">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Section 7.C & 7.D - Presidential Approval</p>
                            <p class="text-xs text-gray-600">In Progress (Your Action)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements Checklist -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Review Checklist</h3>
                
                <div class="space-y-3 text-sm">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">HR certified the leave credits</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Department Head reviewed the application</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Organizational impact assessed</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Final decision is appropriate</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function presidentialApprovalForm() {
    return {
        decisionStatus: 'approved',
        withPayDays: 0,
        withoutPayDays: 0,
        totalDays: 0,
        showDisapprovalReason: false,
        
        signatureData: '',
        fileName: '',
        signatureDisplayed: false,
        isDragging: false,
        isSubmitting: false,

        calculateTotalDays() {
            this.totalDays = (parseFloat(this.withPayDays) || 0) + (parseFloat(this.withoutPayDays) || 0);
        },

        isFormValid() {
            // If disapproved, form is valid (no days required)
            if (this.decisionStatus === 'disapproved') {
                return this.signatureDisplayed;
            }
            
            // If approved, need at least one type of days OR others_specify
            const othersValue = document.getElementById('others_specify')?.value || '';
            const totalDays = (parseFloat(this.withPayDays) || 0) + (parseFloat(this.withoutPayDays) || 0);
            
            return this.signatureDisplayed && (totalDays > 0 || othersValue.trim().length > 0);
        },

        validateBeforeSubmit() {
            if (this.decisionStatus === 'approved') {
                const othersValue = document.getElementById('others_specify')?.value || '';
                const totalDays = (parseFloat(this.withPayDays) || 0) + (parseFloat(this.withoutPayDays) || 0);
                
                if (totalDays === 0 && !othersValue.trim()) {
                    alert('Please specify at least one type of days (with pay, without pay, or other category) when approving leave.');
                    return false;
                }
            }
            return true;
        },

        handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.processFile(file);
            }
        },

        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.processFile(files[0]);
            }
        },

        processFile(file) {
            const maxSize = 5 * 1024 * 1024; // 5MB limit
            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit. Please use a smaller image.');
                return;
            }

            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please upload PNG or JPG only.');
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                this.compressImage(event.target.result, file.name);
            };
            reader.readAsDataURL(file);
        },

        compressImage(dataUrl, originalFileName) {
            const img = new Image();
            img.onload = () => {
                try {
                    const canvas = document.createElement('canvas');
                    // Limit canvas size to 600x400 max
                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 600;
                    const maxHeight = 400;

                    if (width > height) {
                        if (width > maxWidth) {
                            height = (height * maxWidth) / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width = (width * maxHeight) / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    
                    // Get 2D context and fill background with white
                    const ctx = canvas.getContext('2d', { willReadFrequently: true });
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, width, height);
                    
                    // Draw the image
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convert to PNG and store base64 in hidden input (matching travel module)
                    const base64Data = canvas.toDataURL('image/png');
                    
                    this.signatureData = base64Data;
                    this.fileName = originalFileName;
                    this.signatureDisplayed = true;
                    
                    // Store base64 in hidden input field
                    document.getElementById('president_signature').value = base64Data;
                    
                    // Update preview image
                    const previewImg = document.getElementById('signaturePreview');
                    if (previewImg) {
                        previewImg.src = base64Data;
                    }
                    
                } catch (error) {
                    console.error('Error compressing image:', error);
                    alert('Error processing image. Please try a different image.');
                }
            };
            img.onerror = () => {
                alert('Error loading image. Please try a different image.');
            };
            img.src = dataUrl;
        },

        clearSignature() {
            this.signatureData = '';
            this.fileName = '';
            this.signatureDisplayed = false;
            document.getElementById('president_signature').value = '';
            this.$refs.signatureFileInput.value = '';
        }
    };
}
</script>
@endpush
@endsection
