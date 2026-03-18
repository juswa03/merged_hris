<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Employee;
use App\Models\LoginSession;
use Illuminate\Validation\Rules;
class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        $departments = \App\Models\Department::all();
        $positions = \App\Models\Position::all();
        $employmentTypes = \App\Models\EmploymentType::all();
        $jobStatuses = \App\Models\JobStatus::all();
        $positions = \App\Models\Position::all();
        $roles = \App\Models\Role::all();
        return view('auth.login', compact('departments', 'positions', 'employmentTypes', 'jobStatuses', 'positions', 'roles'));
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'loginEmail' => 'required|email',
                'loginPassword' => 'required',
            ]);

            if (Auth::attempt([
                'email' => $credentials['loginEmail'],
                'password' => $credentials['loginPassword']
            ])) {
                // Get authenticated user with role relationship loaded
                $user = Auth::user();
                
                // Force reload of role relationship
                $user->load('role');
                
                // Verify user has a valid role
                if (!$user->role) {
                    Auth::logout();
                    $request->session()->invalidate();
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'User role not found. Please contact administrator.'
                    ], 403)->header('Content-Type', 'application/json');
                }
                
                $request->session()->regenerate();

                // Track login session
                try {
                    LoginSession::create([
                        'user_id'          => $user->id,
                        'session_id'       => $request->session()->getId(),
                        'ip_address'       => $request->ip(),
                        'user_agent'       => $request->userAgent(),
                        'logged_in_at'     => now(),
                        'last_activity_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    // Non-critical — do not block login
                }

                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard'),
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role->name ?? 'Unknown'
                    ]
                ])->header('Content-Type', 'application/json');
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password. Please try again.'
            ], 401)->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Login error: ' . $e->getMessage(), [
                'email' => $request->input('loginEmail'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Mark the session as logged out
        try {
            LoginSession::where('session_id', $request->session()->getId())
                ->whereNull('logged_out_at')
                ->update(['logged_out_at' => now()]);
        } catch (\Exception $e) {
            // Non-critical
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {

    }

    /**
     * Handle registration request
     */
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|string|email|max:255|unique:tbl_users',
    //         'password' => 'required|string|min:8|confirmed',
    //         'role_id' => 'required|exists:tbl_roles,name',
    //     ]);


    //     $user = User::create([
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'role_id' => $request->role_id,
    //         'time_stamp_id' => $timestamp->id,
    //         'status' => 'active',
    //     ]);

    //     Auth::login($user);

    //     return redirect()->route('dashboard');
    // }


public function register(Request $request)
{
    $request->validate([
        // Personal Information
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'gender' => ['required', 'string', 'in:Male,Female,Other'],
        'birthdate' => ['required', 'date'],
        'civil_status' => ['required', 'string', 'in:Single,Married,Divorced,Widowed'],
        
        // Contact Information
        'contact_number' => ['required', 'string', 'max:11'],
        'address' => ['required', 'string', 'max:500'],
        
        // Employment Information
        'department_id' => ['required', 'exists:tbl_departments,id'],
        'position_id' => ['required', 'exists:tbl_positions,id'],
        'employment_type_id' => ['required', 'exists:tbl_employment_type,id'],
        'hire_date' => ['required', 'date'],
        
        // Account Information
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:tbl_users'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role_id' => ['required', 'exists:tbl_roles,id'],
        
        // Additional Information (optional fields from your form)
        'rfid_code' => ['nullable', 'string', 'max:255'],
        'biometric_user_id' => ['nullable', 'string', 'max:255'],
    ]);

    // Note: Removed photo upload since it's not in your current form
    // If you want to add it back, include it in the form and uncomment below

    // Create User
    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'status' => 'active',
        'role_id' => $request->role_id, // Use the role_id from form
    ]);

    // Create Employee
    $employee = Employee::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'gender' => $request->gender,
        'birthdate' => $request->birthdate,
        'contact_number' => $request->contact_number,
        'address' => $request->address,
        'photo_url' => null, // Set to null since photo upload is removed
        'hire_date' => $request->hire_date,
        'civil_status' => $request->civil_status,
        'department_id' => $request->department_id,
        'position_id' => $request->position_id,
        'employment_type_id' => $request->employment_type_id,
        'job_status_id' => 1, // Default to active status or get from your JobStatus model
        'rfid_code' => $request->rfid_code,
        'biometric_user_id' => $request->biometric_user_id,
        'user_id' => $user->id, // Link employee to user
    ]);

    // You might want to log in the user automatically
    // auth()->login($user);

   Auth::login($user);

    return redirect()->route('dashboard');
}
}