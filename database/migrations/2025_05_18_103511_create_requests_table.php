<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('requests', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // resident making the request
        $table->string('form_type'); // e.g. Certificate of Indigency, Residency, etc.
        $table->string('purpose');
        $table->string('status')->default('pending'); // pending, approved_by_purok, sent_to_barangay, finalized
        $table->text('remarks')->nullable(); // for optional notes
        $table->timestamps();

        // Foreign key
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
