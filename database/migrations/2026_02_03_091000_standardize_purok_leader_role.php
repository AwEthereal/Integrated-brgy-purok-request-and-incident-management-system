<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // If role is an ENUM (older schema), convert it to VARCHAR so we can standardize role values safely.
        try {
            $col = DB::selectOne("SELECT DATA_TYPE as data_type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role' LIMIT 1");
            if ($col && strtolower((string) $col->data_type) === 'enum') {
                DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(50) NOT NULL");
            }
        } catch (\Throwable $e) {
            // If information_schema is unavailable or SQL differs, continue; the update below will still run.
        }

        // Convert legacy role label to standardized label.
        DB::table('users')->where('role', 'purok_president')->update(['role' => 'purok_leader']);

        // Rebuild leader uniqueness helper (generated column + unique index) to match standardized role.
        // This is a best-effort rebuild compatible with the existing migration.
        try {
            $hasLeaderFlag = Schema::hasColumn('users', 'leader_flag');

            $indexExists = DB::select("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'users' AND index_name = 'users_purok_leader_unique' LIMIT 1");
            if (!empty($indexExists)) {
                DB::statement("DROP INDEX `users_purok_leader_unique` ON `users`");
            }

            if ($hasLeaderFlag) {
                DB::statement("ALTER TABLE `users` DROP COLUMN `leader_flag`");
            }

            DB::statement("ALTER TABLE `users` ADD COLUMN `leader_flag` TINYINT GENERATED ALWAYS AS (CASE WHEN `role` = 'purok_leader' THEN 1 ELSE NULL END) STORED");
            DB::statement("CREATE UNIQUE INDEX `users_purok_leader_unique` ON `users` (`purok_id`, `leader_flag`)");
        } catch (\Throwable $e) {
            // Ignore if DB engine doesn't support generated columns / index ops.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // Best-effort revert label.
        DB::table('users')->where('role', 'purok_leader')->update(['role' => 'purok_president']);

        // Best-effort rebuild leader_flag expression back to legacy (allow both labels).
        try {
            $indexExists = DB::select("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'users' AND index_name = 'users_purok_leader_unique' LIMIT 1");
            if (!empty($indexExists)) {
                DB::statement("DROP INDEX `users_purok_leader_unique` ON `users`");
            }

            if (Schema::hasColumn('users', 'leader_flag')) {
                DB::statement("ALTER TABLE `users` DROP COLUMN `leader_flag`");
            }

            DB::statement("ALTER TABLE `users` ADD COLUMN `leader_flag` TINYINT GENERATED ALWAYS AS (CASE WHEN `role` IN ('purok_leader','purok_president') THEN 1 ELSE NULL END) STORED");
            DB::statement("CREATE UNIQUE INDEX `users_purok_leader_unique` ON `users` (`purok_id`, `leader_flag`)");
        } catch (\Throwable $e) {
        }
    }
};
