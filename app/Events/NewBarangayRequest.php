<?php

namespace App\Events;

use App\Models\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBarangayRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $requestCount;

    /**
     * Create a new event instance.
     */
    public function __construct(Request $request, $requestCount)
    {
        $this->request = $request;
        $this->requestCount = $requestCount;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('barangay-officials');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-barangay-request';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'request_id' => $this->request->id,
            'form_type' => $this->request->form_type,
            'resident_name' => $this->request->user->name ?? 'Unknown',
            'purok_name' => $this->request->purok->name ?? 'Unknown Purok',
            'created_at' => $this->request->created_at->diffForHumans(),
            'requestCount' => $this->requestCount,
            'message' => 'New request awaiting barangay approval'
        ];
    }
}
