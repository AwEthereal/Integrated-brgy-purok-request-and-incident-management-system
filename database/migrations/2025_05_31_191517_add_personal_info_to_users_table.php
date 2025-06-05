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
            $table->string('first_name')->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->after('middle_name');
            $table->date('birth_date')->nullable()->after('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'divorced'])->nullable()->after('gender');
            $table->string('occupation')->nullable()->after('civil_status');
            $table->string('address_line1')->after('occupation');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->default('Kalawag')->after('address_line2');
            $table->string('province')->default('South Cotabato')->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'birth_date',
                'gender',
                'civil_status',
                'occupation',
                'address_line1',
                'address_line2',
                'city',
                'province',
                'postal_code'
            ]);
        });
    }
};
