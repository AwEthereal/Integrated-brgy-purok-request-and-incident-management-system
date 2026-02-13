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
            ['name' => 'Tagumpay I'],
            ['name' => 'Tagumpay II'],
            ['name' => 'Tagumpay III'],
            ['name' => 'Purok Maunlad'],
            ['name' => 'Purok Pagkakaisa'],
            ['name' => 'Bagong Silang'],
            ['name' => 'Bagong Sikat'],
            ['name' => 'Capitol West'],
            ['name' => 'Capitol East'],
            ['name' => 'Capitol Sentro'],
            ['name' => 'PC Barracks'],
            ['name' => 'Masagana I'],
            ['name' => 'Masagana II'],
            ['name' => 'Masagana III'],
            ['name' => 'Mabuhay'],
            ['name' => 'Landerio'],
            ['name' => 'Pag-Asa I'],
            ['name' => 'Pag-Asa II'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('puroks');
    }
};
