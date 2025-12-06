<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Personal Information Section -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Personal Information</h2>
            
            <!-- First Name -->
            <div class="mt-4">
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <!-- Last Name -->
            <div class="mt-4">
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>

            <!-- Middle Name -->
            <div class="mt-4">
                <x-input-label for="middle_name" :value="__('Middle Name')" />
                <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" autocomplete="additional-name" />
                <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
            </div>

            <!-- Gender -->
            <div class="mt-4">
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <!-- Birthdate -->
            <div class="mt-4">
                <x-input-label for="birthdate" :value="__('Birthdate')" />
                <x-text-input id="birthdate" class="block mt-1 w-full" type="date" name="birthdate" :value="old('birthdate')" required />
                <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
            </div>

            <!-- Civil Status -->
            <div class="mt-4">
                <x-input-label for="civil_status" :value="__('Civil Status')" />
                <select id="civil_status" name="civil_status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">'Select Civil Status'</option>
                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                </select>
                <x-input-error :messages="$errors->get('civil_status')" class="mt-2" />
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Contact Information</h2>
            
            <!-- Contact Number -->
            <div class="mt-4">
                <x-input-label for="contact_number" :value="__('Contact Number')" />
                <x-text-input id="contact_number" class="block mt-1 w-full" type="tel" name="contact_number" :value="old('contact_number')" required autocomplete="tel" />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="mt-4">
                <x-input-label for="address" :value="__('Address')" />
                <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3" required>{{ old('address') }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>
        </div>

        <!-- Employment Information Section -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Employment Information</h2>
            
            <!-- Department -->
            <div class="mt-4">
                <x-input-label for="department_id" :value="__('Department')" />
                <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>

            <!-- Position -->
            <div class="mt-4">
                <x-input-label for="position_id" :value="__('Position')" />
                <select id="position_id" name="position_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">Select Position</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('position_id')" class="mt-2" />
            </div>

            <!-- Employment Type -->
            <div class="mt-4">
                <x-input-label for="employment_type_id" :value="__('Employment Type')" />
                <select id="employment_type_id" name="employment_type_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">Select Employment Type</option>
                    @foreach($employmentTypes as $employmentType)
                        <option value="{{ $employmentType->id }}" {{ old('employment_type_id') == $employmentType->id ? 'selected' : '' }}>{{ $employmentType->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('employment_type_id')" class="mt-2" />
            </div>

            <!-- Job Status -->
            <div class="mt-4">
                <x-input-label for="job_status_id" :value="__('Job Status')" />
                <select id="job_status_id" name="job_status_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">Select Job Status</option>
                    @foreach($jobStatuses as $jobStatus)
                        <option value="{{ $jobStatus->id }}" {{ old('job_status_id') == $jobStatus->id ? 'selected' : '' }}>{{ $jobStatus->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('job_status_id')" class="mt-2" />
            </div>

            <!-- Hire Date -->
            <div class="mt-4">
                <x-input-label for="hire_date" :value="__('Hire Date')" />
                <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date" :value="old('hire_date')" required />
                <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
            </div>
        </div>

        <!-- Account Information Section -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Account Information</h2>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Additional Information</h2>
            
            <!-- RFID Code -->
            <div class="mt-4">
                <x-input-label for="rfid_code" :value="__('RFID Code (Optional)')" />
                <x-text-input id="rfid_code" class="block mt-1 w-full" type="text" name="rfid_code" :value="old('rfid_code')" />
                <x-input-error :messages="$errors->get('rfid_code')" class="mt-2" />
            </div>

            <!-- Biometric User ID -->
            <div class="mt-4">
                <x-input-label for="biometric_user_id" :value="__('Biometric User ID (Optional)')" />
                <x-text-input id="biometric_user_id" class="block mt-1 w-full" type="text" name="biometric_user_id" :value="old('biometric_user_id')" />
                <x-input-error :messages="$errors->get('biometric_user_id')" class="mt-2" />
            </div>

            <!-- Photo -->
            <div class="mt-4">
                <x-input-label for="photo" :value="__('Profile Photo (Optional)')" />
                <input id="photo" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="photo" accept="image/*" />
                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-8">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                Already registered?
            </a>

            <x-primary-button class="ms-4">
                Register
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>