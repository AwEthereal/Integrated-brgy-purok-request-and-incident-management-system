<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\RequestStatusUpdated;
use App\Models\Request;

class TestWebSocket extends Command
{
    protected $signature = 'websocket:test {purok_id} {status=submitted}';
    protected $description = 'Test WebSocket connection and event broadcasting';

    public function handle()
    {
        $purokId = $this->argument('purok_id');
        $status = $this->argument('status');
        
        // Create a dummy request
        $request = new Request([
            'id' => 9999, // Dummy ID for testing
            'purok_id' => $purokId,
            'status' => $status,
            'user_id' => 1, // Dummy user ID
            'type' => 'test',
            'purpose' => 'Testing WebSocket',
        ]);
        
        $this->info("Dispatching RequestStatusUpdated event...");
        event(new RequestStatusUpdated($request, $purokId, $status));
        
        $this->info("Event dispatched to channel: purok." . $purokId);
    }
}
