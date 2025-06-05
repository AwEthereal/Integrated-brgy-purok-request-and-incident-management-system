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
        Schema::table('requests', function (Blueprint $table) {
            $table->string('valid_id_front_path')->nullable()->after('form_type');
            $table->string('valid_id_back_path')->nullable()->after('valid_id_front_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('valid_id_front_path');
            $table->dropColumn('valid_id_back_path');
        });
    }
};
