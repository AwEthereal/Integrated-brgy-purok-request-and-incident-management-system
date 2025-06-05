<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            // Update status field if it exists, otherwise create it
            if (!Schema::hasColumn('requests', 'status')) {
                $table->enum('status', [
                    'pending',
                    'purok_approved',
                    'barangay_approved',
                    'completed',
                    'rejected'
                ])->default('pending');
            } else {
                // Update the existing status column if needed
                $table->enum('status', [
                    'pending',
                    'purok_approved',
                    'barangay_approved',
                    'completed',
                    'rejected'
                ])->default('pending')->change();
            }

            // Add purok_id if it doesn't exist
            if (!Schema::hasColumn('requests', 'purok_id')) {
                $table->foreignId('purok_id')->nullable()->constrained()->onDelete('set null');
            }

            // Add other fields if they don't exist
            $columnsToAdd = [
                'purok_approved_at' => 'timestamp',
                'purok_approved_by' => 'foreignId',
                'purok_notes' => 'text',
                'barangay_approved_at' => 'timestamp',
                'barangay_approved_by' => 'foreignId',
                'barangay_notes' => 'text',
                'document_path' => 'string',
                'document_generated_at' => 'timestamp'
            ];

            foreach ($columnsToAdd as $column => $type) {
                if (!Schema::hasColumn('requests', $column)) {
                    if ($type === 'foreignId') {
                        $table->foreignId($column)->nullable()->constrained('users')->onDelete('set null');
                    } else {
                        $table->$type($column)->nullable();
                    }
                }
            }
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['purok_approved_by']);
            $table->dropForeign(['barangay_approved_by']);
            $table->dropForeign(['purok_id']);
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'purok_id',
                'purok_approved_at',
                'purok_approved_by',
                'purok_notes',
                'barangay_approved_at',
                'barangay_approved_by',
                'barangay_notes',
                'document_path',
                'document_generated_at',
            ]);
        });
    }
};
