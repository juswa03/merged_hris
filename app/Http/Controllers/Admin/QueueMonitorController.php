<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class QueueMonitorController extends Controller
{
    public function index()
    {
        $pendingJobs = collect();
        $failedJobs  = collect();
        $stats       = [
            'pending'    => 0,
            'failed'     => 0,
            'processed_today' => 0,
        ];

        try {
            // Pending jobs
            if (DB::getSchemaBuilder()->hasTable('jobs')) {
                $pendingJobs = DB::table('jobs')
                    ->orderBy('available_at')
                    ->limit(100)
                    ->get()
                    ->map(function ($job) {
                        $payload = json_decode($job->payload, true);
                        $job->display_name = $payload['displayName'] ?? $payload['job'] ?? 'Unknown';
                        $job->attempts_count = $job->attempts;
                        $job->available_at_human = \Carbon\Carbon::createFromTimestamp($job->available_at)->diffForHumans();
                        $job->created_at_human   = \Carbon\Carbon::createFromTimestamp($job->created_at)->diffForHumans();
                        return $job;
                    });
                $stats['pending'] = DB::table('jobs')->count();
            }

            // Failed jobs
            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')
                    ->orderByDesc('failed_at')
                    ->limit(100)
                    ->get()
                    ->map(function ($job) {
                        $payload = json_decode($job->payload, true);
                        $job->display_name    = $payload['displayName'] ?? $payload['job'] ?? 'Unknown';
                        $job->failed_at_human = \Carbon\Carbon::parse($job->failed_at)->diffForHumans();
                        $exception = $job->exception ?? '';
                        $lines = explode("\n", $exception);
                        $job->exception_short = $lines[0] ?? 'Unknown error';
                        return $job;
                    });
                $stats['failed'] = DB::table('failed_jobs')->count();
            }
        } catch (\Exception $e) {
            // Tables may not exist in all environments; silently skip
        }

        return view('admin.queue-monitor.index', compact('pendingJobs', 'failedJobs', 'stats'));
    }

    public function getStats()
    {
        $data = ['pending' => 0, 'failed' => 0, 'processed_today' => 0];

        try {
            if (DB::getSchemaBuilder()->hasTable('jobs')) {
                $data['pending'] = DB::table('jobs')->count();
            }
            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $data['failed'] = DB::table('failed_jobs')->count();
            }
        } catch (\Exception $e) {}

        return response()->json($data);
    }

    public function retryJob(string $id)
    {
        try {
            Artisan::call('queue:retry', ['id' => [$id]]);
            return back()->with('success', "Job #{$id} queued for retry.");
        } catch (\Exception $e) {
            return back()->with('error', 'Retry failed: ' . $e->getMessage());
        }
    }

    public function retryAll()
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            return back()->with('success', 'All failed jobs queued for retry.');
        } catch (\Exception $e) {
            return back()->with('error', 'Retry all failed: ' . $e->getMessage());
        }
    }

    public function deleteJob(string $id)
    {
        try {
            DB::table('failed_jobs')->where('uuid', $id)->delete();
            return back()->with('success', 'Failed job deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function clearAll()
    {
        try {
            Artisan::call('queue:flush');
            return back()->with('success', 'All failed jobs cleared.');
        } catch (\Exception $e) {
            return back()->with('error', 'Clear failed: ' . $e->getMessage());
        }
    }
}
