<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrollBiometricRequestMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $employee;
    private $sessionToken;
    public function __construct($employee, $sessionToken)
    {
        $this->employee = $employee;
        $this->sessionToken = $sessionToken;
        Log::info("🎯 EVENT CONSTRUCTOR - Employee ID: " . $employee->id);
    }

    public function broadcastOn()
    {
        Log::info("📡 BROADCASTING - Channel: biometric-channel, Employee: " . $this->employee->id);
        return new Channel('biometric-channel');
    }

    public function broadcastAs()
    {
        return 'biometric.register';
    }

    public function broadcastWith()
    {
        Log::info("📊 BROADCAST DATA - Preparing data for employee: " . $this->employee->id);
        
        $data = [
            'employeeId' => $this->employee->id,
            'sessionToken' => $this->sessionToken,
            'name'       => $this->formatName($this->employee),
            'department' => $this->employee->department->name,
            'photo'      => $this->getPhotoUrl($this->employee->photo_url),
        ];
        
        Log::info("📦 BROADCAST PAYLOAD: " . json_encode($data));
        return $data;
    }

    /**
     * This method is called when the job is processed by the queue
     */
    public function handle()
    {
        Log::info("🔄 QUEUE HANDLER - Processing broadcast for employee: " . $this->employee->id);
    }

    private function formatName($employee)
    {
        $middleInitial = $employee->middle_name ? strtoupper($employee->middle_name[0]) . '.' : '';
        return trim("{$employee->first_name} {$middleInitial} {$employee->last_name}");
    }

    private function getPhotoUrl($photo)
    {
        return $photo
            ? asset('storage/' . ltrim($photo, '/'))
            : asset('images/logos/uni_logo.png');
    }
}