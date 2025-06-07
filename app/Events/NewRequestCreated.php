<?php

namespace App\Events;

use App\Models\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $purokId;
    public $requestCount;

    public function __construct($purokId, $requestCount)
    {
        $this->purokId = $purokId;
        $this->requestCount = $requestCount;
    }

    public function broadcastOn()
    {
        return new Channel('purok.' . $this->purokId);
    }

    public function broadcastAs()
    {
        return 'new-request';
    }
}
