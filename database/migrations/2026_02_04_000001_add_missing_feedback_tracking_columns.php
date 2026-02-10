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
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'feedback_dismissed_at')) {
                $table->timestamp('feedback_dismissed_at')->nullable()->after('feedback_provided_at');
            }
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('incident_reports', 'feedback_submitted_at')) {
                $table->timestamp('feedback_submitted_at')->nullable()->after('feedback_requested_at');
            }
            if (!Schema::hasColumn('incident_reports', 'feedback_dismissed_at')) {
                $table->timestamp('feedback_dismissed_at')->nullable()->after('feedback_submitted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            if (Schema::hasColumn('requests', 'feedback_dismissed_at')) {
                $table->dropColumn('feedback_dismissed_at');
            }
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            if (Schema::hasColumn('incident_reports', 'feedback_dismissed_at')) {
                $table->dropColumn('feedback_dismissed_at');
            }
            if (Schema::hasColumn('incident_reports', 'feedback_submitted_at')) {
                $table->dropColumn('feedback_submitted_at');
            }
        });
    }
};
