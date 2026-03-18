@extends('president.layouts.app')

@section('title', 'Final Travel Request Approval')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Travel Request - Final Approval</h1>
                <p class="text-gray-600 mt-1">Complete the approval chain with your signature</p>
            </div>
            <a href="{{ route('president.travel') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <!-- Travel Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-4 border-b">Employee Travel Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Employee Name</label>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $travel->user->first_name }} {{ $travel->user->last_name }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Designation</label>
                    <p class="text-gray-900">{{ $travel->designation }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Destination</label>
                    <p class="text-gray-900">{{ $travel->destination }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Travel Date</label>
                    <p class="text-gray-900">
                        @if($travel->duration_type === 'single_day')
                            {{ $travel->inclusive_date_of_travel->format('F d, Y') }}
                        @else
                            {{ $travel->start_date->format('F d, Y') }} to {{ $travel->end_date->format('F d, Y') }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Travel Type</label>
                    <p class="text-gray-900 capitalize">{{ str_replace('_', ' ', $travel->travel_type) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Transportation</label>
                    <p class="text-gray-900 capitalize">{{ str_replace('_', ' ', $travel->transportation) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Source of Funds</label>
                    <p class="text-gray-900 capitalize">{{ str_replace('_', ' ', $travel->source_of_funds) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Submitted Date</label>
                    <p class="text-gray-900">{{ $travel->submitted_at ? $travel->submitted_at->format('M d, Y g:i A') : 'Pending' }}</p>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">Purpose of Travel</label>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $travel->purpose }}</p>
                </div>
            </div>
        </div>

        <!-- Approval Chain Status -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-4 border-b">Approval Chain Status</h2>
            
            <div class="space-y-4">
                <!-- Stage 1: Finance Officer -->
                @if($financeApproval)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            @if($financeApproval->status === 'approved')
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                            @elseif($financeApproval->status === 'rejected')
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-orange-100">
                                    <i class="fas fa-hourglass-start text-orange-600"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-semibold text-gray-900">1. Finance Officer Review</h3>
                            <p class="text-sm text-gray-600">{{ $financeApproval->approver->first_name ?? 'N/A' }} {{ $financeApproval->approver->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $financeApproval->approved_at ? $financeApproval->approved_at->format('M d, Y g:i A') : 'Pending' }}</p>
                            @if($financeApproval->signature_path)
                                <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-300">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Signature:</p>
                                    <img src="{{ Storage::url($financeApproval->signature_path) }}" alt="Signature" class="max-h-14 max-w-xs border border-gray-300 rounded">
                                </div>
                            @endif
                        </div>
                        <span class="inline-block text-xs px-3 py-1 rounded-full font-medium capitalize
                            @if($financeApproval->status === 'approved') bg-green-100 text-green-800
                            @elseif($financeApproval->status === 'rejected') bg-red-100 text-red-800
                            @else bg-orange-100 text-orange-800 @endif">
                            {{ $financeApproval->status }}
                        </span>
                    </div>
                @endif

                <!-- Stage 2: Accountant -->
                @if($accountantApproval)
                    <div class="flex items-start border-t pt-4">
                        <div class="flex-shrink-0 mr-4">
                            @if($accountantApproval->status === 'approved')
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                            @elseif($accountantApproval->status === 'rejected')
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-orange-100">
                                    <i class="fas fa-hourglass-start text-orange-600"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-semibold text-gray-900">2. Accountant Review</h3>
                            <p class="text-sm text-gray-600">{{ $accountantApproval->approver->first_name ?? 'N/A' }} {{ $accountantApproval->approver->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $accountantApproval->approved_at ? $accountantApproval->approved_at->format('M d, Y g:i A') : 'Pending' }}</p>
                            @if($accountantApproval->signature_path)
                                <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-300">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Signature:</p>
                                    <img src="{{ Storage::url($accountantApproval->signature_path) }}" alt="Signature" class="max-h-14 max-w-xs border border-gray-300 rounded">
                                </div>
                            @endif
                        </div>
                        <span class="inline-block text-xs px-3 py-1 rounded-full font-medium capitalize
                            @if($accountantApproval->status === 'approved') bg-green-100 text-green-800
                            @elseif($accountantApproval->status === 'rejected') bg-red-100 text-red-800
                            @else bg-orange-100 text-orange-800 @endif">
                            {{ $accountantApproval->status }}
                        </span>
                    </div>
                @endif

                <!-- Stage 3: Department Head -->
                @if($deptHeadApproval)
                    <div class="flex items-start border-t pt-4">
                        <div class="flex-shrink-0 mr-4">
                            @if($deptHeadApproval->status === 'approved')
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                            @elseif($deptHeadApproval->status === 'rejected')
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-orange-100">
                                    <i class="fas fa-hourglass-start text-orange-600"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-semibold text-gray-900">3. Department Head Approval</h3>
                            <p class="text-sm text-gray-600">{{ $deptHeadApproval->approver->first_name ?? 'N/A' }} {{ $deptHeadApproval->approver->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $deptHeadApproval->approved_at ? $deptHeadApproval->approved_at->format('M d, Y g:i A') : 'Pending' }}</p>
                            @if($deptHeadApproval->signature_path)
                                <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-300">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Signature:</p>
                                    <img src="{{ Storage::url($deptHeadApproval->signature_path) }}" alt="Signature" class="max-h-14 max-w-xs border border-gray-300 rounded">
                                </div>
                            @endif
                        </div>
                        <span class="inline-block text-xs px-3 py-1 rounded-full font-medium capitalize
                            @if($deptHeadApproval->status === 'approved') bg-green-100 text-green-800
                            @elseif($deptHeadApproval->status === 'rejected') bg-red-100 text-red-800
                            @else bg-orange-100 text-orange-800 @endif">
                            {{ $deptHeadApproval->status }}
                        </span>
                    </div>
                @endif

                <!-- Stage 4: President Final Approval -->
                <div class="flex items-start border-t pt-4">
                    <div class="flex-shrink-0 mr-4">
                        @if($canApprove)
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-purple-100">
                                <i class="fas fa-crown text-purple-600"></i>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-100">
                                <i class="fas fa-lock text-gray-600"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-semibold text-gray-900">4. University President (Final Approval)</h3>
                        @if($presidentApproval && $presidentApproval->approved_by)
                            <p class="text-sm text-gray-600">{{ $presidentApproval->approver->first_name ?? 'N/A' }} {{ $presidentApproval->approver->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $presidentApproval->approved_at ? $presidentApproval->approved_at->format('M d, Y g:i A') : 'Pending' }}</p>
                        @else
                            <p class="text-sm text-gray-600">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-gray-500">Awaiting your review</p>
                        @endif
                    </div>
                    @if($canApprove)
                        <span class="inline-block bg-purple-100 text-purple-800 text-xs px-3 py-1 rounded-full font-medium">
                            Pending Your Review
                        </span>
                    @elseif($presidentApproval && $presidentApproval->status === 'approved')
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium">
                            {{ $presidentApproval->status }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Final Approval Form -->
        @if($canApprove)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-4 border-b">University President - Final Decision</h2>
                
                <form action="{{ route('president.travel.approve', $travel) }}" method="POST" enctype="multipart/form-data" x-data="presidentApprovalForm()" @submit.prevent="submitForm">
                    @csrf

                    <!-- Status Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Final Decision</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50"
                                   :class="{'border-green-500 bg-green-50': status === 'approved'}">
                                <input type="radio" name="status" value="approved" x-model="status" @change="onStatusChange"
                                       class="mr-3 w-4 h-4">
                                <div>
                                    <span class="font-semibold text-gray-900">Approve Travel Request</span>
                                    <p class="text-sm text-gray-600">Grant final authorization for the travel to proceed</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-red-50"
                                   :class="{'border-red-500 bg-red-50': status === 'rejected'}">
                                <input type="radio" name="status" value="rejected" x-model="status" @change="onStatusChange"
                                       class="mr-3 w-4 h-4">
                                <div>
                                    <span class="font-semibold text-gray-900">Reject Travel Request</span>
                                    <p class="text-sm text-gray-600">Deny the travel request</p>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rejection Reason (conditional) -->
                    <div x-show="status === 'rejected'" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            Reason for Rejection *
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                  placeholder="Explain why you are rejecting this travel request..."
                                  x-bind:required="status === 'rejected'">{{ old('rejection_reason') }}</textarea>
                        <p class="text-xs text-gray-600 mt-1">Minimum 10 characters</p>
                        @error('rejection_reason')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comments (optional for both) -->
                    <div class="mb-6">
                        <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Comments (Optional)</label>
                        <textarea name="comments" id="comments" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Add any additional comments...">{{ old('comments') }}</textarea>
                        @error('comments')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- E-Signature Section -->
                    <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">Your Electronic Signature <span class="text-red-600">*</span></h3>
                        <p class="text-gray-600 mb-4">Upload your electronic signature to authorize this final decision and complete the approval chain.</p>
                        
                        <!-- Upload Signature Section -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Upload Your Signature</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 bg-white text-center cursor-pointer hover:bg-gray-50 transition"
                                 @click="$refs.signatureFileInput.click()"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="handleDrop($event)"
                                 :class="isDragging ? 'border-purple-500 bg-purple-50' : ''">
                                <i class="fas fa-signature text-5xl text-gray-400 mb-3"></i>
                                <p class="font-medium text-gray-800 text-lg">Click to upload your signature</p>
                                <p class="text-sm text-gray-500 mt-1">or drag and drop your signature image</p>
                                <p class="text-xs text-gray-400 mt-2">Accepted formats: PNG, JPG, PDF (Max 5MB)</p>
                            </div>
                            <input type="file" 
                                   x-ref="signatureFileInput" 
                                   @change="handleFileUpload($event)" 
                                   accept="image/*,.pdf" 
                                   class="hidden"
                                   required>
                            @error('signature_image')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Signature Preview -->
                        <div x-show="signatureDisplayed" class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700">Uploaded Signature Preview</label>
                                <button type="button" 
                                        @click="clearSignature()" 
                                        class="text-sm text-red-600 hover:text-red-700 font-medium">
                                    <i class="fas fa-times mr-1"></i> Change
                                </button>
                            </div>
                            <div class="bg-white rounded border border-gray-300 p-4 flex items-center justify-center"
                                 style="min-height: 120px;">
                                <img id="signaturePreviewImage" 
                                     :src="signatureData" 
                                     class="max-h-32 max-w-full object-contain"
                                     alt="Signature Preview">
                            </div>
                        </div>

                        <input type="hidden" name="signature_image" id="signature_image" :value="signatureData">

                        @if($errors->has('signature_image'))
                            <div class="bg-red-100 border border-red-300 rounded-lg p-3 mt-2">
                                <p class="text-red-800 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first('signature_image') }}</p>
                            </div>
                        @endif

                        <!-- Confirmation Text -->
                        <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg text-sm text-purple-800">
                            <p><i class="fas fa-info-circle mr-2"></i>By uploading your signature, you authorize this final approval decision and complete the travel authority approval chain.</p>
                        </div>
                    </div>

                    <!-- Important Notice -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                        <div class="space-y-2">
                            <p class="text-sm text-purple-800">
                                <i class="fas fa-crown text-purple-600 mr-2"></i>
                                <strong>Final Approval Notice:</strong> Your decision completes the entire travel approval chain. The employee will be notified of your decision.
                            </p>
                            <p class="text-sm text-purple-800">
                                <i class="fas fa-check-circle text-purple-600 mr-2"></i>
                                If approved: The travel request is fully authorized and the employee may proceed.
                            </p>
                            <p class="text-sm text-purple-800">
                                <i class="fas fa-times-circle text-purple-600 mr-2"></i>
                                If rejected: The entire travel request is denied and will not be processed.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('president.travel') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed"
                                :disabled="!status || !signatureData">
                            <span x-show="status !== 'rejected'">
                                <i class="fas fa-check mr-2"></i> Grant Final Approval
                            </span>
                            <span x-show="status === 'rejected'">
                                <i class="fas fa-times mr-2"></i> Deny Request
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="flex items-center">
                    <i class="fas fa-lock text-gray-400 text-3xl mr-4"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">This Request Has Already Been Processed</h3>
                        <p class="text-gray-600 text-sm mt-1">Final approval has already been completed for this travel request.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function presidentApprovalForm() {
    return {
        status: '{{ old("status", "") }}',
        isDragging: false,
        signatureData: '',
        fileName: '',
        signatureDisplayed: false,

        handleDrop(event) {
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileUpload({ target: { files } });
            }
            this.isDragging = false;
        },

        handleFileUpload(event) {
            const files = event.target.files;
            if (files.length === 0) return;
            const file = files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }
            const validTypes = ['image/png', 'image/jpeg', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                alert('Only PNG, JPG, and PDF files are allowed');
                return;
            }
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        let width = img.width;
                        let height = img.height;
                        const maxWidth = 800;
                        const maxHeight = 600;
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
                        
                        // Fill canvas with white background first (prevents black on transparent)
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, width, height);
                        
                        // Draw the image on white background
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Convert to base64 as PNG to preserve quality (not JPEG)
                        this.signatureData = canvas.toDataURL('image/png');
                        this.fileName = file.name.split('.')[0] + '_signature.png';
                        this.signatureDisplayed = true;
                        document.getElementById('signature_image').value = this.signatureData;
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const base64 = e.target.result;
                    if (base64.length > 1048576) {
                        alert('File is too large. Please use a smaller PDF or image.');
                        return;
                    }
                    this.signatureData = base64;
                    this.fileName = file.name;
                    this.signatureDisplayed = true;
                    document.getElementById('signature_image').value = this.signatureData;
                };
                reader.readAsDataURL(file);
            }
        },

        clearSignature() {
            this.signatureData = '';
            this.fileName = '';
            this.signatureDisplayed = false;
            document.getElementById('signature_image').value = '';
            this.$refs.signatureFileInput.value = '';
        },

        onStatusChange() {
            if (this.status !== 'rejected') {
                document.getElementById('rejection_reason').value = '';
            }
        },

        submitForm() {
            // Validate required fields
            if (!this.status) {
                alert('Please select a decision (Approve or Reject)');
                return;
            }

            if (!this.signatureData) {
                alert('Please upload your signature');
                return;
            }

            if (this.status === 'rejected') {
                const rejectionReasonField = document.getElementById('rejection_reason');
                if (!rejectionReasonField.value || rejectionReasonField.value.trim().length < 10) {
                    alert('Please provide a rejection reason (minimum 10 characters)');
                    return;
                }
            }

            // Ensure signature_image hidden field has the data
            document.getElementById('signature_image').value = this.signatureData;

            // Submit the form
            this.$el.submit();
        }
    };
}
</script>
@endpush
@endsection
