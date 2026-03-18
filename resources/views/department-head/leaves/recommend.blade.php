@extends('department-head.layouts.app')
@section('title', 'Leave Recommendation - Section 7.B')

@section('content')
<div class="mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leave Application Recommendation</h1>
                <p class="text-gray-600 mt-2">Section 7.B - Immediate Supervisor / Department Head Recommendation</p>
            </div>
            <a href="{{ route('dept.leaves') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Leave Information -->
        <div class="lg:col-span-2">
            <!-- Employee & Leave Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Employee & Leave Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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

                <hr class="my-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Leave Type</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $leave->getLeaveTypeDisplay() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Duration</label>
                        <p class="mt-1 text-gray-900">
                            {{ $leave->start_date->format('M d, Y') }}
                            @if($leave->end_date && $leave->end_date !== $leave->start_date)
                                — {{ $leave->end_date->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Number of Days</label>
                        <p class="mt-1 text-gray-900 font-medium text-lg">{{ $leave->days ?? 1 }} days</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Reason</label>
                        <p class="mt-1 text-gray-900">{{ Str::limit($leave->reason, 100) }}</p>
                    </div>
                </div>
            </div>

            <!-- HR Certification Status -->
            @if($leave->certifiedBy)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-green-900 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>HR Certification Complete
                </h3>
                <div class="text-sm text-green-800 space-y-1">
                    <p><strong>Certified by:</strong> {{ $leave->certifiedBy->first_name }} {{ $leave->certifiedBy->last_name }}</p>
                    <p><strong>Certified on:</strong> {{ $leave->certified_at->format('M d, Y \a\t H:i') }}</p>
                    @if($leave->vacation_balance !== null || $leave->sick_balance !== null)
                    <div class="mt-3 pt-3 border-t border-green-200">
                        <p><strong>Leave Credits Verified:</strong></p>
                        @if($leave->vacation_balance !== null)
                        <p class="ml-4">VL Balance: {{ $leave->vacation_balance }} days</p>
                        @endif
                        @if($leave->sick_balance !== null)
                        <p class="ml-4">SL Balance: {{ $leave->sick_balance }} days</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recommendation Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Section 7.B - Recommendation</h2>
                
                <form action="{{ route('dept.leaves.recommend', $leave) }}" method="POST" x-data="recommendationForm()">
                    @csrf

                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            As the Immediate Supervisor or Department Head, please review the leave application and provide your recommendation.
                        </p>
                    </div>

                    <!-- Recommendation Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Recommendation <span class="text-red-500">*</span></label>
                        
                        <div class="space-y-3">
                            <!-- For Approval -->
                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="recommendationStatus === 'for_approval' ? 'bg-green-50 border-green-500' : ''">
                                <input type="radio" name="recommendation_status" value="for_approval" required
                                       x-model="recommendationStatus"
                                       @change="showReasonField = false"
                                       class="mt-1 mr-3">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        <i class="fas fa-thumbs-up mr-2 text-green-600"></i>For Approval
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        The employee is approved to take this leave. Work schedule impact has been reviewed and mitigated.
                                    </p>
                                </div>
                            </label>

                            <!-- For Disapproval -->
                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="recommendationStatus === 'for_disapproval' ? 'bg-red-50 border-red-500' : ''">
                                <input type="radio" name="recommendation_status" value="for_disapproval" required
                                       x-model="recommendationStatus"
                                       @change="showReasonField = true"
                                       class="mt-1 mr-3">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        <i class="fas fa-thumbs-down mr-2 text-red-600"></i>For Disapproval
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        The employee cannot take this leave due to operational reasons. Please provide detailed reasons.
                                    </p>
                                </div>
                            </label>
                        </div>
                        
                        @error('recommendation_status')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reason for Disapproval (Conditional) -->
                    <div x-show="showReasonField" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <label for="recommendation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Disapproval <span class="text-red-500" x-show="showReasonField">*</span>
                        </label>
                        <textarea name="recommendation_reason" id="recommendation_reason" rows="4"
                            :required="showReasonField"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Please provide detailed reasons for disapproval..."
                            x-model="recommendationReason">{{ old('recommendation_reason', '') }}</textarea>
                        <p class="text-xs text-gray-600 mt-1">Minimum 20 characters required when disapproving.</p>
                        @error('recommendation_reason')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- General Recommendation Notes (Optional) -->
                    <div class="mb-6">
                        <label for="recommendation_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Comments (Optional)
                        </label>
                        <textarea name="recommendation_notes" id="recommendation_notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Any additional comments about this leave application...">{{ old('recommendation_notes', '') }}</textarea>
                    </div>

                    <!-- Signature Section -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">Immediate Supervisor / Department Head Signature</h3>
                        
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
                            @error('recommendation_signature')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
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

                        <input type="hidden" name="recommendation_signature" id="recommendation_signature" :value="signatureData">
                    </div>

                    <!-- Certification Checkbox -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="certify_recommendation" required
                                   class="mt-1 mr-3 rounded border-gray-300 focus:ring-blue-500">
                            <div>
                                <p class="font-medium text-blue-900">I certify that I have reviewed this leave application</p>
                                <p class="text-sm text-blue-800 mt-1">
                                    I have considered the work schedule impact and operational requirements. I provide this recommendation in accordance with company policies and employment regulations.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('dept.leaves') }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2"></i>Back without saving
                        </a>
                        <div class="space-x-3">
                            <button type="reset" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                                Reset
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg" :disabled="isSubmitting">
                                <span x-show="!isSubmitting">
                                    <i class="fas fa-check mr-2"></i>Submit Recommendation
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

        <!-- Right Column: Status & Info -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Application Status</h3>
                
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($leave->status === 'pending_certification')
                            bg-blue-100 text-blue-800
                        @elseif($leave->status === 'pending_recommendation')
                            bg-purple-100 text-purple-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                    </span>
                </div>

                <div class="space-y-4 text-sm">
                    <!-- Workflow Timeline -->
                    <div class="border-l-4 border-gray-300 pl-4 py-2">
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                @if($leave->certifiedBy)
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white text-xs rounded-full mr-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                @else
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-white text-xs rounded-full mr-2">
                                    <i class="fas fa-hourglass-half"></i>
                                </span>
                                @endif
                                <span class="font-medium">HR Certification</span>
                            </div>
                            @if($leave->certifiedBy)
                            <p class="text-xs text-gray-600 ml-8">Completed {{ $leave->certified_at->diffForHumans() }}</p>
                            @else
                            <p class="text-xs text-gray-600 ml-8">Pending</p>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs rounded-full mr-2">
                                    <i class="fas fa-hourglass-half"></i>
                                </span>
                                <span class="font-medium">Supervisor Recommendation</span>
                            </div>
                            <p class="text-xs text-gray-600 ml-8">In Progress (Your Action)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Review Checklist</h3>
                
                <div class="space-y-3 text-sm">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Employee leave credits verified by HR</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Leave application is complete</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Travel authority checked (if applicable)</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Work schedule impact assessed</span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mt-1 mr-3 rounded" disabled>
                        <span class="text-gray-700">Staff availability confirmed</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function recommendationForm() {
    return {
        recommendationStatus: '{{ old("recommendation_status", "for_approval") }}',
        recommendationReason: '{{ old("recommendation_reason", "") }}',
        showReasonField: '{{ old("recommendation_status", "") }}' === 'for_disapproval',
        
        signatureData: '',
        fileName: '',
        signatureDisplayed: false,
        isDragging: false,
        isSubmitting: false,

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
                    document.getElementById('recommendation_signature').value = base64Data;
                    
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
            document.getElementById('recommendation_signature').value = '';
            this.$refs.signatureFileInput.value = '';
        }
    };
}
</script>
@endpush
@endsection
