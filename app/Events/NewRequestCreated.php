<?php

namespace App\Events;

use App\Models\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewRequestCreated implements ShouldBroadcastNow
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
        return new PrivateChannel('purok.' . $this->purokId);
    }

    public function broadcastAs()
    {
        return 'new-request';
    }

    public function broadcastWith()
    {
        return [
            'purokId' => $this->purokId,
            'requestCount' => $this->requestCount,
            'message' => 'New request received'
        ];
    }
}
