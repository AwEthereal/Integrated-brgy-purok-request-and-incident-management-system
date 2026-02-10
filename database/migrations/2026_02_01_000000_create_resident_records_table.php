<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resident_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purok_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('philsys_card_no')->nullable()->unique();

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();

            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('sex', 16)->nullable();
            $table->string('civil_status', 32)->nullable();
            $table->string('religion')->nullable();
            $table->string('citizenship')->nullable();

            $table->string('residence_address')->nullable();
            $table->string('occupation')->nullable();

            $table->string('contact_number', 64)->nullable();
            $table->string('email')->nullable();

            $table->string('highest_educ_attainment', 32)->nullable();
            $table->string('educ_specify')->nullable();
            $table->boolean('is_graduate')->default(false);
            $table->boolean('is_undergraduate')->default(false);

            $table->date('date_accomplished')->nullable();

            $table->string('left_thumbmark_path')->nullable();
            $table->string('right_thumbmark_path')->nullable();
            $table->string('signature_path')->nullable();

            $table->string('household_number')->nullable(); // Secretary-managed
            $table->unsignedBigInteger('attested_by_user_id')->nullable(); // Secretary-managed

            $table->string('status', 24)->default('active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purok_id')->references('id')->on('puroks')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('attested_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_records');
    }
};
