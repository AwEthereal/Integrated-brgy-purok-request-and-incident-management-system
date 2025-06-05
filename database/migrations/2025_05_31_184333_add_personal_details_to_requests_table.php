<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('user_id');
            $table->string('email')->nullable()->after('contact_number');
            $table->date('birth_date')->nullable()->after('email');
            $table->string('gender')->nullable()->after('birth_date');
            $table->string('civil_status')->nullable()->after('gender');
            $table->string('occupation')->nullable()->after('civil_status');
            $table->string('address')->nullable()->after('occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $columnsToDrop = [
                'contact_number',
                'email',
                'birth_date',
                'gender',
                'civil_status',
                'occupation',
                'address_line1',
                'address_line2',
                'city',
                'province',
                'postal_code',
                'address'  // Include the new address column
            ];

            // Only drop columns that exist
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
