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
        Schema::table('requests', function (Blueprint $table) {
            $table->timestamp('barangay_rejected_at')->nullable()->after('barangay_approved_by');
            $table->foreignId('barangay_rejected_by')->nullable()->after('barangay_rejected_at')
                  ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['barangay_rejected_by']);
            $table->dropColumn(['barangay_rejected_at', 'barangay_rejected_by']);
        });
    }
};
