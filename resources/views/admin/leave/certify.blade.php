@extends('admin.layouts.app')

@section('title', 'Certify Leave Application - Section 7.A')

@section('content')
<div class="mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leave Credit Certification</h1>
                <p class="text-gray-600 mt-2">Section 7.A - Certification of Leave Credits</p>
            </div>
            <a href="{{ route('admin.leave.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Employee Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Employee Information</h2>
                
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

                <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Application Details</h3>
                
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
                        <p class="mt-1 text-gray-900">{{ $leave->reason }}</p>
                    </div>
                </div>
            </div>

            <!-- Certification Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Section 7.A - Certification of Leave Credits</h2>
                
                <form action="{{ route('admin.leave.store-certification', $leave) }}" method="POST" x-data="certificationForm()">
                    @csrf

                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Verify the employee's leave credit balance and enter the certification details below.
                        </p>
                    </div>

                    <!-- As of Date -->
                    <div class="mb-6">
                        <label for="credit_as_of_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Leave Credits as of Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="credit_as_of_date" id="credit_as_of_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('credit_as_of_date', $leave->credit_as_of_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                        @error('credit_as_of_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vacation Leave Credits -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">Vacation Leave (VL) Credits</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="vacation_earned" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Earned <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="vacation_earned" id="vacation_earned" step="0.25" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    x-model.number="vacationEarned"
                                    @change="calculateVacationBalance()"
                                    value="{{ old('vacation_earned', $leave->vacation_earned ?? '') }}">
                                @error('vacation_earned')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vacation_less" class="block text-sm font-medium text-gray-700 mb-2">
                                    Less This Application <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="vacation_less" id="vacation_less" step="0.25" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    x-model.number="vacationLess"
                                    @change="calculateVacationBalance()"
                                    value="{{ old('vacation_less', $leave->vacation_less ?? ($leave->days ?? 0)) }}">
                                @error('vacation_less')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vacation_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                    Balance
                                </label>
                                <input type="number" name="vacation_balance" id="vacation_balance" step="0.25" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                                    x-model.number="vacationBalance"
                                    value="{{ old('vacation_balance', $leave->vacation_balance ?? '') }}">
                            </div>
                        </div>

                        @if(old('vacation_less', $leave->vacation_less ?? 0) > old('vacation_earned', $leave->vacation_earned ?? 0))
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Insufficient VL Balance:</strong> This leave should be marked as "Without Pay"
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Sick Leave Credits -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">Sick Leave (SL) Credits</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="sick_earned" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Earned <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="sick_earned" id="sick_earned" step="0.25" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    x-model.number="sickEarned"
                                    @change="calculateSickBalance()"
                                    value="{{ old('sick_earned', $leave->sick_earned ?? '') }}">
                                @error('sick_earned')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sick_less" class="block text-sm font-medium text-gray-700 mb-2">
                                    Less This Application <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="sick_less" id="sick_less" step="0.25" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    x-model.number="sickLess"
                                    @change="calculateSickBalance()"
                                    value="{{ old('sick_less', $leave->sick_less ?? '') }}">
                                @error('sick_less')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sick_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                    Balance
                                </label>
                                <input type="number" name="sick_balance" id="sick_balance" step="0.25" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                                    x-model.number="sickBalance"
                                    value="{{ old('sick_balance', $leave->sick_balance ?? '') }}">
                            </div>
                        </div>

                        @if(old('sick_less', $leave->sick_less ?? 0) > old('sick_earned', $leave->sick_earned ?? 0))
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Insufficient SL Balance:</strong> This leave should be marked as "Without Pay"
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- HR Officer Signature -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">HR Officer / Admin Officer Signature</h3>
                        
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
                            @error('certification_signature')
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

                        <input type="hidden" name="certification_signature" id="certification_signature" :value="signatureData">
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.leave.index') }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2"></i>Back without saving
                        </a>
                        <div class="space-x-3">
                            <button type="reset" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                                Clear
                            </button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg" :disabled="isSubmitting">
                                <span x-show="!isSubmitting">
                                    <i class="fas fa-check-circle mr-2"></i>Submit Certification
                                </span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Status & Notes -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Status</h3>
                
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($leave->status === 'pending')
                            bg-yellow-100 text-yellow-800
                        @elseif($leave->status === 'pending_certification')
                            bg-blue-100 text-blue-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div>
                        <label class="block text-gray-600 font-medium">Leave Type</label>
                        <p class="text-gray-900">{{ $leave->getLeaveTypeDisplay() }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 font-medium">Days</label>
                        <p class="text-gray-900">{{ $leave->days ?? 1 }} days</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 font-medium">Start Date</label>
                        <p class="text-gray-900">{{ $leave->start_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            @if($leave->certifiedBy)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    Already certified by {{ $leave->certifiedBy->first_name }} {{ $leave->certifiedBy->last_name }}
                </p>
                <p class="text-xs text-green-700 mt-2">{{ $leave->certified_at->format('M d, Y \a\t H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function certificationForm() {
    return {
        vacationEarned: parseFloat('{{ old("vacation_earned", $leave->vacation_earned ?? 0) }}') || 0,
        vacationLess: parseFloat('{{ old("vacation_less", $leave->vacation_less ?? 0) }}') || 0,
        vacationBalance: parseFloat('{{ old("vacation_balance", ($leave->vacation_earned ?? 0) - ($leave->vacation_less ?? 0)) }}') || 0,
        
        sickEarned: parseFloat('{{ old("sick_earned", $leave->sick_earned ?? 0) }}') || 0,
        sickLess: parseFloat('{{ old("sick_less", $leave->sick_less ?? 0) }}') || 0,
        sickBalance: parseFloat('{{ old("sick_balance", ($leave->sick_earned ?? 0) - ($leave->sick_less ?? 0)) }}') || 0,
        
        signatureData: '',
        fileName: '',
        signatureDisplayed: false,
        isDragging: false,
        isSubmitting: false,

        calculateVacationBalance() {
            this.vacationBalance = this.vacationEarned - this.vacationLess;
        },

        calculateSickBalance() {
            this.sickBalance = this.sickEarned - this.sickLess;
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
                // For large images, compress/resize
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
                    document.getElementById('certification_signature').value = base64Data;
                    
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
            document.getElementById('certification_signature').value = '';
            this.$refs.signatureFileInput.value = '';
        }
    };
}
</script>
@endpush
@endsection
