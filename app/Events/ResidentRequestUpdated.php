<?php

namespace App\Events;

use App\Models\Request as RequestModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResidentRequestUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $status;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(RequestModel $request, $status, $message = null)
    {
        $this->request = $request;
        $this->status = $status;
        $this->message = $message ?? $this->getDefaultMessage($status);
    }

    /**
     * Get default message based on status
     */
    private function getDefaultMessage($status)
    {
        $messages = [
            'purok_approved' => 'Your request has been approved by the Purok Leader',
            'barangay_approved' => 'Your request has been approved by the Barangay Office',
            'rejected' => 'Your request has been rejected',
            'completed' => 'Your document is ready for pickup',
        ];

        return $messages[$status] ?? 'Your request status has been updated';
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Broadcast to the specific resident's private channel
        return new PrivateChannel('App.Models.User.' . $this->request->user_id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'request-updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'request_id' => $this->request->id,
            'status' => $this->status,
            'message' => $this->message,
            'form_type' => $this->request->form_type,
            'updated_at' => $this->request->updated_at->toIso8601String(),
        ];
    }
}
