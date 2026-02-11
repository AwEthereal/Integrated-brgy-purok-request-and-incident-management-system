<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class BarangayCaptainSeeder extends Seeder
{
    public function run(): void
    {
        $username = '110001';

        $values = [
            'name' => 'Barangay Captain',
            'email' => 'captain@example.com',
            'password' => Hash::make('edlau0318'),
            'role' => 'barangay_captain',
            'is_approved' => true,
            'updated_at' => now(),
            'created_at' => now(),
        ];

        if (Schema::hasColumn('users', 'username')) {
            $values['username'] = $username;
        }

        if (Schema::hasColumn('users', 'first_name')) {
            $values['first_name'] = 'Barangay';
        }

        if (Schema::hasColumn('users', 'last_name')) {
            $values['last_name'] = 'Captain';
        }

        DB::table('users')->updateOrInsert(
            ['username' => $username],
            $values
        );
    }
}
