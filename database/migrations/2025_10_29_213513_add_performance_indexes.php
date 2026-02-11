<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropIndexIfExists(string $table, string $index): void
    {
        try {
            $database = DB::getDatabaseName();
            if (!is_string($database) || $database === '') {
                return;
            }

            $rows = DB::select(
                'SELECT 1
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = ?
                   AND INDEX_NAME = ?
                 LIMIT 1',
                [$database, $table, $index]
            );

            if (count($rows) > 0) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
            }
        } catch (\Throwable $e) {
        }
    }

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
        $this->dropIndexIfExists('requests', 'requests_user_id_index');
        $this->dropIndexIfExists('requests', 'requests_status_index');
        $this->dropIndexIfExists('requests', 'requests_purok_id_index');
        $this->dropIndexIfExists('requests', 'requests_user_id_status_index');
        $this->dropIndexIfExists('requests', 'requests_created_at_index');

        $this->dropIndexIfExists('incident_reports', 'incident_reports_user_id_index');
        $this->dropIndexIfExists('incident_reports', 'incident_reports_status_index');
        $this->dropIndexIfExists('incident_reports', 'incident_reports_purok_id_index');
        $this->dropIndexIfExists('incident_reports', 'incident_reports_user_id_status_index');
        $this->dropIndexIfExists('incident_reports', 'incident_reports_created_at_index');

        $this->dropIndexIfExists('users', 'users_role_index');
        $this->dropIndexIfExists('users', 'users_purok_id_index');
        $this->dropIndexIfExists('users', 'users_is_approved_index');
        $this->dropIndexIfExists('users', 'users_role_purok_id_index');
    }
};