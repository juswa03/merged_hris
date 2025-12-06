<?php

namespace App\Services;

use App\Models\BiometricLog;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BiometricLogger
{
    /**
     * Log a biometric scan event.
     *
     * @param int $employeeId
     * @param int|null $deviceId
     * @param string $type      // e.g. 'fingerprint', 'face', 'backup_pin'
     * @param string $status    // e.g. 'success', 'failed'
     * @param string|null $timestamp
     * @return \App\Models\BiometricLog
     */
    public function log($employeeId, $deviceId, $type, $status = 'success', $timestamp = null)
    {
        try {
            $device = Device::firstOrCreate(
                ['device_id' => $deviceId],
                ['building' => 'Automotive Building', 'floor' => 'Third Floor', 'department_id' => 1, 'room' => 'HR Room']
            );

            $log = BiometricLog::create([
                'employee_id' => $employeeId,
                'device_id'   => $device->id, 
                'type'        => $type,
                'status'      => $status,
                'timestamp'   => $timestamp,
            ]);

            Log::info("✅ Biometric log recorded for employee {$employeeId}", [
                'device_id' => $deviceId,
                'type' => $type,
                'status' => $status,
                'timestamp' => $log->timestamp,
            ]);

            return $log;
        } catch (\Exception $e) {
            Log::error('❌ Failed to record biometric log', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Log a failed attempt separately for clarity.
     *
     * @param int $employeeId
     * @param int|null $deviceId
     * @param string $type
     * @param string|null $reason
     * @return \App\Models\BiometricLog
     */
    public function logFailedAttempt($employeeId, $deviceId, $type, $reason = null)
    {
        return $this->log($employeeId, $deviceId, $type, $reason ? "failed: {$reason}" : 'failed');
    }

    /**
     * Check if an employee logged in successfully today.
     *
     * @param int $employeeId
     * @return bool
     */
    public function hasLoggedInToday($employeeId)
    {
        return BiometricLog::where('employee_id', $employeeId)
            ->whereDate('timestamp', Carbon::today())
            ->where('status', 'like', 'success%')
            ->exists();
    }

    /**
     * Get all biometric logs for today (for one employee or all).
     *
     * @param int|null $employeeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayLogs($employeeId = null)
    {
        $query = BiometricLog::whereDate('timestamp', Carbon::today())
            ->orderBy('timestamp', 'asc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->get();
    }

    /**
     * Get recent biometric logs (limit default 10).
     *
     * @param int $limit
     * @param int|null $employeeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentLogs($limit = 10, $employeeId = null)
    {
        $query = BiometricLog::orderBy('timestamp', 'desc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Count all logs of an employee within a date range.
     *
     * @param int $employeeId
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @return int
     */
    public function countLogsInRange($employeeId, $startDate, $endDate)
    {
        return BiometricLog::where('employee_id', $employeeId)
            ->whereBetween('timestamp', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->count();
    }

    /**
     * Static method to log info messages
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info($message, $context = [])
    {
        Log::info("[BiometricLogger] {$message}", $context);
    }

    /**
     * Static method to log error messages
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error($message, $context = [])
    {
        Log::error("[BiometricLogger] {$message}", $context);
    }

    /**
     * Static method to log warning messages
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning($message, $context = [])
    {
        Log::warning("[BiometricLogger] {$message}", $context);
    }

    /**
     * Static method to log debug messages
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug($message, $context = [])
    {
        Log::debug("[BiometricLogger] {$message}", $context);
    }
}
