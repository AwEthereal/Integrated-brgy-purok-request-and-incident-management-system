<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add a generated column that is 1 for purok leaders, NULL otherwise (so UNIQUE ignores non-leaders)
        if (!Schema::hasColumn('users', 'leader_flag')) {
            DB::statement("ALTER TABLE `users` ADD COLUMN `leader_flag` TINYINT GENERATED ALWAYS AS (CASE WHEN `role` = 'purok_leader' THEN 1 ELSE NULL END) STORED");
        }

        // Create a unique index so only one leader (flag=1) per purok_id is allowed
        $exists = DB::select("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'users' AND index_name = 'users_purok_leader_unique' LIMIT 1");
        if (empty($exists)) {
            DB::statement("CREATE UNIQUE INDEX `users_purok_leader_unique` ON `users` (`purok_id`, `leader_flag`)");
        }
    }

    public function down(): void
    {
        // Drop unique index if exists
        $exists = DB::select("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'users' AND index_name = 'users_purok_leader_unique' LIMIT 1");
        if (!empty($exists)) {
            DB::statement("DROP INDEX `users_purok_leader_unique` ON `users`");
        }

        // Drop generated column if exists
        if (Schema::hasColumn('users', 'leader_flag')) {
            DB::statement("ALTER TABLE `users` DROP COLUMN `leader_flag`");
        }
    }
};
