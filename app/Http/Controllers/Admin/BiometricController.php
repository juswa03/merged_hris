<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Device;
use App\Models\Department;
use App\Models\FingerprintTemplate;
use App\Events\EnrollBiometricRequestMessage;
use App\Models\BiometricLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\BiometricLogger;
use App\Exports\BiometricEnrollmentExport;
use App\Exports\BiometricAuditLogExport;
use Maatwebsite\Excel\Facades\Excel;

class BiometricController extends Controller
{
    //
       protected $logger;

    public function __construct(BiometricLogger $logger)
    {
        $this->logger = $logger;
        $this->authorizeResource(Employee::class, 'employee');
    }

    /**
     * Display the biometric enrollment management page
     */
    public function index()
    {
        // Get employees who haven't enrolled fingerprint data yet
        $allEmployees = Employee::with(['department', 'user', 'position'])
            ->whereDoesntHave('fingerprintTemplate') // employees with no fingerprints yet
            ->whereHas('user', function ($q) {
                $q->where('status', 'Active'); // only active users
            })
            ->orderBy('created_at', 'desc')
            ->get();



    

        // Get enrolled employees for statistics
        $enrolledEmployees = Employee::whereHas('fingerprintTemplate')->count();

       $totalEmployees = Employee::whereHas('user', function ($q) {
        $q->where('status', 'Active');
        })
        ->count();

        $departments = Department::orderBy('name')->pluck('name');

        // Calculate enrollment statistics
        $enrollmentStats = [
            'total_employees' => $totalEmployees,
            'enrolled_count' => $enrolledEmployees,
            'pending_count' => $allEmployees->count(),
            'enrollment_percentage' => $totalEmployees > 0 ? round(($enrolledEmployees / $totalEmployees) * 100, 1) : 0
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'all_employees' => $allEmployees,
                'enrollment_stats' => $enrollmentStats,
                'departments' => $departments
            ]);
        }

        return view('admin.biometric.index', [
            'all_employees' => $allEmployees,
            'enrollment_stats' => $enrollmentStats,
            'departments' => $departments
        ]);
    }

    /**
     * Get employees for enrollment (API endpoint)
     */
    public function getUnenrolledEmployees(Request $request)
    {
        $query = Employee::with(['department', 'user', 'position'])
            ->whereDoesntHave('fingerprintTemplate')
            ->whereHas('user', function ($q) {
                $q->where('status', 'Active');
            });

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function($deptQuery) use ($search) {
                      $deptQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply department filter
        if ($request->has('department') && !empty($request->department)) {
            $query->whereHas('department', function($deptQuery) use ($request) {
                $deptQuery->where('name', $request->department);
            });
        }

        // Apply pagination
        $perPage = $request->get('per_page', 9);
        $employees = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Calculate statistics
        $totalEmployees = Employee::whereHas('user', function ($q) {
            $q->where('status', 'Active');
        })->count();

        $enrolledEmployees = Employee::whereHas('fingerprintTemplate')
            ->whereHas('user', function ($q) {
                $q->where('status', 'Active');
            })->count();

        $statistics = [
            'total_employees' => $totalEmployees,
            'enrolled_employees' => $enrolledEmployees,
            'pending_enrollments' => $employees->total(),
            'enrollment_percentage' => $totalEmployees > 0 ? round(($enrolledEmployees / $totalEmployees) * 100, 1) : 0
        ];

        return response()->json([
            'success' => true,
            'employees' => $employees->items(),
            'statistics' => $statistics,
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * Start biometric enrollment process
     */
    public function startEnrollment(Request $request, Employee $employee)
    {
        Log::info($request);
        $validated = $request->validate([
            'employee_id' => 'required|exists:tbl_employee,id',
        ]);
        
        $employee = Employee::where('id', $request->input('employee_id'))->first();
        $fullname = $employee->first_name . ' ' . ($employee->middle_name ? strtoupper($employee->middle_name[0]) . '. ' : '') . $employee->last_name;
        Log::info($fullname);
        try {
            // Check if employee is already enrolled
            if ($employee->fingerprintTemplate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee is already enrolled for fingerprint access'
                ], 400);
            }

            // Create enrollment session in a simple sessions table or use cache
            $sessionToken = bin2hex(random_bytes(32));
            
            // Store session in cache for 10 minutes
            cache()->put("fingerprint_session_{$sessionToken}", [
                'employee_id' => $employee->id,
                'device_id' => $validated['device_id'] ?? null,
                'device_type' => $validated['device_type'] ?? 'web_scanner',
                'started_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ], 600); // 10 minutes

            Log::info("Broadcasting...");
            $event = new EnrollBiometricRequestMessage($employee, $sessionToken);
            broadcast($event)->toOthers();
            Log::info('Broadcasting event data:', $event->broadcastWith());
            return response()->json([
                'success' => true,
                'message' => "Please ask {$fullname} to place their finger on the biometric scanner.",
                'session_token' => $sessionToken,
                'employee' => $employee->load(['department', 'position'])
            ]);

        } catch (\Exception $e) {
            Log::error('Fingerprint enrollment session creation failed', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start enrollment session'
            ], 500);
        }
    }

    /**
     * Process fingerprint enrollment data
     */
    public function processEnrollment(Request $request)
    {
        Log::info("request: " . $request);
        $validated = $request->validate([
            'session_token'        => 'required|string',
            'fingerprint_template' => 'required|string', // Base64 encoded fingerprint template
            'employee_id'          => 'required|integer|exists:tbl_employee,id',
        ]);
        Log::info("Verify successful");
        try {
            DB::beginTransaction();

            // Find and validate session
            $session = cache()->get("fingerprint_session_{$validated['session_token']}");

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired enrollment session'
                ], 400);
            }

            $employee = Employee::findOrFail($session['employee_id']);


            // Create fingerprint template record
            $fingerprintTemplate = FingerprintTemplate::create([
                'employee_id' => $employee->id,
                'template' => $validated['fingerprint_template'],
            ]);

            // Remove session from cache
            cache()->forget("fingerprint_session_{$validated['session_token']}");

            // Log enrollment activity
            Log::info('Fingerprint enrollment completed', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'enrolled_by' => auth()->user()->name ?? 'System'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint enrollment completed successfully',
                'template_id' => $fingerprintTemplate->id,
                'employee' => $employee->load(['department', 'position'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Fingerprint enrollment processing failed', [
                'session_token' => $validated['session_token'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process fingerprint enrollment'
            ], 500);
        }
    }

    /**
     * Cancel enrollment session
     */
    public function cancelEnrollment(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'required|string'
        ]);

        try {
            // Check if session exists
            $session = cache()->get("fingerprint_session_{$validated['session_token']}");
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or already completed'
                ], 404);
            }

            // Remove session from cache
            cache()->forget("fingerprint_session_{$validated['session_token']}");

            return response()->json([
                'success' => true,
                'message' => 'Enrollment session cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Fingerprint enrollment cancellation failed', [
                'session_token' => $validated['session_token'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel enrollment session'
            ], 500);
        }
    }

    /**
     * Get enrollment status
     */
    public function getEnrollmentStatus(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'required|string'
        ]);

        $session = cache()->get("fingerprint_session_{$validated['session_token']}");

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or expired'
            ], 404);
        }

        // Check if session expired
        if (Carbon::parse($session['expires_at'])->isPast()) {
            cache()->forget("fingerprint_session_{$validated['session_token']}");
            return response()->json([
                'success' => false,
                'message' => 'Session expired'
            ], 410);
        }

        return response()->json([
            'success' => true,
            'session' => $session
        ]);
    }

    /**
     * View enrolled employees
     */
    public function enrolled()
    {
        $enrolledEmployees = Employee::with(['department', 'user', 'position', 'fingerprintTemplate'])
            ->whereHas('fingerprintTemplate')
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::orderBy('name')->pluck('name');

        if (request()->wantsJson()) {
            return response()->json([
                'enrolled_employees' => $enrolledEmployees,
                'departments' => $departments
            ]);
        }

        return view('admin.biometric.enrolled', [
            'enrolled_employees' => $enrolledEmployees,
            'departments' => $departments
        ]);
    }

    /**
     * Remove fingerprint enrollment
     */
    public function removeEnrollment(Request $request, Employee $employee)
    {
        try {
            DB::beginTransaction();

            $fingerprintTemplate = $employee->fingerprintTemplate;
            
            if (!$fingerprintTemplate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee is not enrolled for fingerprint access'
                ], 404);
            }

            // Delete the fingerprint template
            $fingerprintTemplate->delete();

            // Log removal activity
            Log::info('Fingerprint enrollment removed', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'removed_by' => auth()->user()->name ?? 'System',
                'reason' => $request->get('reason', 'Administrative removal')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint enrollment removed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Fingerprint enrollment removal failed', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove fingerprint enrollment'
            ], 500);
        }
    }

    /**
     * Get enrollment statistics
     */
    public function getStatistics()
    {
        $totalEmployees = Employee::whereHas('user', function ($q) {
            $q->where('status', 'Active');
        })->count();

        $enrolledCount = Employee::whereHas('fingerprintTemplate')
            ->whereHas('user', function ($q) {
                $q->where('status', 'Active');
            })->count();

        $pendingCount = $totalEmployees - $enrolledCount;
        $enrollmentPercentage = $totalEmployees > 0 ? round(($enrolledCount / $totalEmployees) * 100, 1) : 0;

        // Department-wise statistics
        $departmentStats = DB::table('tbl_employee as e')
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            ->join('tbl_users as u', 'e.user_id', '=', 'u.id')
            ->leftJoin('tbl_fingerprint_templates as ft', 'e.id', '=', 'ft.employee_id')
            ->where('u.status', 'Active')
            ->groupBy('d.id', 'd.name')
            ->select([
                'd.name',
                DB::raw('COUNT(e.id) as total'),
                DB::raw('COUNT(ft.id) as enrolled'),
                DB::raw('ROUND((COUNT(ft.id) / COUNT(e.id)) * 100, 1) as percentage')
            ])
            ->get();

        // Recent enrollments
        $recentEnrollments = DB::table('tbl_fingerprint_templates as ft')
            ->join('tbl_employee as e', 'ft.employee_id', '=', 'e.id')
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            ->select([
                'ft.id',
                'ft.created_at',
                'e.first_name',
                'e.last_name',
                'e.employee_id',
                'd.name as department_name'
            ])
            ->orderBy('ft.created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_employees' => $totalEmployees,
                'enrolled_count' => $enrolledCount,
                'pending_count' => $pendingCount,
                'enrollment_percentage' => $enrollmentPercentage,
                'department_stats' => $departmentStats,
                'recent_enrollments' => $recentEnrollments
            ]
        ]);
    }
    public function getAllTemplates()
    {
        Log::info('📡 Biometric Scanner requested fingerprint templates.');

        $templates = FingerprintTemplate::with(['employee:id,first_name,last_name,middle_name,department_id,photo_url'])
            ->select('id', 'employee_id', 'template') // Added 'id' here
            ->get();

        if ($templates->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => '⚠ No fingerprint templates found.',
                'data' => [[
                    'id' => null, // Added id field
                    'employee_id' => null,
                    'template' => null,
                    'employee' => [
                        'id_number'  => 'N/A',
                        'name'       => 'No Employee',
                        'department' => 'N/A',
                        'photo'      => asset('images/icons/user_icon.png'),
                    ],
                    'dtr' => null
                ]],
            ], 200);
        }
        $data = $templates->map(function ($tpl) {
            $employee = $tpl->employee;

            // Get today's DTR entry for the employee
            $todayDtr = null;
            if ($employee) {
                $dtrEntry = \App\Models\DtrEntry::where('employee_id', $employee->id)
                    ->whereDate('dtr_date', today())
                    ->first();

                if ($dtrEntry) {
                    $todayDtr = [
                        'dtr_date' => $dtrEntry->dtr_date->format('Y-m-d'),
                        'am_arrival' => $dtrEntry->am_arrival,
                        'am_departure' => $dtrEntry->am_departure,
                        'pm_arrival' => $dtrEntry->pm_arrival,
                        'pm_departure' => $dtrEntry->pm_departure,
                        'total_hours' => $dtrEntry->total_hours,
                        'total_minutes' => $dtrEntry->total_minutes,
                        'under_time_minutes' => $dtrEntry->under_time_minutes,
                        'remarks' => $dtrEntry->remarks,
                        'status' => $dtrEntry->status,
                        'is_holiday' => $dtrEntry->is_holiday,
                        'is_weekend' => $dtrEntry->is_weekend,
                    ];
                }
            }

            return [
                'id' => $tpl->id, // Template primary key
                'employee_id' => $tpl->employee_id, // Foreign key to employee
                'template' => (string) $tpl->template,
                'department' => [
                    'id' => $employee->department->id ?? null,
                    'name' => $employee->department->name ?? 'N/A',
                ],
                'employee' => $employee ? [
                    'name'     => $employee->first_name . ' ' .
                                ($employee->middle_name ? $employee->middle_name[0] . '. ' : '') .
                                $employee->last_name,
                    'photo' => $employee->photo
                        ? asset('storage/' . $employee->photo)
                        : asset('images/icons/nikol.jpg'),
                ] : null,
                'dtr' => $todayDtr // Today's DTR data
            ];
        });

        return response()->json([
            'success' => true,
            'message' => '✅ Fingerprint templates loaded successfully.',
            'data' => $data,
        ]);
    }

    public function getLastAttendanceStatus($employeeId): JsonResponse
    {
        try {
            // Validate employee exists
            $employee = Employee::find($employeeId);
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found',
                    'employee_id' => $employeeId
                ], 404);
            }

            // Get the latest attendance record for today
            $latestAttendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->first();

            // If no attendance today, check if employee has any previous records
            if (!$latestAttendance) {
                $lastEverAttendance = Attendance::where('employee_id', $employeeId)
                    ->orderBy('created_at', 'desc')
                    ->first();

                return response()->json([
                    'success' => true,
                    'employee_id' => (int)$employeeId,
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'last_attendance_type' => null,
                    'last_attendance_time' => null,
                    'suggested_next_action' => 'in', // Start with time-in for new day
                    'message' => 'No attendance records found for today'
                ]);
            }

            // Determine suggested next action based on last attendance type
            $lastType = strtolower($latestAttendance->attendanceType->name ?? '');
            $suggestedNextAction = $this->determineNextAction($lastType);

            return response()->json([
                'success' => true,
                'employee_id' => (int)$employeeId,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'last_attendance_type' => $lastType,
                'last_attendance_time' => $latestAttendance->created_at->toDateTimeString(),
                'last_attendance_id' => $latestAttendance->id,
                'suggested_next_action' => $suggestedNextAction,
                'message' => 'Last attendance status retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attendance status',
                'error' => $e->getMessage(),
                'employee_id' => $employeeId
            ], 500);
        }
    }

    /**
     * Determine the next suggested action based on last attendance type
     * 
     * @param string $lastType
     * @return string
     */
    private function determineNextAction(string $lastType): string
    {
        return match ($lastType) {
            'in', 'break_end' => 'out', // After time-in or break end, suggest time-out
            'out', 'break_start' => 'in', // After time-out or break start, suggest time-in
            default => 'in' // Default to time-in for unknown types
        };
    }
                //morning Remark conditions if the attendance type value is 'IN': calculate if the attendance time is <= to 6:50am, mark as "EARLY IN". else if attendance time is around 7am to 7:59, mark as 'IN ON_TIME'. else, mark as 'LATE IN'.
                //Morning Remarks conditions if the attendance type is 'OUT': calculate if the attendance time is around 4 hours higher than the morning 'IN', mark as 'OUT ON-TIME'. else if, attendance time is below 4 hours duration than the morning IN, mark as 'EARLY OUT'. else, Mark as 'LATE OUT'.
                //afternoon Remarks conditions if the attendance type value is 'IN': Calculate if the attendance time is > the morning out, 
    /**
     * Store raw attendance data (for your SendRawAttendance method)
     * 
     * @param Request $request
     * @return JsonResponse
     */
public function storeAttendanceRecord(Request $request): JsonResponse
{
    Log::info("Storing attendance record:", $request->all());
    try {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:tbl_employee,id',
            'device.device_uid' => 'required|string|exists:devices,device_uid',
        ]);

        $employeeId = $validated['employee_id'];
        $deviceUid = $validated['device']['device_uid'];
        $now = now();

        // 1️⃣ Load employee with relationships
        $employee = Employee::with(['position', 'department'])
            ->findOrFail($employeeId);

        // 2️⃣ Find attendance source
        $attendanceSource = \App\Models\AttendanceSource::find(2);

        // 3️⃣ Find device from UID
        $device = Device::where('device_uid', $deviceUid)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.',
            ], 404);
        }

        // 4️⃣ Find last attendance record
        $lastRecord = Attendance::where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->first();

        $attendanceTypeName = '';
        $remarks = '';

        if (!$lastRecord) {
            $attendanceTypeName = 'in';
            $remarks = 'FIRST_IN';
        } else {
            $lastType = optional($lastRecord->attendanceType)->name;

            if ($lastType === 'in') {
                $attendanceTypeName = 'out';
                $timeIn = $lastRecord->created_at;
                $workDuration = $timeIn->diffInMinutes($now);
                $expectedWorkMinutes = 8 * 60;

                if ($workDuration < ($expectedWorkMinutes - 30)) {
                    $remarks = 'EARLY_OUT';
                } elseif ($workDuration <= ($expectedWorkMinutes + 15)) {
                    $remarks = 'ON_TIME_OUT';
                } else {
                    $remarks = 'LATE_OUT';
                }
            } else {
                $attendanceTypeName = 'in';
                $remarks = 'WORK_RESUMED';
            }
        }

        // 5️⃣ Attendance type record
        $attendanceType = \App\Models\AttendanceType::firstOrCreate(
            ['name' => $attendanceTypeName],
            ['name' => $attendanceTypeName]
        );

        // 6️⃣ Create attendance record
        $attendance = Attendance::create([
            'employee_id' => $employeeId,
            'attendance_source_id' => $attendanceSource->id ?? null,
            'attendance_type_id' => $attendanceType->id,
            'device_uid' => $device->device_uid,
            'remarks' => $remarks,
        ]);

        // 7️⃣ Process DTR
        app(\App\Services\DtrService::class)->processDtr(
            $employeeId,
            now()->toDateString()
        );

        // 8️⃣ Get updated DTR entry
        $dtrEntry = \App\Models\DtrEntry::where('employee_id', $employeeId)
            ->where('dtr_date', now()->toDateString())
            ->first();

        // 9️⃣ Get all today's attendance records
        $todayAttendances = Attendance::with(['attendanceType', 'attendanceSource'])
            ->where('employee_id', $employeeId)
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'asc')
            ->get();

        //  logging
        if (isset($validated['ip_address']) || isset($validated['location'])) {
            Log::info('Attendance recorded with additional info', [
                'attendance_id' => $attendance->id,
                'ip_address' => $validated['ip_address'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
        }

        // Prepare attendance records
        $attendanceRecords = $todayAttendances->map(function($att) {
            return [
                'id' => $att->id,
                'type' => $att->attendanceType->name ?? 'unknown',
                'time' => $att->created_at->format('h:i A'),
                'remarks' => $att->remarks,
                'source' => $att->attendanceSource->name ?? 'unknown',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'photo_url' => $employee->photo_url
                    ? asset($employee->photo_url)
                    : asset('images/icons/user_icon.png'),
                'position' => $employee->position->name ?? 'N/A',
                'department' => $employee->department->name ?? 'N/A',
            ],
            'attendance' => [
                'type' => $attendanceTypeName,
                'remarks' => $remarks,
                'timestamp' => $attendance->created_at->toDateTimeString(),
            ],
            'dtr' => $dtrEntry ? [
                'date' => $dtrEntry->dtr_date->format('Y-m-d'),
                'status' => $dtrEntry->status,
                'remarks' => $dtrEntry->remarks,
                'am_arrival' => $dtrEntry->am_arrival,
                'am_departure' => $dtrEntry->am_departure,
                'pm_arrival' => $dtrEntry->pm_arrival,
                'pm_departure' => $dtrEntry->pm_departure,
                'total_hours' => $dtrEntry->total_hours,
                'total_minutes' => $dtrEntry->total_minutes,
                'under_time_minutes' => $dtrEntry->under_time_minutes,
                'is_weekend' => $dtrEntry->is_weekend,
                'is_holiday' => $dtrEntry->is_holiday,
            ] : null,
            'today_attendances' => $attendanceRecords,
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to record attendance',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Get today's attendance summary for an employee
     * 
     * @param int $employeeId
     * @return JsonResponse
     */
    public function getTodayAttendanceSummary($employeeId): JsonResponse
    {
        try {
            $employee = Employee::find($employeeId);
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            $todayAttendances = Attendance::with(['attendanceType', 'attendanceSource'])
                ->where('employee_id', $employeeId)
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'asc')
                ->get();

            $summary = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'date' => today()->toDateString(),
                'total_records' => $todayAttendances->count(),
                'first_time_in' => null,
                'last_time_out' => null,
                'records' => []
            ];

            foreach ($todayAttendances as $attendance) {
                $record = [
                    'id' => $attendance->id,
                    'type' => $attendance->attendanceType->name,
                    'source' => $attendance->attendanceSource->name,
                    'time' => $attendance->created_at->toTimeString(),
                    'device_uid' => $attendance->device_uid,
                    'remarks' => $attendance->remarks
                ];

                $summary['records'][] = $record;

                // Track first time-in
                if ($attendance->attendanceType->name === 'in' && !$summary['first_time_in']) {
                    $summary['first_time_in'] = $attendance->created_at->toDateTimeString();
                }

                // Track last time-out
                if ($attendance->attendanceType->name === 'out') {
                    $summary['last_time_out'] = $attendance->created_at->toDateTimeString();
                }
            }

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'message' => 'Today\'s attendance summary retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attendance summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ================================
    // NEW ENHANCEMENT ENDPOINTS
    // ================================

    /**
     * Register a biometric device
     */
    public function registerDevice(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_uid' => 'required|string|unique:devices,device_uid',
                'device_name' => 'required|string|max:255',
                'device_model' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:255',
                'ip_address' => 'nullable|ip',
                'status' => 'nullable|in:active,inactive,maintenance,error'
            ]);

            $device = Device::create([
                'device_uid' => $validated['device_uid'],
                'device_type' => 'biometric',
                'device_name' => $validated['device_name'],
                'device_model' => $validated['device_model'] ?? 'ZKTeco Live10R',
                'location' => $validated['location'] ?? 'Unspecified',
                'ip_address' => $validated['ip_address'] ?? $request->ip(),
                'status' => $validated['status'] ?? 'active',
                'last_heartbeat_at' => now(),
                'templates_count' => 0
            ]);

            BiometricLogger::info("Device registered: {$device->device_name} ({$device->device_uid})");

            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully',
                'device_id' => $device->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            BiometricLogger::error("Device registration failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Device registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send device heartbeat
     */
    public function deviceHeartbeat(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:active,inactive,maintenance,error',
                'templates_count' => 'nullable|integer|min:0',
                'ip_address' => 'nullable|ip'
            ]);

            $device = Device::biometric()->findOrFail($id);

            $device->update([
                'status' => $validated['status'],
                'templates_count' => $validated['templates_count'] ?? $device->templates_count,
                'ip_address' => $validated['ip_address'] ?? $request->ip(),
                'last_heartbeat_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Heartbeat received successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Heartbeat processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log failed scan attempt
     */
    public function logFailedScan(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'nullable|exists:tbl_employee,id',
                'device_uid' => 'required|string',
                'failure_reason' => 'required|in:template_not_found,quality_too_low,device_error,timeout,unknown',
                'quality_score' => 'nullable|integer|min:0|max:100',
                'ip_address' => 'nullable|ip'
            ]);

            $failedScan = \App\Models\FailedScanAttempt::create([
                'employee_id' => $validated['employee_id'] ?? null,
                'device_uid' => $validated['device_uid'],
                'failure_reason' => $validated['failure_reason'],
                'quality_score' => $validated['quality_score'] ?? null,
                'ip_address' => $validated['ip_address'] ?? $request->ip(),
                'attempted_at' => now()
            ]);

            BiometricLogger::warning("Failed scan attempt: {$validated['failure_reason']} from device {$validated['device_uid']}");

            return response()->json([
                'success' => true,
                'message' => 'Failed scan logged successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log scan attempt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update device location
     */
    public function updateDeviceLocation(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'location' => 'required|string|max:255'
            ]);

            $device = Device::biometric()->findOrFail($id);

            $device->update([
                'location' => $validated['location']
            ]);

            BiometricLogger::info("Device location updated: {$device->device_name} to {$validated['location']}");

            return response()->json([
                'success' => true,
                'message' => 'Device location updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Location update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get device health analytics
     */
    public function deviceHealthAnalytics()
    {
        try {
            $devices = Device::biometric()->get();

            $analytics = [
                'total_devices' => $devices->count(),
                'active_devices' => $devices->where('status', 'active')->count(),
                'inactive_devices' => $devices->where('status', 'inactive')->count(),
                'error_devices' => $devices->where('status', 'error')->count(),
                'maintenance_devices' => $devices->where('status', 'maintenance')->count(),
                'devices' => []
            ];

            foreach ($devices as $device) {
                $lastHeartbeat = $device->last_heartbeat_at;
                $minutesSinceHeartbeat = $lastHeartbeat ? now()->diffInMinutes($lastHeartbeat) : null;
                $isOffline = $minutesSinceHeartbeat > 5; // Offline if no heartbeat in 5 minutes

                $analytics['devices'][] = [
                    'id' => $device->id,
                    'device_name' => $device->device_name,
                    'device_uid' => $device->device_uid,
                    'location' => $device->location,
                    'status' => $device->status,
                    'templates_count' => $device->templates_count,
                    'last_heartbeat' => $lastHeartbeat ? $lastHeartbeat->toDateTimeString() : null,
                    'minutes_since_heartbeat' => $minutesSinceHeartbeat,
                    'is_offline' => $isOffline
                ];
            }

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve device analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 50);
            $employeeId = $request->input('employee_id');
            $action = $request->input('action');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query = \App\Models\BiometricAuditLog::query();

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            if ($action) {
                $query->where('action', 'like', '%' . $action . '%');
            }

            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->where('created_at', '<=', $endDate);
            }

            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve audit logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get failed scan attempts statistics
     */
    public function failedScansStatistics(Request $request)
    {
        try {
            $days = $request->input('days', 7);
            $startDate = now()->subDays($days);

            $failedScans = \App\Models\FailedScanAttempt::where('attempted_at', '>=', $startDate)->get();

            $statistics = [
                'total_failed_scans' => $failedScans->count(),
                'by_reason' => $failedScans->groupBy('failure_reason')->map(function ($group) {
                    return $group->count();
                }),
                'by_device' => $failedScans->groupBy('device_uid')->map(function ($group) {
                    return $group->count();
                }),
                'average_quality_score' => $failedScans->whereNotNull('quality_score')->avg('quality_score'),
                'daily_counts' => []
            ];

            // Daily breakdown
            for ($i = 0; $i < $days; $i++) {
                $date = now()->subDays($i)->toDateString();
                $count = $failedScans->filter(function ($scan) use ($date) {
                    return $scan->attempted_at->toDateString() === $date;
                })->count();

                $statistics['daily_counts'][$date] = $count;
            }

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve failed scans statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportEnrollments(Request $request)
    {
        $filters = $request->only(['department_id', 'status']);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "biometric_enrollments_export_{$timestamp}.xlsx";
        return Excel::download(new BiometricEnrollmentExport($filters), $filename);
    }

    public function exportAuditLogs(Request $request)
    {
        $filters = $request->only(['employee_id', 'action', 'date_from', 'date_to']);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "biometric_audit_logs_export_{$timestamp}.xlsx";
        return Excel::download(new BiometricAuditLogExport($filters), $filename);
    }
}
