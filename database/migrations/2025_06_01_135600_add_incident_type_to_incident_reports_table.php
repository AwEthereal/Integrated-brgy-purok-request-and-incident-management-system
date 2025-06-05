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
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->enum('incident_type', [
                'crime',
                'accident',
                'natural_disaster',
                'medical_emergency',
                'fire',
                'public_disturbance',
                'traffic_incident',
                'missing_person',
                'environmental_hazard',
                'other'
            ])->default('other')->after('user_id');
            
            // Add an index for better query performance
            $table->index('incident_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropIndex(['incident_type']);
            $table->dropColumn('incident_type');
        });
    }
};
