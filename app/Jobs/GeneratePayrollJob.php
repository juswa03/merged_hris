<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $period;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(\App\Models\PayrollPeriod $period, \App\Models\User $user)
    {
        $this->period = $period;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\DtrToPayrollService $service): void
    {
        try {
            $result = $service->generatePayrollFromDtr(
                $this->period,
                function ($processed, $total, $employee) {
                    $percentage = $total > 0 ? round(($processed / $total) * 100) : 0;
                    $message = "Processing {$employee->full_name} ({$processed}/{$total})";
                    
                    event(new \App\Events\PayrollGenerationProgress(
                        $this->user->id,
                        $message,
                        $percentage,
                        $processed,
                        $total
                    ));
                }
            );

            // Store result in session or cache if needed, but for now just notify completion
            // Since we can't write to the user's session from a queue, we rely on the event data
            // Or we could store it in a database table for "JobResults"
            
            // For this implementation, we'll pass the result in the event
            event(new \App\Events\PayrollGenerationCompleted($this->user->id, $result));

        } catch (\Exception $e) {
            event(new \App\Events\PayrollGenerationFailed($this->user->id, $e->getMessage()));
            throw $e; // Re-throw to mark job as failed in queue
        }
    }
}
