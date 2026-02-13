<?php

namespace Database\Seeders;

use App\Models\Request;
use App\Models\IncidentReport;
use App\Models\PurokChangeRequest;
use App\Models\User;
use App\Models\Purok;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting test data generation...');

        // Clear existing data
        $this->clearExistingData();

        // Get users for testing
        $residents = User::where('role', 'resident')->where('is_approved', true)->get();
        if ($residents->isEmpty()) {
            $this->command->warn('No approved residents found. Creating dummy approved residents...');
            $this->ensurePuroksExist();
            $this->createDummyResidents(25);
            $residents = User::where('role', 'resident')->where('is_approved', true)->get();
        }

        if ($residents->isEmpty()) {
            $this->command->error('No approved residents found! Unable to generate test data.');
            return;
        }

        $this->command->info("Found {$residents->count()} approved residents");

        // Create test requests
        $this->createTestRequests($residents);

        // Create test incident reports
        $this->createTestIncidents($residents);

        $this->command->newLine();
        $this->command->info('✓ Test data generation complete!');
        $this->showSummary();
    }

    private function ensurePuroksExist(): void
    {
        if (Purok::count() > 0) {
            return;
        }

        $this->command->warn('No puroks found. Inserting default puroks...');

        DB::table('puroks')->insert([
            ['name' => 'Purok Tagumpay I'],
            ['name' => 'Purok Tagumpay II'],
            ['name' => 'Purok Tagumpay III'],
            ['name' => 'Purok Maunlad'],
            ['name' => 'Purok Pagkakaisa'],
            ['name' => 'Purok Masagana'],
            ['name' => 'Purok Maligaya'],
            ['name' => 'Purok Malinis'],
            ['name' => 'Purok Magsasaka'],
            ['name' => 'Purok Mabuhay'],
            ['name' => 'Purok Malakas'],
            ['name' => 'Purok Malusog'],
            ['name' => 'Purok Matatag'],
            ['name' => 'Purok Masipag'],
            ['name' => 'Purok Masaya'],
            ['name' => 'Purok Mapayapa'],
            ['name' => 'Purok Capitol Centro'],
            ['name' => 'Purok Malinaw'],
            ['name' => 'Purok Masigla'],
            ['name' => 'Purok Matapat'],
            ['name' => 'Purok Malaya'],
            ['name' => 'Purok Malinis II'],
            ['name' => 'Purok Masinop'],
            ['name' => 'Purok Matibay'],
        ]);
    }

    private function createDummyResidents(int $count): void
    {
        $purokIds = Purok::pluck('id')->all();
        if (empty($purokIds)) {
            return;
        }

        for ($i = 1; $i <= $count; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $email = fake()->unique()->safeEmail();

            User::create([
                'name' => $firstName . ' ' . $lastName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'username' => 'resident' . strtolower((string) $i) . '_' . fake()->unique()->numerify('###'),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'resident',
                'is_approved' => true,
                'is_dummy' => true,
                'approved_at' => now(),
                'purok_id' => $purokIds[array_rand($purokIds)],
                'contact_number' => fake()->numerify('09#########'),
                'address' => fake()->address(),
            ]);
        }

        $this->command->info("  ✓ Created {$count} dummy approved residents (password: password)");
    }

    /**
     * Clear existing requests and incidents
     */
    private function clearExistingData(): void
    {
        $this->command->warn('Clearing existing test data...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Request::truncate();
        $this->command->info('  ✓ Requests cleared');
        
        IncidentReport::truncate();
        $this->command->info('  ✓ Incident reports cleared');
        
        if (class_exists(PurokChangeRequest::class)) {
            PurokChangeRequest::truncate();
            $this->command->info('  ✓ Purok change requests cleared');
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Create test requests with various statuses
     */
    private function createTestRequests($residents): void
    {
        $this->command->info('Creating test requests...');

        $targetCount = 80;

        $formTypes = array_keys(Request::FORM_TYPES);
        $statuses = ['pending', 'purok_approved', 'barangay_approved', 'rejected', 'completed'];
        
        $resident1 = $residents->first();
        $resident2 = $residents->skip(1)->first() ?? $resident1;
        $resident3 = $residents->skip(2)->first() ?? $resident1;

        // Create requests with different statuses and times for testing yellow dots
        $testRequests = [
            // PENDING - Should show yellow dot for Purok Leader
            [
                'user_id' => $resident1->id,
                'purok_id' => $resident1->purok_id,
                'form_type' => 'barangay_clearance',
                'purpose' => 'Testing - Pending Request (Purok Leader should see yellow dot)',
                'status' => 'pending',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            
            // PUROK APPROVED - Should show yellow dot for Barangay Official
            [
                'user_id' => $resident1->id,
                'purok_id' => $resident1->purok_id,
                'form_type' => 'business_permit',
                'purpose' => 'Testing - Purok Approved (Barangay should see yellow dot)',
                'status' => 'purok_approved',
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subMinutes(30),
                'purok_approved_by' => null,
                'purok_approved_at' => now()->subMinutes(30),
            ],
            
            // REJECTED - Should show yellow dot for Resident (action required)
            [
                'user_id' => $resident2->id,
                'purok_id' => $resident2->purok_id,
                'form_type' => 'barangay_clearance',
                'purpose' => 'Testing - Rejected Request (Resident should see yellow dot)',
                'status' => 'rejected',
                'rejection_reason' => 'Incomplete documents - Please resubmit with valid ID',
                'created_at' => now()->subHours(24),
                'updated_at' => now()->subHours(1),
            ],
            
            // COMPLETED - Should show yellow dot for Resident (action required - pickup)
            [
                'user_id' => $resident2->id,
                'purok_id' => $resident2->purok_id,
                'form_type' => 'certificate_of_residency',
                'purpose' => 'Testing - Completed (Resident should see yellow dot for pickup)',
                'status' => 'completed',
                'created_at' => now()->subHours(48),
                'updated_at' => now()->subHours(2),
                'barangay_approved_by' => null,
                'barangay_approved_at' => now()->subHours(2),
            ],
            
            // OLD PUROK APPROVED - Should NOT show yellow dot (informational, >2h)
            [
                'user_id' => $resident3->id,
                'purok_id' => $resident3->purok_id,
                'form_type' => 'barangay_id',
                'purpose' => 'Testing - Old Approval (Should NOT show dots)',
                'status' => 'purok_approved',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
                'purok_approved_at' => now()->subDays(1),
            ],
            
            // BARANGAY APPROVED - Recent, should show brief dot
            [
                'user_id' => $resident3->id,
                'purok_id' => $resident3->purok_id,
                'form_type' => 'barangay_clearance',
                'purpose' => 'Testing - Barangay Approved (Brief dot for resident)',
                'status' => 'barangay_approved',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subMinutes(90),
                'barangay_approved_at' => now()->subMinutes(90),
            ],
        ];

        foreach ($testRequests as $requestData) {
            Request::create($requestData);
        }

        $remaining = $targetCount - count($testRequests);
        if ($remaining > 0) {
            for ($i = 0; $i < $remaining; $i++) {
                $resident = $residents->random();
                $status = $statuses[array_rand($statuses)];
                $formType = $formTypes[array_rand($formTypes)];

                $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                $updatedAt = (clone $createdAt)->addMinutes(rand(0, 60 * 24));

                $data = [
                    'user_id' => $resident->id,
                    'purok_id' => $resident->purok_id,
                    'form_type' => $formType,
                    'purpose' => 'Test Data - ' . fake()->sentence(6),
                    'status' => $status,
                    'requester_name' => $resident->full_name ?? $resident->name,
                    'email' => $resident->email,
                    'contact_number' => $resident->contact_number ?? fake()->numerify('09#########'),
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                if ($status === 'rejected') {
                    $data['rejection_reason'] = 'Test Data - ' . fake()->sentence(8);
                    $data['rejected_at'] = $updatedAt;
                }

                if ($status === 'purok_approved') {
                    $data['purok_approved_at'] = $updatedAt;
                    $data['purok_approved_by'] = null;
                }

                if ($status === 'barangay_approved') {
                    $data['barangay_approved_at'] = $updatedAt;
                    $data['barangay_approved_by'] = null;
                }

                if ($status === 'completed') {
                    $data['document_generated_at'] = $updatedAt;
                }

                Request::create($data);
            }
        }

        $this->command->info("  ✓ Created " . $targetCount . " test requests");
    }

    /**
     * Create test incident reports
     */
    private function createTestIncidents($residents): void
    {
        $this->command->info('Creating test incident reports...');

        $targetCount = 80;

        $incidentTypes = array_keys(IncidentReport::TYPES);
        $resident1 = $residents->first();
        $resident2 = $residents->skip(1)->first() ?? $resident1;

        $testIncidents = [
            // PENDING - Should show yellow dot for Barangay Officials
            [
                'user_id' => $resident1->id,
                'purok_id' => $resident1->purok_id,
                'incident_type' => 'public_disturbance',
                'description' => 'Testing - Pending Incident (Barangay should see yellow dot)',
                'location' => 'Test Street, Block 1',
                'status' => 'pending',
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            
            // IN PROGRESS - Should show brief dot for Resident
            [
                'user_id' => $resident1->id,
                'purok_id' => $resident1->purok_id,
                'incident_type' => 'environmental_hazard',
                'description' => 'Testing - In Progress (Resident should see brief dot)',
                'location' => 'Main Road',
                'status' => 'in_progress',
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subMinutes(30),
            ],
            
            // RESOLVED - Should show brief dot for Resident
            [
                'user_id' => $resident2->id,
                'purok_id' => $resident2->purok_id,
                'incident_type' => 'traffic_incident',
                'description' => 'Testing - Resolved (Resident should see brief dot)',
                'location' => 'Corner Street',
                'status' => 'resolved',
                'staff_notes' => 'Issue has been resolved by our team',
                'created_at' => now()->subHours(10),
                'updated_at' => now()->subHour(),
            ],
            
            // OLD RESOLVED - Should NOT show dots
            [
                'user_id' => $resident2->id,
                'purok_id' => $resident2->purok_id,
                'incident_type' => 'fire',
                'description' => 'Testing - Old Resolved (Should NOT show dots)',
                'location' => 'Test Avenue',
                'status' => 'resolved',
                'staff_notes' => 'Fixed last week',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($testIncidents as $incidentData) {
            IncidentReport::create($incidentData);
        }

        $remaining = $targetCount - count($testIncidents);
        if ($remaining > 0) {
            $statuses = ['pending', 'in_progress', 'resolved'];
            for ($i = 0; $i < $remaining; $i++) {
                $resident = $residents->random();
                $status = $statuses[array_rand($statuses)];
                $incidentType = $incidentTypes[array_rand($incidentTypes)];

                $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                $updatedAt = (clone $createdAt)->addMinutes(rand(0, 60 * 24));

                $data = [
                    'user_id' => $resident->id,
                    'purok_id' => $resident->purok_id,
                    'incident_type' => $incidentType,
                    'description' => 'Test Data - ' . fake()->paragraph(2),
                    'location' => fake()->streetAddress(),
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                if ($status === 'resolved') {
                    $data['staff_notes'] = 'Test Data - ' . fake()->sentence(10);
                }

                IncidentReport::create($data);
            }
        }

        $this->command->info("  ✓ Created " . $targetCount . " test incidents");
    }

    /**
     * Show summary of created data
     */
    private function showSummary(): void
    {
        $this->command->newLine();
        $this->command->info('=== Test Data Summary ===');
        
        $requests = Request::count();
        $incidents = IncidentReport::count();
        $users = User::count();
        
        $this->command->table(
            ['Type', 'Count'],
            [
                ['Requests', $requests],
                ['Incidents', $incidents],
                ['Users (kept)', $users],
            ]
        );

        $this->command->newLine();
        $this->command->info('=== Status Breakdown ===');
        
        // Requests by status
        $requestStatuses = Request::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [$item->status, $item->count])
            ->toArray();
        
        if (!empty($requestStatuses)) {
            $this->command->info('Requests:');
            $this->command->table(['Status', 'Count'], $requestStatuses);
        }

        // Incidents by status
        $incidentStatuses = IncidentReport::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [$item->status, $item->count])
            ->toArray();
        
        if (!empty($incidentStatuses)) {
            $this->command->info('Incidents:');
            $this->command->table(['Status', 'Count'], $incidentStatuses);
        }

        $this->command->newLine();
        $this->command->info('🟡 Yellow Dot Test Cases Created:');
        $this->command->line('  • Purok Leader: Should see dot on pending request');
        $this->command->line('  • Barangay Official: Should see dots on purok_approved & pending incidents');
        $this->command->line('  • Resident: Should see dots on rejected & completed (action required)');
        $this->command->line('  • Resident: Should see brief dots on recent approvals (informational)');
    }
}
