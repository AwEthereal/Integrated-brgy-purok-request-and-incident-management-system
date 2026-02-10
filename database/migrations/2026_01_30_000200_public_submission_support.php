<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Requests: make user_id nullable and add requester_name
        if (Schema::hasTable('requests')) {
            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'user_id')) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (Throwable $e) {
                        // ignore if not exists
                    }
                }
            });

            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'user_id')) {
                    try {
                        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                            $table->unsignedBigInteger('user_id')->nullable()->change();
                        }
                    } catch (Throwable $e) {
                        // Skip on drivers that do not support change() without DBAL
                    }
                }
            });

            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'user_id')) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('requests', 'requester_name')) {
                    $table->string('requester_name')->nullable()->after('email');
                }
            });
        }

        // Incident Reports: make user_id nullable and add reporter/contact/email
        if (Schema::hasTable('incident_reports')) {
            Schema::table('incident_reports', function (Blueprint $table) {
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (Throwable $e) {
                        // ignore if not exists
                    }
                }
            });

            Schema::table('incident_reports', function (Blueprint $table) {
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    try {
                        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                            $table->unsignedBigInteger('user_id')->nullable()->change();
                        }
                    } catch (Throwable $e) {
                        // Skip on drivers that do not support change() without DBAL
                    }
                }
            });

            Schema::table('incident_reports', function (Blueprint $table) {
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('incident_reports', 'reporter_name')) {
                    $table->string('reporter_name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('incident_reports', 'contact_number')) {
                    $table->string('contact_number', 32)->nullable()->after('reporter_name');
                }
                if (!Schema::hasColumn('incident_reports', 'email')) {
                    $table->string('email')->nullable()->after('contact_number');
                }
            });
        }
    }

    public function down(): void
    {
        // Reverse additional columns; attempt to restore NOT NULL (may fail if data contains nulls)
        if (Schema::hasTable('requests')) {
            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'requester_name')) {
                    $table->dropColumn('requester_name');
                }
                if (Schema::hasColumn('requests', 'user_id')) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (Throwable $e) {}
                }
            });
            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'user_id')) {
                    try { $table->dropForeign(['user_id']); } catch (Throwable $e) {}
                }
            });
            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'user_id')) {
                    try { $table->unsignedBigInteger('user_id')->nullable(false)->change(); } catch (Throwable $e) {}
                }
            });
            Schema::table('requests', function (Blueprint $table) {
                if (Schema::hasColumn('requests', 'user_id')) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }

        if (Schema::hasTable('incident_reports')) {
            Schema::table('incident_reports', function (Blueprint $table) {
                foreach (['email','contact_number','reporter_name'] as $col) {
                    if (Schema::hasColumn('incident_reports', $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (Throwable $e) {}
                }
            });
            Schema::table('incident_reports', function (Blueprint $table) {
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    try { $table->dropForeign(['user_id']); } catch (Throwable $e) {}
                }
            });
            Schema::table('incident_reports', function (Blueprint $table) {
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    try { $table->unsignedBigInteger('user_id')->nullable(false)->change(); } catch (Throwable $e) {}
                }
            });
            Schema::table('incident_reports', function (Blueprint $table) {
                if (Schema::hasColumn('incident_reports', 'user_id')) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }
};
