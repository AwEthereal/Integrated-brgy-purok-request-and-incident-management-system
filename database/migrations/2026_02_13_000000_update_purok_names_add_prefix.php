<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('puroks') || !Schema::hasColumn('puroks', 'name')) {
            return;
        }

        // Normalize the specific Capitol name first (handle both prefixed and unprefixed).
        DB::table('puroks')->where('name', 'Capitol Sentro')->update(['name' => 'Capitol Centro']);
        DB::table('puroks')->where('name', 'Purok Capitol Sentro')->update(['name' => 'Purok Capitol Centro']);

        // Add "Purok " prefix to names that don't have it yet.
        // Use a single SQL update for efficiency.
        DB::statement("UPDATE `puroks` SET `name` = CONCAT('Purok ', `name`) WHERE `name` NOT LIKE 'Purok %'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('puroks') || !Schema::hasColumn('puroks', 'name')) {
            return;
        }

        // Remove "Purok " prefix for rows that have it.
        DB::statement("UPDATE `puroks` SET `name` = SUBSTRING(`name`, 7) WHERE `name` LIKE 'Purok %'");

        // Best-effort revert for the specific Capitol name.
        DB::table('puroks')->where('name', 'Capitol Centro')->update(['name' => 'Capitol Sentro']);
        DB::table('puroks')->where('name', 'Purok Capitol Centro')->update(['name' => 'Purok Capitol Sentro']);
    }
};
