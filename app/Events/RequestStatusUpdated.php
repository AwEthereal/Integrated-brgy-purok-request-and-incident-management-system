<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $purokId;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Request $request
     * @param int $purokId
     * @param string $status
     * @return void
     */
    public function __construct($request, $purokId, $status)
    {
        $this->request = $request;
        $this->purokId = $purokId;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('purok.' . $this->purokId),
            new PrivateChannel('App.Models.User.' . $this->request->user_id)
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'request-status-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $statusMessages = [
            'purok_approved' => 'Your request has been approved by the Purok Leader',
            'barangay_approved' => 'Your request has been approved by the Barangay. Document is ready for pickup!',
            'rejected' => 'Your request has been rejected',
            'completed' => 'Your request has been marked as completed'
        ];
        
        return [
            'request_id' => $this->request->id,
            'status' => $this->status,
            'message' => $statusMessages[$this->status] ?? 'Your request status has been updated',
            'purpose' => $this->request->purpose,
            'form_type' => $this->request->form_type,
            'updated_at' => $this->request->updated_at->toDateTimeString(),
        ];
    }
}
