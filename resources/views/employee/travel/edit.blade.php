@extends('employee.layouts.app')

@section('title', 'Edit Travel Authority')

@section('content')
<div class="mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Travel Authority</h2>

        <form action="{{ route('employees.travel.update', $travel) }}" method="POST" x-data="travelApplicationForm()" @submit.prevent="validateAndSubmit">
            @csrf
            @method('PUT')

            <!-- Travel Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type of Travel</label>
                <select name="travel_type" x-model="travelType" @change="onTravelTypeChange" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Travel Type</option>
                    @foreach(App\Models\TravelAuthority::getTravelTypes() as $value => $label)
                        <option value="{{ $value }}" {{ old('travel_type', $travel->travel_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('travel_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Travel Type Information -->
            <div x-show="travelType" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_PERSONAL_ABROAD }}'">
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Personal Travel Abroad Requirements</h4>
                        <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                            <li>Requires travel authority from the President</li>
                            <li>Without official time and official business</li>
                            <li>Personal funds will be used</li>
                        </ul>
                    </div>
                </template>
                <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_OFFICIAL_TIME }}'">
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Official Time Travel</h4>
                        <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                            <li>Expenses covered by official funds</li>
                            <li>Source of funds: MOOE</li>
                        </ul>
                    </div>
                </template>
                <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_OFFICIAL_BUSINESS }}'">
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Official Business Travel</h4>
                        <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                            <li>University-related business activities</li>
                            <li>Expenses covered by official funds</li>
                        </ul>
                    </div>
                </template>
                <template x-if="travelType === '{{ App\Models\TravelAuthority::TYPE_OFFICIAL_TRAVEL }}'">
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Official Travel</h4>
                        <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                            <li>Official university travel</li>
                            <li>May use university vehicle</li>
                        </ul>
                    </div>
                </template>
            </div>

            <!-- Employee Name -->
            <div class="mb-6">
                <label for="employee_name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="employee_name" id="employee_name" required readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                    value="{{ old('employee_name', auth()->user()->first_name . ' ' . auth()->user()->last_name) }}">
                @error('employee_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                    <input type="text" name="designation" id="designation" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('designation', $travel->designation) }}">
                    @error('designation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                    <input type="text" name="destination" id="destination" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('destination', $travel->destination) }}">
                    @error('destination')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Duration Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="duration_type" value="single_day" x-model="durationType" @change="onDurationChange" class="mr-3" {{ old('duration_type', $travel->duration_type) === 'single_day' ? 'checked' : '' }}>
                        <div>
                            <span class="block font-medium text-gray-900">Single Day</span>
                            <span class="text-sm text-gray-500">One day travel</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="duration_type" value="multiple_days" x-model="durationType" @change="onDurationChange" class="mr-3" {{ old('duration_type', $travel->duration_type) === 'multiple_days' ? 'checked' : '' }}>
                        <div>
                            <span class="block font-medium text-gray-900">Multiple Days</span>
                            <span class="text-sm text-gray-500">Multiple days travel</span>
                        </div>
                    </label>
                </div>
                @error('duration_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" id="start_date" x-model="startDate" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('start_date', $travel->start_date->format('Y-m-d')) }}">
                    @error('start_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="durationType === 'multiple_days'">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" id="end_date" x-model="endDate"
                        :required="durationType === 'multiple_days'"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('end_date', $travel->end_date ? $travel->end_date->format('Y-m-d') : '') }}">
                    @error('end_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Single Date Field -->
                <div x-show="durationType === 'single_day'" class="md:col-span-2">
                    <label for="inclusive_date_of_travel" class="block text-sm font-medium text-gray-700 mb-2">Travel Date</label>
                    <input type="date" name="inclusive_date_of_travel" id="inclusive_date_of_travel" x-model="singleDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('inclusive_date_of_travel', $travel->inclusive_date_of_travel->format('Y-m-d')) }}">
                    @error('inclusive_date_of_travel')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                <textarea name="purpose" id="purpose" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Describe the purpose of your travel...">{{ old('purpose', $travel->purpose) }}</textarea>
                @error('purpose')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transportation -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Transportation</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(App\Models\TravelAuthority::getTransportationTypes() as $value => $label)
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="transportation" value="{{ $value }}" 
                               class="mr-3" {{ old('transportation', $travel->transportation) == $value ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('transportation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Source of Funds -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Source of Funds</label>
                <div class="space-y-3">
                    @foreach(App\Models\TravelAuthority::getFundSources() as $value => $label)
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="source_of_funds" value="{{ $value }}" 
                               x-model="fundSource"
                               class="mr-3" {{ old('source_of_funds', $travel->source_of_funds) == $value ? 'checked' : '' }}>
                        <div>
                            <span class="block font-medium text-gray-900">{{ $label }}</span>
                            <span class="text-sm text-gray-500">
                                @if($value === 'mooe')
                                    Maintenance and Other Operating Expenses
                                @elseif($value === 'personal')
                                    Personal funds for personal travel
                                @else
                                    Other funding sources
                                @endif
                            </span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('source_of_funds')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Other Funds Specification -->
            <div x-show="fundSource === 'other'" class="mb-6">
                <label for="other_funds_specification" class="block text-sm font-medium text-gray-700 mb-2">Specify Other Funding Source</label>
                <input type="text" name="other_funds_specification" id="other_funds_specification"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ old('other_funds_specification', $travel->other_funds_specification) }}"
                    placeholder="Please specify the source of funds...">
                @error('other_funds_specification')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recommending Official Selection (Stage 1) -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="mb-3">
                    <h4 class="font-semibold text-blue-900 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white text-sm rounded-full mr-2">1</span>
                        Stage 1: Recommending Official
                    </h4>
                    <p class="text-sm text-blue-800 mt-1">
                        Select your Department Head or designated recommending official who will review and approve your travel request first.
                    </p>
                </div>

                @php
                // Get the current user's department and find department heads
                $currentUser = auth()->user();
                $userDept = $currentUser->personalInformation?->department;
                
                // Get all department heads and admins
                $recommendingOfficials = \App\Models\User::with('personalInformation')
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('name', ['Department Head', 'Super Admin']);
                    })
                    ->orderBy('first_name')
                    ->get();
                @endphp

                <select name="recommending_official_id" id="recommending_official_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Recommending Official...</option>
                    @foreach($recommendingOfficials as $official)
                        <option value="{{ $official->id }}" {{ old('recommending_official_id', $travel->recommending_official_id) == $official->id ? 'selected' : '' }}>
                            {{ $official->first_name }} {{ $official->last_name }}
                            @if($official->personalInformation?->department)
                                ({{ $official->personalInformation->department }})
                            @endif
                            - {{ implode(', ', $official->getRoleNames()->toArray()) }}
                        </option>
                    @endforeach
                </select>
                @error('recommending_official_id')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5-Stage Workflow Information -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-list-ol text-blue-600 mr-2"></i>
                    5-Stage Approval Process
                </h4>
                <div class="text-sm text-gray-700 space-y-2">
                    <div class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-xs rounded-full mr-2 flex-shrink-0 mt-0.5">1</span>
                        <div>
                            <strong>Recommending Official:</strong> Your selected department head will review
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-xs rounded-full mr-2 flex-shrink-0 mt-0.5">2</span>
                        <div>
                            <strong>Allotment Available:</strong> Finance Officer checks budget allotment
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-xs rounded-full mr-2 flex-shrink-0 mt-0.5">3</span>
                        <div>
                            <strong>Funds Availability:</strong> Accountant verifies available funds
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-xs rounded-full mr-2 flex-shrink-0 mt-0.5">4</span>
                        <div>
                            <strong>University President:</strong> Final approval authority
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-set fields based on travel type -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">Automated Settings</h4>
                <div class="text-sm text-gray-600">
                    <p x-show="travelType === 'personal_abroad'">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        This travel will require President's approval and will use personal funds.
                    </p>
                    <p x-show="travelType === 'official_time' || travelType === 'official_business'">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        This travel will use official funds (MOOE).
                    </p>
                </div>
            </div>

            <!-- Employee E-Signature Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6" x-data="signatureUploader()">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Electronic Signature</h2>
                <p class="text-gray-600 mb-4">Upload your electronic signature to confirm and submit your travel authority request for approval.</p>
                
                <!-- Upload Signature Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Upload Your Signature</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 bg-gray-50 text-center cursor-pointer hover:bg-gray-100 transition"
                         @click="$refs.signatureFileInput.click()"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         :class="isDragging ? 'border-blue-500 bg-blue-50' : ''">
                        <i class="fas fa-signature text-5xl text-gray-400 mb-3"></i>
                        <p class="font-medium text-gray-800 text-lg">Click to upload your signature</p>
                        <p class="text-sm text-gray-500 mt-1">or drag and drop your signature image</p>
                        <p class="text-xs text-gray-400 mt-2">Accepted formats: PNG, JPG, PDF (Max 5MB)</p>
                    </div>
                    <input type="file" 
                           x-ref="signatureFileInput" 
                           @change="handleFileUpload($event)" 
                           accept="image/*,.pdf" 
                           class="hidden">
                    @error('signature_image_base64')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Signature Preview -->
                <div x-show="signatureDisplayed" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
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

                <!-- File Name Display -->
                <div x-show="fileName" class="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>
                        <strong>File selected:</strong> <span x-text="fileName"></span>
                    </p>
                </div>

                <!-- Hidden field for signature data -->
                <input type="hidden" name="signature_image_base64" id="signature_image_base64" :value="signatureData">
                <input type="hidden" name="signature_method" value="upload">
                <input type="hidden" name="employee_signature" id="employee_signature" value="Signature - Uploaded">
                
                <!-- Confirmation Message -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        By uploading your signature, you confirm that all information provided is accurate and complete, and that you authorize this travel request.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('employees.travel') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg" :disabled="isSubmitting">
                    <span x-show="!isSubmitting">Update Travel Authority</span>
                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function signatureUploader() {
    return {
        signatureData: '',
        fileName: '',
        signatureDisplayed: false,
        isDragging: false,

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
            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit. Please choose a smaller file.');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please upload a PNG, JPG, or PDF file.');
                return;
            }

            // Read and store file
            const reader = new FileReader();
            reader.onload = (event) => {
                this.signatureData = event.target.result;
                this.fileName = file.name;
                this.signatureDisplayed = true;
                
                // Update hidden field
                document.getElementById('signature_image_base64').value = this.signatureData;
            };
            reader.readAsDataURL(file);
        },

        clearSignature() {
            this.signatureData = '';
            this.fileName = '';
            this.signatureDisplayed = false;
            document.getElementById('signature_image_base64').value = '';
            this.$refs.signatureFileInput.value = '';
        }
    };
}

function travelApplicationForm() {
    return {
        travelType: '{{ $travel->travel_type }}',
        durationType: '{{ $travel->duration_type }}',
        startDate: '{{ $travel->start_date->format('Y-m-d') }}',
        endDate: '{{ $travel->end_date ? $travel->end_date->format('Y-m-d') : '' }}',
        singleDate: '{{ $travel->inclusive_date_of_travel->format('Y-m-d') }}',
        fundSource: '{{ $travel->source_of_funds }}',
        isSubmitting: false,

        init() {
            // Set initial fund source based on travel type if present
            if (this.travelType) {
                this.onTravelTypeChange();
            }
        },

        onTravelTypeChange() {
            // Auto-set fund source based on travel type
            if (this.travelType === 'personal_abroad') {
                this.fundSource = 'personal';
                const personalRadio = document.querySelector('input[name="source_of_funds"][value="personal"]');
                if (personalRadio) {
                    personalRadio.checked = true;
                }
            } else if (this.travelType === 'official_time' || this.travelType === 'official_business' || this.travelType === 'official_travel') {
                this.fundSource = 'mooe';
                const mooeRadio = document.querySelector('input[name="source_of_funds"][value="mooe"]');
                if (mooeRadio) {
                    mooeRadio.checked = true;
                }
            }
        },

        onDurationChange() {
            if (this.durationType === 'single_day') {
                this.endDate = '';
            }
        },

        validateAndSubmit() {
            // Signature upload is optional when editing
            // The form can be submitted without uploading a new signature
            this.isSubmitting = true;
            this.$el.submit();
        }
    };
}
</script>
@endpush
@endsection
