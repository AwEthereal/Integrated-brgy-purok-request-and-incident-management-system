<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Request as ClearanceRequest;
use App\Models\IncidentReport;

class LightSampleSeeder extends Seeder
{
    public function run(): void
    {
        // Create a few demo residents if not present
        $residentIds = User::where('role', 'resident')->pluck('id')->all();
        if (count($residentIds) < 5) {
            for ($i = 1; $i <= 5; $i++) {
                $u = new User();
                $u->username = 'resident_demo_' . Str::random(6);
                $u->password = Hash::make('password');
                $u->role = 'resident';
                $u->is_approved = 1;
                // Best-effort set optional columns to satisfy NOT NULL constraints if present
                if (Schema::hasColumn('users', 'first_name')) $u->first_name = 'Resident';
                if (Schema::hasColumn('users', 'last_name')) $u->last_name = 'Demo';
                if (Schema::hasColumn('users', 'name')) $u->name = 'Resident Demo';
                if (Schema::hasColumn('users', 'email')) $u->email = $u->username . '@example.test';
                if (Schema::hasColumn('users', 'address_line1')) $u->address_line1 = 'Sample Address';
                $u->save();
                $residentIds[] = $u->id;
            }
        }
        if (empty($residentIds)) {
            // Fallback to any user
            $residentIds = User::pluck('id')->all();
            if (empty($residentIds)) return; // nothing to do
        }

        // Create 120 clearance requests
        $formTypes = array_keys(ClearanceRequest::FORM_TYPES);
        $requestStatuses = ['pending','purok_approved','barangay_approved','completed','rejected'];
        for ($i = 1; $i <= 120; $i++) {
            $created = Carbon::now()->subDays(rand(0, 150))->subMinutes(rand(0, 1440));
            $data = [
                'user_id' => $residentIds[array_rand($residentIds)],
                'form_type' => $formTypes[array_rand($formTypes)],
                'purpose' => 'Sample purpose #' . $i,
                'status' => $requestStatuses[array_rand($requestStatuses)],
                'remarks' => null,
                'created_at' => $created,
                'updated_at' => $created->copy()->addDays(rand(0, 10)),
            ];
            // Only set columns that exist to avoid SQL errors
            $insert = [];
            foreach ($data as $col => $val) {
                if ($col === 'created_at' || $col === 'updated_at' || Schema::hasColumn('requests', $col)) {
                    $insert[$col] = $val;
                }
            }
            DB::table('requests')->insert($insert);
        }

        // Create 120 incident reports
        $incidentStatuses = [
            IncidentReport::STATUS_PENDING,
            IncidentReport::STATUS_IN_PROGRESS,
            IncidentReport::STATUS_RESOLVED,
        ];
        for ($i = 1; $i <= 120; $i++) {
            $created = Carbon::now()->subDays(rand(0, 150))->subMinutes(rand(0, 1440));
            $data = [
                'user_id' => $residentIds[array_rand($residentIds)],
                'description' => 'Sample incident description #' . $i,
                'status' => $incidentStatuses[array_rand($incidentStatuses)],
                'location' => 'Kalawag Dos',
                'created_at' => $created,
                'updated_at' => $created->copy()->addDays(rand(0, 10)),
            ];
            $insert = [];
            foreach ($data as $col => $val) {
                if ($col === 'created_at' || $col === 'updated_at' || Schema::hasColumn('incident_reports', $col)) {
                    $insert[$col] = $val;
                }
            }
            DB::table('incident_reports')->insert($insert);
        }
    }
}
