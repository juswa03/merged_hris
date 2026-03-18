<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class SystemHealthController extends Controller
{
    public function index()
    {
        // Disk Usage
        $storagePath = storage_path();
        $diskFree    = disk_free_space($storagePath);
        $diskTotal   = disk_total_space($storagePath);
        $diskUsed    = $diskTotal - $diskFree;
        $diskPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        // Database size
        $dbName = DB::connection()->getDatabaseName();
        $dbSizeResult = DB::select(
            "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
             FROM information_schema.TABLES WHERE table_schema = ?",
            [$dbName]
        );
        $dbSizeMb = $dbSizeResult[0]->size_mb ?? 0;
        $tableCount = DB::select(
            "SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE table_schema = ?",
            [$dbName]
        )[0]->cnt ?? 0;

        // Queue / Jobs
        $pendingJobs = 0;
        try {
            $pendingJobs = DB::table('jobs')->count();
        } catch (\Exception $e) {}

        $failedJobs = 0;
        try {
            $failedJobs = DB::table('failed_jobs')->count();
        } catch (\Exception $e) {}

        // Recent failed jobs
        $recentFailedJobs = collect();
        try {
            $recentFailedJobs = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {}

        // Cache check
        $cacheStatus = 'Unknown';
        try {
            Cache::put('_health_check', 1, 10);
            $cacheStatus = Cache::get('_health_check') === 1 ? 'Operational' : 'Error';
        } catch (\Exception $e) {
            $cacheStatus = 'Error';
        }

        // Log file size
        $logPath  = storage_path('logs/laravel.log');
        $logSizeKb = file_exists($logPath) ? round(filesize($logPath) / 1024, 1) : 0;

        // Server info
        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env'             => app()->environment(),
            'debug_mode'      => config('app.debug'),
            'timezone'        => config('app.timezone'),
            'driver_session'  => config('session.driver'),
            'driver_queue'    => config('queue.default'),
            'driver_cache'    => config('cache.default'),
        ];

        return view('admin.system-health.index', compact(
            'diskFree', 'diskTotal', 'diskUsed', 'diskPercent',
            'dbSizeMb', 'tableCount',
            'pendingJobs', 'failedJobs', 'recentFailedJobs',
            'cacheStatus',
            'logSizeKb',
            'info'
        ));
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return back()->with('success', 'Cache cleared successfully.');
    }

    public function clearFailedJobs()
    {
        Artisan::call('queue:flush');

        return back()->with('success', 'Failed jobs cleared successfully.');
    }

    public function clearLog()
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return back()->with('success', 'Log file cleared successfully.');
    }
}
