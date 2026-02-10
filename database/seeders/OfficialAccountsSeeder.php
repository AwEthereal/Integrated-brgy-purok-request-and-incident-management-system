<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Purok;

class OfficialAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'id' => 12,
                'username' => '100001',
                'role' => 'admin',
                'name' => 'Admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin100001@example.com',
                'purok_name' => null,
            ],
            [
                'id' => 20,
                'username' => '200001',
                'role' => 'secretary',
                'name' => 'Secretary',
                'first_name' => 'Secretary',
                'last_name' => 'User',
                'email' => 'secretary200001@example.com',
                'purok_name' => null,
            ],
            [
                'id' => 17,
                'username' => '110001',
                'role' => 'barangay_captain',
                'name' => 'Barangay Captain',
                'first_name' => 'Barangay',
                'last_name' => 'Captain',
                'email' => 'captain110001@example.com',
                'purok_name' => null,
            ],
            [
                'id' => 22,
                'username' => '300001',
                'role' => 'purok_leader',
                'name' => 'Purok Leader - Tagumpay I',
                'first_name' => 'Tagumpay',
                'last_name' => 'Leader',
                'email' => 'leader300001@example.com',
                'purok_name' => 'Tagumpay I',
            ],
            [
                'id' => 26,
                'username' => '300002',
                'role' => 'purok_leader',
                'name' => 'Purok Leader - Purok Pagkakaisa',
                'first_name' => 'Pagkakaisa',
                'last_name' => 'Leader',
                'email' => 'leader300002@example.com',
                'purok_name' => 'Purok Pagkakaisa',
            ],
            [
                'id' => 36,
                'username' => '300003',
                'role' => 'purok_leader',
                'name' => 'Purok Leader - Mabuhay',
                'first_name' => 'Mabuhay',
                'last_name' => 'Leader',
                'email' => 'leader300003@example.com',
                'purok_name' => 'Mabuhay',
            ],
        ];

        foreach ($accounts as $acc) {
            $values = [
                'name' => $acc['name'],
                'email' => $acc['email'],
                'password' => bcrypt('edlau0318'),
                // Map to allowed enum values
                'role' => (function ($role) {
                    $allowed = [
                        'resident', 'purok_leader', 'sk_chairman', 'barangay_kagawad', 'secretary', 'barangay_captain', 'admin'
                    ];
                    if (in_array($role, $allowed, true)) return $role;
                    return 'resident';
                })($acc['role']),
                'is_approved' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ];

            if (Schema::hasColumn('users', 'first_name')) {
                $values['first_name'] = $acc['first_name'];
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $values['last_name'] = $acc['last_name'];
            }
            if (Schema::hasColumn('users', 'address_line1')) {
                $values['address_line1'] = 'Official Address';
            }
            if (Schema::hasColumn('users', 'username')) {
                $values['username'] = $acc['username'];
            }

            // Attach to purok if specified
            if (!empty($acc['purok_name']) && Schema::hasTable('puroks')) {
                $purok = Purok::firstOrCreate(['name' => $acc['purok_name']], ['description' => null]);
                if (Schema::hasColumn('users', 'purok_id')) {
                    $values['purok_id'] = $purok->id;
                }
            }

            // Upsert by fixed ID
            DB::table('users')->updateOrInsert(['id' => $acc['id']], $values);
        }
    }
}
