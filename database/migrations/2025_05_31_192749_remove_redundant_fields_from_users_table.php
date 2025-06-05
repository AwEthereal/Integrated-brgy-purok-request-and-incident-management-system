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
            // Remove redundant address fields
            $table->dropColumn([
                'address_line1',
                'address_line2',
                'city',
                'province',
                'postal_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the columns if needed
            $table->string('address_line1')->after('purok_id');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->after('address_line2');
            $table->string('province')->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
        });
    }
};
