<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AppointmentCalled implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(
        public string $reference,
        public int $window
    ) {}

    public function broadcastOn()
    {
        return new Channel('queue');
    }

    // 🔴 THIS IS CRITICAL
    public function broadcastAs()
    {
        return 'AppointmentCalled';
    }
}
