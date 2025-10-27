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
        Schema::table('users', function (Blueprint $table) {
            // Check and add columns if they don't exist
            if (!Schema::hasColumn('users', 'suffix')) {
                $table->string('suffix', 10)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('contact_number');
            }
            if (!Schema::hasColumn('users', 'place_of_birth')) {
                $table->string('place_of_birth')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'sex')) {
                $table->enum('sex', ['male', 'female'])->nullable()->after('place_of_birth');
            }
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable()->after('civil_status');
            }
            if (!Schema::hasColumn('users', 'house_number')) {
                $table->string('house_number', 50)->nullable()->after('occupation');
            }
            if (!Schema::hasColumn('users', 'street')) {
                $table->string('street')->nullable()->after('house_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'suffix',
                'date_of_birth',
                'place_of_birth',
                'sex',
                'nationality',
                'house_number',
                'street'
            ]);
        });
    }
};
