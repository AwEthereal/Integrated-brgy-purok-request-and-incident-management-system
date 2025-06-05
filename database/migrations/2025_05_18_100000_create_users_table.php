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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number')->nullable();
            $table->string('address', 1000)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['resident', 'purok_president', 'sk_chairman', 'barangay_kagawad', 'secretary', 'barangay_captain']);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('purok_id')->nullable()->constrained()->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
