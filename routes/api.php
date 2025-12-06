<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\AttendanceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Attendance API Routes (for reports) - Protected for authenticated users
Route::middleware(['auth', 'can:access_admin'])->group(function () {
    Route::get('/attendance-summary', [AttendanceController::class, 'getAttendanceSummary']);
    Route::get('/employee-attendance/{employeeId}', [AttendanceController::class, 'getEmployeeAttendance']);
});

Route::prefix('biometric')->group(function () {
    // Existing endpoints
    Route::post('/start-enrollment', [BiometricController::class, 'startEnrollment']); // Trigger registration
    Route::get('/get-registered-templates', [BiometricController::class, 'getAllTemplates']); // Get all fingerprint templates
    Route::post('/process-enrollment', [BiometricController::class, 'processEnrollment']); // Receive fingerprint data
    Route::get('/attendance/last-status/{employeeId}', [BiometricController::class, 'getLastAttendanceStatus']); //Check if the attendance_type is time-in or time-out
    Route::post('/attendance/store', [BiometricController::class, 'storeAttendanceRecord']);
    Route::get('/attendance/today-summary/{employeeId}', [BiometricController::class, 'getTodayAttendanceSummary']);

    // NEW: Device management endpoints
    Route::post('/devices/register', [BiometricController::class, 'registerDevice']);
    Route::post('/devices/{id}/heartbeat', [BiometricController::class, 'deviceHeartbeat']);
    Route::put('/devices/{id}/location', [BiometricController::class, 'updateDeviceLocation']);
    Route::get('/devices/health-analytics', [BiometricController::class, 'deviceHealthAnalytics']);

    // NEW: Failed scans and audit logs
    Route::post('/scan/failed', [BiometricController::class, 'logFailedScan']);
    Route::get('/audit-logs', [BiometricController::class, 'getAuditLogs']);
    Route::get('/failed-scans/statistics', [BiometricController::class, 'failedScansStatistics']);
});