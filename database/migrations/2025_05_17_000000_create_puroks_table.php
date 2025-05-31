<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('puroks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Optional: insert default puroks
        DB::table('puroks')->insert([
            ['name' => 'Purok 1'],
            ['name' => 'Purok 2'],
            ['name' => 'Purok 3'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('puroks');
    }
};
