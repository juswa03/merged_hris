<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BiometricRegistrationRequested implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function broadcastOn()
    {
        //return ['biometric-channel']; // or 
        return new Channel('biometric-channel');
    }

    public function broadcastAs()
    {
        return 'biometric.register';
    }

    public function broadcastWith()
    {
        return [
            'employeeId' => $this->employee->id,
            'name'       => $this->formatName($this->employee),
            'department' => $this->employee->department,
            'photo'      => $this->getPhotoUrl($this->employee->photo),
        ];
    }

    private function formatName($employee)
    {
        $middleInitial = $employee->middle_name ? strtoupper($employee->middle_name[0]) . '.' : '';
        return trim("{$employee->first_name} {$middleInitial} {$employee->last_name}");
    }

    private function getPhotoUrl($photo)
    {
        return $photo
            ? asset('storage/' . ltrim($photo, '/')) // Ensures URL is fully qualified
            : asset('images/icons/user-icon.webp'); // fallback image
    }
}
