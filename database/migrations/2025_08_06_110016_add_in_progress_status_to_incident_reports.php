<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'Pending' status to 'In Progress' if needed
        // This is a data migration to ensure consistency
        \DB::table('incident_reports')
            ->where('status', 'Pending')
            ->update(['status' => 'In Progress']);
            
        // No schema changes needed as we're just updating the status values
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'In Progress' back to 'Pending' if rolling back
        \DB::table('incident_reports')
            ->where('status', 'In Progress')
            ->update(['status' => 'Pending']);
    }
};
