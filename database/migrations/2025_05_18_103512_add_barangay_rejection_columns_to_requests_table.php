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
            if (!Schema::hasColumn('requests', 'barangay_rejected_at')) {
                $table->timestamp('barangay_rejected_at')->nullable();
            }
            if (!Schema::hasColumn('requests', 'barangay_rejected_by')) {
                $table->foreignId('barangay_rejected_by')->nullable()->constrained('users')->nullOnDelete();
            }
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
