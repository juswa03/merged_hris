<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University HRMIS - Login & Register</title>
    
    <!-- Replace CDN links with Laravel asset helpers or use Laravel Mix -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Add CSRF Token for forms -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .form-container {
            perspective: 1000px;
            position: relative;
            height: 100%;
        }
        .form-flip {
            transform-style: preserve-3d;
            transition: transform 0.8s;
            position: relative;
            width: 100%;
            min-height: 600px;
        }
        .form-flip.flipped {
            transform: rotateY(180deg);
        }
        .form-front, .form-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            top: 0;
            left: 0;
            overflow-y: auto;
        }
        .form-front {
            z-index: 2;
        }
        .form-back {
            transform: rotateY(180deg);
        }
        .input-highlight {
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
        }
        .input-highlight:focus {
            border-bottom: 2px solid #4f46e5;
        }
        .password-toggle {
            cursor: pointer;
            transition: color 0.3s;
        }
        .password-toggle:hover {
            color: #4f46e5;
        }
        .university-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/logos/uni_photo.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center overflow-y-auto">
    <div class="container mx-auto px-4">
           <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Left Side - University Info -->
                <div class="hidden md:block md:w-1/2 university-bg p-8 text-white">
                    <div class="flex flex-col h-full justify-center">
                        <h1 class="text-3xl font-bold mb-4">HRMIS Portal</h1>
                        <p class="mb-6">Human Resource Management Information System</p>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-users text-xl mr-3"></i>
                                <span>Employee Management</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-xl mr-3"></i>
                                <span>Attendance Tracking</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-file-invoice-dollar text-xl mr-3"></i>
                                <span>Payroll Processing</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Forms -->
                <div class="w-full md:w-1/2 p-8 form-container">
                    <div class="form-flip">

                        <!-- Login Form (Front) -->
                        <div class="front form-front">
                            <div class="front">
                            <div class="flex justify-center mb-6">
                               <img src="{{ asset('images/logos/uni_logo.png') }}" alt="BIPSU Logo" class="h-12">
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Sign In to Your Account</h2>
                            
                            <form id="loginForm" class="space-y-4">
                                <div>
                                    <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" id="loginEmail" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="@university.edu" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" id="loginPassword" class="input-highlight w-full pl-10 pr-10 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="••••••••" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('loginPassword', this)"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                                    </div>
                                    <div class="text-sm">
                                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>
                                    </div>
                                </div>
                                
                                <div>
                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                                        Sign in
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-6">
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gray-300"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                                    </div>
                                </div>
                                
                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <div>
                                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-300">
                                            <i class="fab fa-google text-red-500 mr-2"></i> Google
                                        </a>
                                    </div>
                                    <div>
                                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-300">
                                            <i class="fab fa-microsoft text-blue-500 mr-2"></i> Microsoft
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600">
                                    Don't have an account? 
                                    <button onclick="flipForm()" class="font-medium text-blue-600 hover:text-blue-500 ml-1">Register here</button>
                                </p>
                            </div>
                        </div>
                        
                        </div>

                                                <!-- Register Form (Back) -->
                        <div class="form-back absolute top-0 left-0 w-full h-full bg-white p-8 overflow-y-auto">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">Create New Account</h2>
                                <button onclick="flipForm()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <form id="registerForm" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                            <input type="text" name="first_name" id="first_name" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="John" required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                            <input type="text" name="last_name" id="last_name" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="Doe" required>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" name="middle_name" id="middle_name" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="Michael">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-venus-mars text-gray-400"></i>
                                            </div>
                                            <select name="gender" id="gender" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500 bg-white" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">Birthdate</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar text-gray-400"></i>
                                            </div>
                                            <input type="date" name="birthdate" id="birthdate" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Civil Status --}}
                                <div>
                                    <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-heart text-gray-400"></i>
                                        </div>
                                        <select name="civil_status" id="civil_status" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500 bg-white" required>
                                            <option value="">Select Civil Status</option>
                                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                                <option value="{{ $status }}" {{ old('civil_status') == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input type="tel" name="contact_number" id="contact_number" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="09123456789" required>
                                    </div>
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-home text-gray-400"></i>
                                        </div>
                                        <textarea name="address" id="address" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" rows="2" placeholder="Complete address" required></textarea>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" name="email" id="email" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="you@university.edu" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" name="password" id="password" class="input-highlight w-full pl-10 pr-10 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="••••••••" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('password', this)"></i>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                                </div>
                                
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" placeholder="••••••••" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-building text-gray-400"></i>
                                        </div>
                                        <select name="department_id" id="department_id" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500 bg-white" required>
                                            <option value="">Select Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                {{-- Position --}}
                                <div>
                                    <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-briefcase text-gray-400"></i>
                                        </div>
                                        <select name="position_id" id="position_id" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500 bg-white" required>
                                            <option value="">Select Position</option>
                                            @foreach($positions as $position)
                                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                                    {{ $position->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Employment type --}}
                                <div>
                                    <label for="employment_type_id" class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-clock text-gray-400"></i>
                                        </div>
                                        <select name="employment_type_id" id="employment_type_id" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500 bg-white" required>
                                            <option value="">Select Employment Type</option>
                                            @foreach($employmentTypes as $employmentType)
                                                <option value="{{ $employmentType->id }}" {{ old('employment_type_id') == $employmentType->id ? 'selected' : '' }}>
                                                    {{ $employmentType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Hire Date</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                        </div>
                                        <input type="date" name="hire_date" id="hire_date" class="input-highlight w-full pl-10 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:ring-0 focus:border-blue-500" required>
                                    </div>
                                </div>
                                
                                {{-- Roles --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                                        @foreach($roles as $role)
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="role_id" value="{{ $role->id }}" 
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                    {{ old('role_id', 4) == $role->id ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                                    </label>
                                </div>
                                
                                <div>
                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                                        Create Account
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600">
                                    Already have an account? 
                                    <button onclick="flipForm()" class="font-medium text-blue-600 hover:text-blue-500 ml-1">Sign in</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function flipForm() {
        const formFlip = document.querySelector('.form-flip');
        formFlip.classList.toggle('flipped');
    }

    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    // Login Form Submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            loginEmail: document.getElementById('loginEmail').value,
            loginPassword: document.getElementById('loginPassword').value
        };

        fetch('{{ route("login.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => {
            alert(error.message || 'Login failed. Please try again.');
        });
    });

    // Registration Form Submission
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        middle_name: document.getElementById('middle_name').value,
        gender: document.getElementById('gender').value,
        birthdate: document.getElementById('birthdate').value,
        civil_status: document.getElementById('civil_status').value,
        contact_number: document.getElementById('contact_number').value,
        address: document.getElementById('address').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value,
        department_id: document.getElementById('department_id').value,
        position_id: document.getElementById('position_id').value,
        employment_type_id: document.getElementById('employment_type_id').value,
        hire_date: document.getElementById('hire_date').value,
        role_id: document.querySelector('input[name="role_id"]:checked').value
    };

    fetch('{{ route("register.post") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        if (error.errors) {
            alert(Object.values(error.errors).join('\n'));
        } else {
            alert(error.message || 'Registration failed. Please try again.');
        }
    });
});
    </script>
</body>
</html>