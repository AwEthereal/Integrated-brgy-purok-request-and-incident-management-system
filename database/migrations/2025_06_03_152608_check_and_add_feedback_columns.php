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
        // Check and add columns to requests table
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'feedback_requested_at')) {
                $table->timestamp('feedback_requested_at')->nullable();
            }
            if (!Schema::hasColumn('requests', 'feedback_provided_at')) {
                $table->timestamp('feedback_provided_at')->nullable();
            }
            if (!Schema::hasColumn('requests', 'feedback_skipped')) {
                $table->boolean('feedback_skipped')->default(false);
            }
        });

        // Check and add columns to incident_reports table
        Schema::table('incident_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('incident_reports', 'feedback_requested_at')) {
                $table->timestamp('feedback_requested_at')->nullable();
            }
            if (!Schema::hasColumn('incident_reports', 'feedback_skipped')) {
                $table->boolean('feedback_skipped')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't drop the columns in the down method to prevent data loss
        // If you need to remove these columns, create a new migration
    }
};
