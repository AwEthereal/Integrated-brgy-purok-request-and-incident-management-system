<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resident_records', function (Blueprint $table) {
            $table->string('region')->nullable()->after('residence_address');
            $table->string('province')->nullable()->after('region');
            $table->string('city_municipality')->nullable()->after('province');
            $table->string('barangay')->nullable()->after('city_municipality');
        });
    }

    public function down(): void
    {
        Schema::table('resident_records', function (Blueprint $table) {
            $table->dropColumn(['region', 'province', 'city_municipality', 'barangay']);
        });
    }
};
