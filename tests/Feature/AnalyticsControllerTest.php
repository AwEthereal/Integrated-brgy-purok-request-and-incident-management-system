<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Purok;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use Carbon\Carbon;

class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ?User $admin = null;

    protected function setUp(): void
    {
        parent::setUp();
        // Act as an admin user to access analytics endpoints
        $user = User::create([
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'address_line1' => 'Test Address',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);
        $this->admin = $user;
        $this->actingAs($user);

        // Freeze time for deterministic labels
        Carbon::setTestNow(Carbon::create(2026, 2, 2, 12, 0, 0));
    }

    public function test_clearances_per_purok_grouping_counts(): void
    {
        $p1 = Purok::create(['name' => 'Alpha']);
        $p2 = Purok::create(['name' => 'Beta']);
        // 5 for Alpha, 2 for Beta in 2026
        for ($i = 0; $i < 5; $i++) {
            $r = ServiceRequest::create([
                'form_type' => 'barangay_clearance',
                'status' => 'barangay_approved',
                'purok_id' => $p1->id,
                'purpose' => 'Test',
                'user_id' => $this->admin->id,
            ]);
            $r->created_at = Carbon::create(2026, 1, 10 + $i);
            $r->save();
        }
        for ($i = 0; $i < 2; $i++) {
            $r = ServiceRequest::create([
                'form_type' => 'business_clearance',
                'status' => 'barangay_approved',
                'purok_id' => $p2->id,
                'purpose' => 'Test',
                'user_id' => $this->admin->id,
            ]);
            $r->created_at = Carbon::create(2026, 1, 20 + $i);
            $r->save();
        }

        $resp = $this->getJson('/analytics/clearances?group=per_purok&year=2026');
        $resp->assertOk();
        $json = $resp->json();
        $this->assertArrayHasKey('labels', $json);
        $this->assertArrayHasKey('datasets', $json);
        $labels = $json['labels'];
        $data = $json['datasets'][0]['data'];
        $map = array_combine($labels, $data);
        $this->assertEquals(5, $map['Alpha'] ?? 0);
        $this->assertEquals(2, $map['Beta'] ?? 0);
    }

    public function test_clearances_total_monthly_time_series(): void
    {
        $p = Purok::create(['name' => 'Gamma']);
        // Dec 2025: 1; Jan 2026: 3; Feb 2026: 2
        $dates = [
            Carbon::create(2025, 12, 15),
            Carbon::create(2026, 1, 5),
            Carbon::create(2026, 1, 10),
            Carbon::create(2026, 1, 20),
            Carbon::create(2026, 2, 1),
            Carbon::create(2026, 2, 2),
        ];
        foreach ($dates as $d) {
            $r = ServiceRequest::create([
                'form_type' => 'certificate_of_residency',
                'status' => 'barangay_approved',
                'purok_id' => $p->id,
                'purpose' => 'Test',
                'user_id' => $this->admin->id,
            ]);
            $r->created_at = $d;
            $r->save();
        }
        $resp = $this->getJson('/analytics/clearances?period=monthly&group=total');
        $resp->assertOk();
        $json = $resp->json();
        $labels = $json['labels'];
        $data = $json['datasets'][0]['data'];
        // Expect last label to be Feb 2026
        $this->assertEquals('Feb 2026', end($labels));
        $this->assertEquals(6, array_sum($data));
        // Check last two months counts
        $lastIndex = count($labels) - 1;
        $this->assertEquals(2, $data[$lastIndex]); // Feb 2026
        $this->assertEquals(3, $data[$lastIndex - 1]); // Jan 2026
    }

    public function test_incidents_per_type_grouping_counts(): void
    {
        $p = Purok::create(['name' => 'Delta']);
        // 4 fires and 1 other in 2026
        for ($i = 0; $i < 4; $i++) {
            $rep = IncidentReport::create([
                'user_id' => $this->admin->id,
                'purok_id' => $p->id,
                'reporter_name' => 'R',
                'contact_number' => '09111111111',
                'incident_type' => 'fire',
                'description' => 'd',
                'status' => 'pending',
            ]);
            $rep->created_at = Carbon::create(2026, 1, 5 + $i);
            $rep->save();
        }
        $o = IncidentReport::create([
            'user_id' => $this->admin->id,
            'purok_id' => $p->id,
            'reporter_name' => 'R',
            'contact_number' => '09111111111',
            'incident_type' => 'other',
            'incident_type_other' => 'Strange',
            'description' => 'd',
            'status' => 'pending',
        ]);
        $o->created_at = Carbon::create(2026, 1, 25);
        $o->save();

        $resp = $this->getJson('/analytics/incidents?group=per_type&year=2026');
        $resp->assertOk();
        $json = $resp->json();
        $labels = $json['labels'];
        $data = $json['datasets'][0]['data'];
        $map = array_combine($labels, $data);
        $this->assertEquals(4, $map['Fire'] ?? 0);
        $this->assertEquals(1, $map['Other'] ?? 0);
    }

    public function test_incidents_total_monthly_time_series(): void
    {
        $p = Purok::create(['name' => 'Epsilon']);
        // Jan 2026: 1; Feb 2026: 2
        $dates = [
            Carbon::create(2026, 1, 5),
            Carbon::create(2026, 2, 1),
            Carbon::create(2026, 2, 2),
        ];
        foreach ($dates as $d) {
            $rep = IncidentReport::create([
                'user_id' => $this->admin->id,
                'purok_id' => $p->id,
                'reporter_name' => 'R',
                'contact_number' => '09111111111',
                'incident_type' => 'fire',
                'description' => 'd',
                'status' => 'pending',
            ]);
            $rep->created_at = $d;
            $rep->save();
        }
        $resp = $this->getJson('/analytics/incidents?period=monthly&group=total');
        $resp->assertOk();
        $json = $resp->json();
        $labels = $json['labels'];
        $data = $json['datasets'][0]['data'];
        $this->assertEquals('Feb 2026', end($labels));
        $this->assertEquals(3, array_sum($data));
        $lastIndex = count($labels) - 1;
        $this->assertEquals(2, $data[$lastIndex]); // Feb 2026
        $this->assertEquals(1, $data[$lastIndex - 1]); // Jan 2026
    }
}
