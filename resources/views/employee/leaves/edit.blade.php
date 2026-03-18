@extends('employee.layouts.app')

@section('title', 'Edit Leave Application')

@section('content')
<div class="mx-auto px-4 py-8 max-w-5xl">
    <!-- Error Alert -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl mt-1"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-red-900 mb-3">Please Fix the Following Errors</h3>
                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 flex items-start">
                                <i class="fas fa-check-circle text-red-600 mr-2 mt-0.5 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Leave Application</h1>
                <p class="text-gray-600 mt-2">CS Form No. 6, Revised 2020</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm font-medium text-blue-900">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>
                    Filing Date: <span class="font-bold">{{ $leave->filing_date->format('M d, Y') }}</span>
                </p>
            </div>
        </div>
        @if($leave->status !== 'pending')
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    This application is no longer in pending status and cannot be edited.
                </p>
            </div>
        @endif
    </div>

    @if($leave->status === 'pending')
        <form action="{{ route('employees.leaves.update', $leave->id) }}" method="POST" x-data="{ ...leaveApplicationForm(), ...signatureUploader() }" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- ==================== PART 1: OFFICE/DEPARTMENT ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">1</span>
                    Office / Department
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                            Department / Office / Division <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="department" id="department" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('department', $leave->department) }}"
                            placeholder="Your department or office assignment">
                        @error('department')
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ==================== PART 2: EMPLOYEE NAME ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">2</span>
                    Name
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" required readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                            value="{{ old('last_name', $leave->user->last_name) }}">
                    </div>
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" required readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                            value="{{ old('first_name', $leave->user->first_name) }}">
                    </div>
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Middle Name
                        </label>
                        <input type="text" name="middle_name" id="middle_name" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                            value="{{ old('middle_name', $leave->user->personalInformation?->middle_name) }}">
                    </div>
                </div>
            </div>

            <!-- ==================== PART 3: DATE OF FILING ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">3</span>
                    Date of Filing
                </h2>

                <div class="max-w-md">
                    <label for="filing_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Filing Date <span class="text-red-600">*</span>
                    </label>
                    <input type="date" name="filing_date" id="filing_date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('filing_date', $leave->filing_date->format('Y-m-d')) }}">
                    @error('filing_date')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- ==================== PART 4: POSITION ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">4</span>
                    Position
                </h2>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Official Position Title <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="position" id="position" required readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                        value="{{ old('position', $leave->position) }}">
                </div>
            </div>

            <!-- ==================== PART 5: SALARY ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">5</span>
                    Salary
                </h2>

                <div class="max-w-md">
                    <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">
                        Monthly / Daily Salary Rate <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-2.5 text-gray-500 font-medium">₱</span>
                        <input type="number" name="salary" id="salary" required readonly step="0.01" min="0"
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                            value="{{ old('salary', $leave->salary) }}">
                    </div>
                </div>
            </div>

            <!-- ==================== PART 6: DETAILS OF APPLICATION ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 text-white text-sm font-bold rounded-full mr-3">6</span>
                    Details of Application
                </h2>

                <!-- 6.A - Type of Leave -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">A</span>
                        Type of Leave to Be Availed Of
                    </h3>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Select Leave Type <span class="text-red-600">*</span>
                        </label>
                        <select name="type" id="leave_type" x-model="leaveType" @change="onLeaveTypeChange" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select a leave type --</option>
                            @foreach(App\Models\Leave::getLeaveTypes() as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $leave->type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Leave Type Information Card -->
                    <div x-show="leaveType" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <template x-if="leaveType === 'vacation'">
                            <div>
                                <p class="text-sm font-medium text-blue-900 mb-2">Vacation Leave</p>
                                <ul class="text-xs text-blue-800 list-disc list-inside space-y-1">
                                    <li>For rest and recreation purposes</li>
                                    <li>Requires at least 5 days advance notice</li>
                                    <li>Must specify destination for vacation</li>
                                </ul>
                            </div>
                        </template>
                        <template x-if="leaveType === 'sick'">
                            <div>
                                <p class="text-sm font-medium text-blue-900 mb-2">Sick Leave</p>
                                <ul class="text-xs text-blue-800 list-disc list-inside space-y-1">
                                    <li>For employee illness or medical treatment</li>
                                    <li>Medical certificate may be required</li>
                                    <li>Specify illness details</li>
                                </ul>
                            </div>
                        </template>
                        <template x-if="leaveType === 'special_privilege'">
                            <div>
                                <p class="text-sm font-medium text-blue-900 mb-2">Special Privilege Leave</p>
                                <ul class="text-xs text-blue-800 list-disc list-inside space-y-1">
                                    <li>For personal business or special occasions</li>
                                    <li>Limited to 5 days per year</li>
                                    <li>Specify purpose and destination if needed</li>
                                </ul>
                            </div>
                        </template>
                        <template x-if="leaveType === 'study'">
                            <div>
                                <p class="text-sm font-medium text-blue-900 mb-2">Study Leave</p>
                                <ul class="text-xs text-blue-800 list-disc list-inside space-y-1">
                                    <li>For continuing education or professional development</li>
                                    <li>Specify degree program or exam details</li>
                                    <li>May require proof of enrollment or examination schedule</li>
                                </ul>
                            </div>
                        </template>
                        <template x-if="leaveType && !['vacation', 'sick', 'special_privilege', 'study'].includes(leaveType)">
                            <p class="text-sm text-blue-900">
                                <i class="fas fa-info-circle mr-1"></i>
                                Please complete the details section below with all required information for this leave type.
                            </p>
                        </template>
                    </div>
                </div>

                <!-- 6.B - Details of Leave -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">B</span>
                        Details of Leave
                    </h3>

                    <div class="space-y-4">
                        <!-- Vacation/Special Privilege Leave - Location -->
                        <template x-if="['vacation', 'special_privilege'].includes(leaveType)">
                            <div>
                                <label for="leave_location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Destination / Location <span class="text-red-600">*</span>
                                </label>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="radio" name="location_type" value="within" id="within_philippines" 
                                               x-model="locationWithinPhilippines" class="mr-3">
                                        <label for="within_philippines" class="text-sm text-gray-700">Within the Philippines</label>
                                    </div>
                                    <div x-show="locationWithinPhilippines === 'within'" class="ml-6">
                                        <input type="text" name="leave_location" id="leave_location"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            value="{{ old('leave_location', $leave->leave_location) }}"
                                            placeholder="City/Province">
                                    </div>

                                    <div class="flex items-center">
                                        <input type="radio" name="location_type" value="abroad" id="abroad"
                                               x-model="locationWithinPhilippines" class="mr-3">
                                        <label for="abroad" class="text-sm text-gray-700">Abroad</label>
                                    </div>
                                    <div x-show="locationWithinPhilippines === 'abroad'" class="ml-6">
                                        <input type="text" name="abroad_specify" id="abroad_specify"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            value="{{ old('abroad_specify', $leave->abroad_specify) }}"
                                            placeholder="Country and city">
                                    </div>
                                </div>
                                @error('leave_location')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                                @error('abroad_specify')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </template>

                        <!-- Sick Leave - Illness Details -->
                        <template x-if="leaveType === 'sick'">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Type of Sickness <span class="text-red-600">*</span>
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="sick_type" value="in_hospital" x-model="sickType" class="mr-3">
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900">In Hospital (Specify Illness)</span>
                                        </div>
                                    </label>

                                    <div x-show="sickType === 'in_hospital'" class="ml-6 mt-2">
                                        <input type="text" name="hospital_illness" id="hospital_illness"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            value="{{ old('hospital_illness', $leave->hospital_illness) }}"
                                            placeholder="Specify the illness">
                                    </div>

                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="sick_type" value="out_patient" x-model="sickType" class="mr-3">
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900">Out Patient (Specify Illness)</span>
                                        </div>
                                    </label>

                                    <div x-show="sickType === 'out_patient'" class="ml-6 mt-2">
                                        <input type="text" name="outpatient_illness" id="outpatient_illness"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            value="{{ old('outpatient_illness', $leave->outpatient_illness) }}"
                                            placeholder="Specify the illness">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Study Leave - Purpose -->
                        <template x-if="leaveType === 'study'">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Purpose of Study Leave <span class="text-red-600">*</span>
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="study_purpose" value="masters_degree" x-model="studyPurpose" class="mr-3">
                                        <span class="text-sm font-medium text-gray-900">Completion of Master's Degree</span>
                                    </label>

                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="study_purpose" value="bar_exam" x-model="studyPurpose" class="mr-3">
                                        <span class="text-sm font-medium text-gray-900">BAR/Board Examination Review</span>
                                    </label>

                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="study_purpose" value="other_study" x-model="studyPurpose" class="mr-3">
                                        <span class="text-sm font-medium text-gray-900">Other Purpose</span>
                                    </label>

                                    <div x-show="studyPurpose === 'other_study'" class="ml-6 mt-2">
                                        <input type="text" name="other_purpose_specify" id="other_purpose_specify"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            value="{{ old('other_purpose_specify', $leave->other_purpose_specify) }}"
                                            placeholder="Specify the purpose">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Special Privilege Leave - Type Selection -->
                        <template x-if="leaveType === 'special_privilege'">
                            <div>
                                <label for="slp_type" class="block text-sm font-medium text-gray-700 mb-3">
                                    Type of Special Leave Privilege <span class="text-red-600">*</span>
                                </label>
                                <select name="slp_type" id="slp_type" x-model="slpType"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Select Special Privilege Type --</option>
                                    <option value="funeral_mourning">Funeral/Mourning</option>
                                    <option value="graduation">Graduation Ceremony</option>
                                    <option value="enrollment">Enrollment of Child</option>
                                    <option value="wedding_anniversary">Wedding Anniversary</option>
                                    <option value="birthday">Birthday</option>
                                    <option value="hospitalization">Hospitalization (Immediate Family)</option>
                                    <option value="accident">Accident (Immediate Family)</option>
                                    <option value="relocation">Relocation</option>
                                    <option value="government_transaction">Government Transaction</option>
                                    <option value="calamity">Calamity/Disaster</option>
                                </select>
                                @error('slp_type')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </template>

                        <!-- Other Leave Details -->
                        <template x-if="leaveType && !['vacation', 'sick', 'special_privilege', 'study', ''].includes(leaveType)">
                            <div>
                                <label for="other_leave_details" class="block text-sm font-medium text-gray-700 mb-2">
                                    Additional Details <span class="text-red-600">*</span>
                                </label>
                                <textarea name="other_leave_details" id="other_leave_details" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Provide details relevant to your leave type...">{{ old('other_leave_details', $leave->other_leave_details) }}</textarea>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 6.C - Number of Working Days Applied For -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">C</span>
                        Number of Working Days Applied For
                    </h3>

                    <div class="space-y-4">
                        <!-- Duration Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Duration Type <span class="text-red-600">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="duration_type" value="full_day" x-model="durationType" @change="onDurationChange" class="mr-3">
                                    <div>
                                        <span class="block font-medium text-gray-900">Full Day</span>
                                        <span class="text-xs text-gray-500">Single day leave</span>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="duration_type" value="half_day" x-model="durationType" @change="onDurationChange" class="mr-3">
                                    <div>
                                        <span class="block font-medium text-gray-900">Half Day</span>
                                        <span class="text-xs text-gray-500">Morning or afternoon</span>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="duration_type" value="multiple_days" x-model="durationType" @change="onDurationChange" class="mr-3">
                                    <div>
                                        <span class="block font-medium text-gray-900">Multiple Days</span>
                                        <span class="text-xs text-gray-500">Multiple consecutive days</span>
                                    </div>
                                </label>
                            </div>
                            @error('duration_type')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date <span class="text-red-600">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" x-model="startDate" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}">
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date (for multiple days) -->
                        <div x-show="durationType === 'multiple_days'">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                End Date <span class="text-red-600">*</span>
                            </label>
                            <input type="date" name="end_date" id="end_date" x-model="endDate"
                                :required="durationType === 'multiple_days'"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('end_date', $leave->end_date ? $leave->end_date->format('Y-m-d') : '') }}">
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Half Day Time Selection -->
                        <div x-show="durationType === 'half_day'">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Time of Absence <span class="text-red-600">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="half_day_time" value="morning" x-model="halfDayTime" class="mr-3">
                                    <span class="font-medium text-gray-900">Morning</span>
                                </label>

                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="half_day_time" value="afternoon" x-model="halfDayTime" class="mr-3">
                                    <span class="font-medium text-gray-900">Afternoon</span>
                                </label>
                            </div>
                        </div>

                        <!-- Display Total Days -->
                        <div x-show="startDate" class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-900">
                                <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                                <strong>Total Days Applied:</strong> <span x-text="calculateTotalDays()"></span>
                            </p>
                            <p class="text-xs text-blue-800 mt-1" x-text="generateDateRangeDisplay()"></p>
                        </div>
                    </div>
                </div>

                <!-- 6.D - Commutation -->
                <div class="pb-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-bold rounded-full mr-2">D</span>
                        Commutation
                    </h3>

                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Is commutation being requested? <span class="text-red-600">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="commutation" value="not_requested" x-model="commutation" class="mr-3" required>
                            <div>
                                <span class="block font-medium text-gray-900">Not Requested</span>
                                <span class="text-xs text-gray-500">No cash conversion of leave credits</span>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="commutation" value="requested" x-model="commutation" class="mr-3" required>
                            <div>
                                <span class="block font-medium text-gray-900">Requested</span>
                                <span class="text-xs text-gray-500">Convert leave credits to cash</span>
                            </div>
                        </label>
                    </div>
                    @error('commutation')
                        <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- ==================== SIGNATURE SECTION ==================== -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Electronic Signature</h2>
                <p class="text-gray-600 mb-4">Upload your electronic signature to confirm your leave application.</p>
                
                <!-- Upload Signature Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Upload Your Signature</label>
                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 bg-gray-50 text-center cursor-pointer hover:bg-gray-100 transition"
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
                           name="electronic_signature"
                           @change="handleFileUpload($event)" 
                           accept="image/*,.pdf" 
                           class="hidden">
                    @error('electronic_signature')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Preview Section -->
                <div x-show="fileName" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700">Selected File</label>
                        <button type="button" 
                                @click="clearSignature()" 
                                class="text-sm text-red-600 hover:text-red-700 font-medium">
                            <i class="fas fa-times mr-1"></i> Change
                        </button>
                    </div>
                    <div class="p-3 bg-white rounded border border-gray-300">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-file-image text-blue-500 mr-2"></i>
                            <strong x-text="fileName"></strong> 
                            <span class="text-gray-500 ml-2" x-text="fileSize"></span>
                        </p>
                    </div>
                </div>

                <!-- Confirmation Message -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        By uploading your signature, you confirm that all information provided is accurate and complete, and that you authorize this leave application.
                    </p>
                </div>
            </div>

            <!-- ==================== FORM ACTIONS ==================== -->
            <div class="flex justify-end space-x-4 mb-8">
                <a href="{{ route('employees.leaves') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-6 py-2 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-2 rounded-lg transition flex items-center">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <i class="fas fa-lock text-red-500 text-4xl mb-4 inline-block"></i>
            <h3 class="text-xl font-semibold text-red-900 mb-2">Cannot Edit Application</h3>
            <p class="text-red-700 mb-6">This leave application cannot be edited because it is no longer in pending status.</p>
            <a href="{{ route('employees.leaves.show', $leave->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> View Application
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function leaveApplicationForm() {
    return {
        leaveType: '{{ old('type', $leave->type) }}',
        durationType: '{{ old('duration_type', $leave->duration_type) }}',
        startDate: '{{ old('start_date', $leave->start_date->format('Y-m-d')) }}',
        endDate: '{{ old('end_date', $leave->end_date ? $leave->end_date->format('Y-m-d') : '') }}',
        halfDayTime: '{{ old('half_day_time', $leave->half_day_time ?? 'morning') }}',
        locationWithinPhilippines: '{{ old('location_type', $leave->leave_location ? 'within' : 'abroad') }}',
        sickType: '{{ old('sick_type', $leave->sick_type ?? '') }}',
        studyPurpose: '{{ old('study_purpose', $leave->study_purpose ?? '') }}',
        slpType: '{{ old('slp_type', $leave->slp_type ?? '') }}',
        commutation: '{{ old('commutation', $leave->commutation ?? 'not_requested') }}',

        onLeaveTypeChange() {
            // Reset specific fields when leave type changes
            this.sickType = '';
            this.studyPurpose = '';
            this.slpType = '';
        },

        onDurationChange() {
            if (this.durationType === 'full_day') {
                this.endDate = '';
            } else if (this.durationType === 'half_day') {
                this.endDate = '';
            }
        },

        calculateTotalDays() {
            if (!this.startDate) return '0';

            if (this.durationType === 'half_day') {
                return '0.5';
            }

            if (this.durationType === 'full_day') {
                return '1';
            }

            if (this.durationType === 'multiple_days' && this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                return diffDays.toString();
            }

            return '0';
        },

        generateDateRangeDisplay() {
            if (!this.startDate) return '';

            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            };

            if (this.durationType === 'full_day' || this.durationType === 'half_day') {
                return `${this.durationType === 'half_day' ? 'Half Day - ' : ''}${formatDate(this.startDate)}`;
            }

            if (this.durationType === 'multiple_days' && this.endDate) {
                return `${formatDate(this.startDate)} to ${formatDate(this.endDate)}`;
            }

            return '';
        }
    };
}

function signatureUploader() {
    return {
        fileName: '',
        fileSize: '',
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

            // Store file name and size
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        clearSignature() {
            this.fileName = '';
            this.fileSize = '';
            this.$refs.signatureFileInput.value = '';
        }
    };
}
</script>
@endpush
@endsection
