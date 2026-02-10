<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resident_records', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('attested_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('resident_records', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
