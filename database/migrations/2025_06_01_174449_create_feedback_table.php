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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('incident_report_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('request_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('sqd0_rating')->comment('I am satisfied with the service that I availed.');
            $table->tinyInteger('sqd1_rating')->comment('I spent an acceptable amount of time for my transaction.');
            $table->tinyInteger('sqd2_rating')->comment('The office accurately informed me and followed the transaction\'s requirements and steps.');
            $table->tinyInteger('sqd3_rating')->comment('My online transaction (including steps and payment) was simple and convenient.');
            $table->tinyInteger('sqd4_rating')->comment('I easily found information about my transaction from the office or its website.');
            $table->tinyInteger('sqd5_rating')->comment('I paid an acceptable amount of fees for my transaction.');
            $table->tinyInteger('sqd6_rating')->comment('I am confident that my online transaction was secure.');
            $table->tinyInteger('sqd7_rating')->comment('The office\'s online support was available, or (if asked questions) was quick to respond.');
            $table->tinyInteger('sqd8_rating')->comment('I got what I needed from the government office.');
            $table->text('comments')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
