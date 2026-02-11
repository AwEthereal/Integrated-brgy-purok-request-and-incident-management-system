<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropForeignKeysForColumnIfExists(string $table, string $column): void
    {
        try {
            $database = DB::getDatabaseName();
            if (!is_string($database) || $database === '') {
                return;
            }

            $rows = DB::select(
                'SELECT CONSTRAINT_NAME AS name
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$database, $table, $column]
            );

            foreach ($rows as $row) {
                $name = $row->name ?? null;
                if (is_string($name) && $name !== '') {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
                }
            }
        } catch (\Throwable $e) {
        }
    }

    public function up(): void
    {
        $this->dropForeignKeysForColumnIfExists('requests', 'user_id');
        $this->dropForeignKeysForColumnIfExists('incident_reports', 'user_id');

        // Requests table
        Schema::table('requests', function (Blueprint $table) {
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add new column if missing
            if (!Schema::hasColumn('requests', 'requester_name')) {
                $table->string('requester_name')->nullable()->after('email');
            }

            // Recreate FK
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Incident Reports table
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();

            if (!Schema::hasColumn('incident_reports', 'reporter_name')) {
                $table->string('reporter_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('incident_reports', 'contact_number')) {
                $table->string('contact_number', 32)->nullable()->after('reporter_name');
            }
            if (!Schema::hasColumn('incident_reports', 'email')) {
                $table->string('email')->nullable()->after('contact_number');
            }

            // Recreate FK
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $this->dropForeignKeysForColumnIfExists('requests', 'user_id');
        $this->dropForeignKeysForColumnIfExists('incident_reports', 'user_id');

        // If public submissions exist (user_id is nullable), rollback cannot make user_id NOT NULL.
        // Since this project can refresh without preserving data, remove those rows first.
        try {
            DB::table('requests')->whereNull('user_id')->delete();
        } catch (\Throwable $e) {
        }
        try {
            DB::table('incident_reports')->whereNull('user_id')->delete();
        } catch (\Throwable $e) {
        }

        Schema::table('requests', function (Blueprint $table) {
            if (Schema::hasColumn('requests', 'requester_name')) {
                $table->dropColumn('requester_name');
            }
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            foreach (['email', 'contact_number', 'reporter_name'] as $col) {
                if (Schema::hasColumn('incident_reports', $col)) {
                    $table->dropColumn($col);
                }
            }
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
