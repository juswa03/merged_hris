<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\BiometricLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricLogController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(404, 'Employee record not found');
        }

        // Get filters from request
        $filters = [
            'start_date' => $request->get('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->get('end_date', now()->format('Y-m-d')),
            'type' => $request->get('type'),
            'status' => $request->get('status'),
        ];

        // Build query
        $query = BiometricLog::where('employee_id', $employee->id)
            ->with(['device'])
            ->orderBy('timestamp', 'desc');

        // Apply filters
        if ($filters['start_date']) {
            $query->whereDate('timestamp', '>=', $filters['start_date']);
        }

        if ($filters['end_date']) {
            $query->whereDate('timestamp', '<=', $filters['end_date']);
        }

        if ($filters['type']) {
            $query->where('type', $filters['type']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $logs = $query->paginate(20);

        // Get statistics
        $logStats = $this->getLogStatistics($employee, $filters);
        $patterns = $this->getAccessPatterns($employee, $filters);
        $recentActivity = $this->getRecentActivity($employee);

        return view('employee.biometric.logs', compact(
            'employee',
            'logs',
            'filters',
            'logStats',
            'patterns',
            'recentActivity'
        ));
    }

    private function getLogStatistics($employee, $filters)
    {
        $baseQuery = BiometricLog::where('employee_id', $employee->id);

        // Apply same filters as main query
        if ($filters['start_date']) {
            $baseQuery->whereDate('timestamp', '>=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $baseQuery->whereDate('timestamp', '<=', $filters['end_date']);
        }

        return [
            'total' => $baseQuery->count(),
            'today' => BiometricLog::where('employee_id', $employee->id)
                        ->whereDate('timestamp', today())
                        ->count(),
            'fingerprint' => $baseQuery->where('type', 'fingerprint')->count(),
            'rfid' => $baseQuery->where('type', 'rfid')->count(),
        ];
    }

    private function getAccessPatterns($employee, $filters)
    {
        $baseQuery = BiometricLog::where('employee_id', $employee->id);

        if ($filters['start_date']) {
            $baseQuery->whereDate('timestamp', '>=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $baseQuery->whereDate('timestamp', '<=', $filters['end_date']);
        }

        $totalLogs = $baseQuery->count();
        $successLogs = $baseQuery->where('status', 'success')->count();
        
        // Get most used device
        $mostUsedDevice = BiometricLog::where('employee_id', $employee->id)
            ->with('device')
            ->selectRaw('device_id, COUNT(*) as count')
            ->groupBy('device_id')
            ->orderByDesc('count')
            ->first();

        // Get preferred method
        $fingerprintCount = $baseQuery->where('type', 'fingerprint')->count();
        $rfidCount = $baseQuery->where('type', 'rfid')->count();
        
        $preferredMethod = $fingerprintCount >= $rfidCount ? 'fingerprint' : 'rfid';
        $methodPercentage = $totalLogs > 0 ? 
            round(($preferredMethod === 'fingerprint' ? $fingerprintCount : $rfidCount) / $totalLogs * 100) : 0;

        return [
            'most_used_device' => $mostUsedDevice->device->name ?? 'N/A',
            'preferred_method' => $preferredMethod,
            'method_percentage' => $methodPercentage,
            'success_rate' => $totalLogs > 0 ? round(($successLogs / $totalLogs) * 100) : 0,
        ];
    }

    private function getRecentActivity($employee)
    {
        return [
            'last_fingerprint' => BiometricLog::where('employee_id', $employee->id)
                                ->where('type', 'fingerprint')
                                ->where('status', 'success')
                                ->latest('timestamp')
                                ->value('timestamp'),
            'last_rfid' => BiometricLog::where('employee_id', $employee->id)
                            ->where('type', 'rfid')
                            ->where('status', 'success')
                            ->latest('timestamp')
                            ->value('timestamp'),
            'today_accesses' => BiometricLog::where('employee_id', $employee->id)
                                ->whereDate('timestamp', today())
                                ->count(),
            'avg_daily' => $this->calculateAverageDaily($employee),
        ];
    }

    private function calculateAverageDaily($employee)
    {
        $thirtyDaysAgo = now()->subDays(30);
        $totalAccesses = BiometricLog::where('employee_id', $employee->id)
                            ->where('timestamp', '>=', $thirtyDaysAgo)
                            ->count();
        
        return round($totalAccesses / 30);
    }

    public function export(Request $request)
    {
        $employee = Auth::user()->employee;
        
        // Similar filtering logic as index method
        $query = BiometricLog::where('employee_id', $employee->id)
            ->with(['device'])
            ->orderBy('timestamp', 'desc');

        // Apply filters
        if ($request->has('start_date')) {
            $query->whereDate('timestamp', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('timestamp', '<=', $request->end_date);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->get();

        // Generate CSV or Excel file
        return $this->generateExport($logs);
    }

    private function generateExport($logs)
    {
        $fileName = 'biometric-logs-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Date', 'Time', 'Type', 'Device', 'Location', 'Status', 'RFID Code']);
            
            // Add data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->timestamp->format('Y-m-d'),
                    $log->timestamp->format('H:i:s'),
                    ucfirst($log->type),
                    $log->device->name ?? 'N/A',
                    $log->location ?? 'Main Office',
                    ucfirst($log->status),
                    $log->rfid_code ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showDetails($id)
    {
        $employee = Auth::user()->employee;
        
        $log = BiometricLog::where('id', $id)
            ->where('employee_id', $employee->id)
            ->with(['device'])
            ->firstOrFail();

        // Return HTML for modal content
        $html = view('employee.biometric.log-details', compact('log'))->render();

        return response()->json(['html' => $html]);
    }
}