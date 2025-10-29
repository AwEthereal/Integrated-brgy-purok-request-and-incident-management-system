<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes to requests table
        Schema::table('requests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('purok_id');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });

        // Add indexes to incident_reports table
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('purok_id');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('purok_id');
            $table->index('is_approved');
            $table->index(['role', 'purok_id']);
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['purok_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['purok_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['purok_id']);
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['role', 'purok_id']);
        });
    }
};