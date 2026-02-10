<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Purok;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AnalyticsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marker = '[DEMO_ANALYTICS]';

        // Deterministic randomness during unit tests to make assertions stable
        if (app()->runningUnitTests()) {
            mt_srand(123456);
        }

        $clearanceTypes = [
            'barangay_clearance',
            'business_clearance',
            'certificate_of_residency',
            'certificate_of_indigency',
        ];
        $incidentTypes = array_keys(IncidentReport::TYPES);

        // Last 12 months including current
        $months = [];
        $now = now();
        for ($i = 11; $i >= 0; $i--) {
            $months[] = $now->copy()->subMonths($i)->startOfMonth();
        }

        DB::beginTransaction();
        try {
            // Reset previous demo data
            if (Schema::hasTable('requests')) {
                ServiceRequest::where('remarks', 'LIKE', "%$marker%")
                    ->delete();
            }
            if (Schema::hasTable('incident_reports')) {
                IncidentReport::where('description', 'LIKE', "%$marker%")
                    ->delete();
            }
            // Also remove demo puroks we created previously
            if (Schema::hasTable('puroks')) {
                Purok::where('name', 'LIKE', '%(DEMO)%')->delete();
            }

            // Use existing puroks if present; otherwise create a few demo puroks we can clean up later
            $puroks = Purok::orderBy('name')->get();
            if ($puroks->isEmpty()) {
                $names = ['Purok A (DEMO)', 'Purok B (DEMO)', 'Purok C (DEMO)', 'Purok D (DEMO)', 'Purok E (DEMO)'];
                foreach ($names as $n) {
                    $puroks->push(Purok::create(['name' => $n]));
                }
            }

            // Ensure a demo resident exists for foreign key requirements (respect current schema)
            $userDefaults = [
                'name' => 'Demo Resident',
                'password' => bcrypt('password'),
                'role' => 'resident',
            ];
            if (Schema::hasColumn('users', 'first_name')) {
                $userDefaults['first_name'] = 'Demo';
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $userDefaults['last_name'] = 'Resident';
            }
            if (Schema::hasColumn('users', 'address_line1')) {
                $userDefaults['address_line1'] = 'Demo Address';
            }
            if (Schema::hasColumn('users', 'username')) {
                $userDefaults['username'] = 'demo.resident';
            }
            if (Schema::hasColumn('users', 'gender')) {
                $userDefaults['gender'] = 'other';
            }
            if (Schema::hasColumn('users', 'civil_status')) {
                $userDefaults['civil_status'] = 'single';
            }
            $demoUser = User::firstOrCreate(
                ['email' => 'demo.resident@example.com'],
                $userDefaults
            );

            foreach ($puroks as $idx => $purok) {
                // Vary base counts by purok
                $clearanceBase = 5 + ($idx * 3); // increasing counts per purok
                $incidentBase = 3 + ($idx * 2);

                foreach ($months as $m) {
                    // Spread within the month
                    $daysInMonth = $m->daysInMonth;

                    // Create some clearances
                    $cCount = max(1, (int) round($clearanceBase * rand(80, 140) / 100));
                    for ($c = 0; $c < $cCount; $c++) {
                        $created = $m->copy()->addDays(rand(0, $daysInMonth - 1))->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                        $type = $clearanceTypes[array_rand($clearanceTypes)];
                        $req = ServiceRequest::create([
                            'form_type' => $type,
                            'status' => 'barangay_approved',
                            'user_id' => $demoUser->id,
                            'purok_id' => $purok->id,
                            'remarks' => $marker . ' Demo clearance entry',
                            'purpose' => 'Demo purpose',
                        ]);
                        $req->created_at = $created;
                        $req->updated_at = $created;
                        $req->save();
                    }

                    // Create some incidents
                    $iCount = max(1, (int) round($incidentBase * rand(80, 140) / 100));
                    for ($j = 0; $j < $iCount; $j++) {
                        $created = $m->copy()->addDays(rand(0, $daysInMonth - 1))->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                        $itype = $incidentTypes[array_rand($incidentTypes)];
                        $desc = $marker . ' Demo incident entry';
                        $report = IncidentReport::create([
                            'user_id' => $demoUser->id,
                            'purok_id' => $purok->id,
                            'reporter_name' => 'Demo Reporter',
                            'contact_number' => '09123456789',
                            'email' => null,
                            'incident_type' => $itype,
                            'incident_type_other' => $itype === 'other' ? 'Demo Other Type' : null,
                            'description' => $desc,
                            'status' => 'pending',
                            'location' => 'Demo Location',
                        ]);
                        $report->created_at = $created;
                        $report->updated_at = $created;
                        $report->save();
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
