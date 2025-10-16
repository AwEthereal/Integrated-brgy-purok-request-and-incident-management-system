<?php

namespace App\Events;

use App\Models\IncidentReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewIncidentReported implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;
    public $incidentCount;

    /**
     * Create a new event instance.
     */
    public function __construct(IncidentReport $incident, $incidentCount)
    {
        $this->incident = $incident;
        $this->incidentCount = $incidentCount;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Broadcast to barangay officials channel
        return new PrivateChannel('barangay-officials');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'new-incident';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'incident_id' => $this->incident->id,
            'incident_type' => $this->incident->incident_type,
            'description' => \Illuminate\Support\Str::limit($this->incident->description, 100),
            'location' => $this->incident->location,
            'reporter_name' => $this->incident->user->name ?? 'Unknown',
            'purok_name' => $this->incident->purok->name ?? 'N/A',
            'incidentCount' => $this->incidentCount,
            'created_at' => $this->incident->created_at->toIso8601String(),
            'message' => 'New incident report received'
        ];
    }
}
