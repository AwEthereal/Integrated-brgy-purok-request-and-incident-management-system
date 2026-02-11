<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('feedback') || !Schema::hasColumn('feedback', 'user_id')) {
            return;
        }

        Schema::table('feedback', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('feedback') || !Schema::hasColumn('feedback', 'user_id')) {
            return;
        }

        DB::table('feedback')->whereNull('user_id')->delete();

        Schema::table('feedback', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};

