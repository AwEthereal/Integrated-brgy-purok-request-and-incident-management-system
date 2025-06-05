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
        // Add columns to requests table
        Schema::table('requests', function (Blueprint $table) {
            $table->timestamp('feedback_requested_at')->nullable();
            $table->timestamp('feedback_provided_at')->nullable();
            $table->boolean('feedback_skipped')->default(false);
        });

        // Add columns to incident_reports table
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->timestamp('feedback_requested_at')->nullable();
            $table->boolean('feedback_skipped')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop columns from requests table
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['feedback_requested_at', 'feedback_provided_at', 'feedback_skipped']);
        });

        // Drop columns from incident_reports table
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropColumn(['feedback_requested_at', 'feedback_skipped']);
        });
    }
};
