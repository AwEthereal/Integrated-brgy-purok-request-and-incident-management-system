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
        // First, drop the foreign key constraint
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        // Then modify the column to be nullable
        Schema::table('feedback', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
        
        // Finally, re-add the foreign key constraint
        Schema::table('feedback', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop the foreign key constraint
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        // Then modify the column to be not nullable
        Schema::table('feedback', function (Blueprint $table) {
            // Ensure there are no NULL values before making the column NOT NULL
            \DB::table('feedback')
                ->whereNull('user_id')
                ->update(['user_id' => 1]); // Use a default user ID
                
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
        
        // Finally, re-add the foreign key constraint
        Schema::table('feedback', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
