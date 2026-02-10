<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Purok;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting dummy users creation...');
        
        // Get all puroks
        $puroks = Purok::all();
        
        if ($puroks->isEmpty()) {
            $this->command->error('❌ No puroks found! Please create puroks first.');
            return;
        }
        
        $this->command->info("📍 Found {$puroks->count()} puroks");
        
        // 1. Create Barangay Officials (one for each role)
        $this->createBarangayOfficials();
        
        // 2. Create Purok Presidents (one per purok)
        $this->createPurokPresidents($puroks);
        
        // 3. Create 500 Residents distributed across puroks
        $this->createResidents($puroks, 500);
        
        $this->command->info('✅ Dummy users creation completed!');
        $this->displaySummary();
    }
    
    /**
     * Create barangay officials for each role
     */
    private function createBarangayOfficials(): void
    {
        $this->command->info('👔 Creating Barangay Officials...');
        
        $officials = [
            [
                'role' => 'barangay_captain',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'captain.dummy@test.com',
            ],
            [
                'role' => 'barangay_kagawad',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'kagawad1.dummy@test.com',
            ],
            [
                'role' => 'barangay_kagawad',
                'first_name' => 'Pedro',
                'last_name' => 'Reyes',
                'email' => 'kagawad2.dummy@test.com',
            ],
            [
                'role' => 'secretary',
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'email' => 'secretary.dummy@test.com',
            ],
            [
                'role' => 'sk_chairman',
                'first_name' => 'Jose',
                'last_name' => 'Mendoza',
                'email' => 'sk.dummy@test.com',
            ],
        ];
        
        foreach ($officials as $official) {
            $fullName = $official['first_name'] . ' Test ' . $official['last_name'];
            User::create([
                'name' => $fullName,
                'first_name' => $official['first_name'],
                'middle_name' => 'Test',
                'last_name' => $official['last_name'],
                'suffix' => null,
                'email' => $official['email'],
                'password' => Hash::make('password123'),
                'role' => $official['role'],
                'contact_number' => '09' . rand(100000000, 999999999),
                'date_of_birth' => now()->subYears(rand(30, 60))->format('Y-m-d'),
                'place_of_birth' => 'Barangay Kalawag II, Pasig City',
                'sex' => rand(0, 1) ? 'male' : 'female',
                'civil_status' => ['single', 'married', 'widowed'][rand(0, 2)],
                'nationality' => 'Filipino',
                'occupation' => 'Government Official',
                'house_number' => rand(1, 500),
                'street' => 'Main Street',
                'purok_id' => Purok::inRandomOrder()->first()->id,
                'is_approved' => true,
                'is_dummy' => true, // Marker for dummy data
            ]);
            
            $this->command->info("  ✓ Created {$official['role']}: {$official['first_name']} {$official['last_name']}");
        }
    }
    
    /**
     * Create purok presidents for each purok
     */
    private function createPurokPresidents($puroks): void
    {
        $this->command->info('👥 Creating Purok Presidents...');
        
        $firstNames = ['Roberto', 'Ricardo', 'Ramon', 'Rodrigo', 'Rafael', 'Ronaldo', 'Rene', 'Romeo'];
        $lastNames = ['Cruz', 'Ramos', 'Bautista', 'Gonzales', 'Torres', 'Flores', 'Villanueva', 'Aquino'];
        
        foreach ($puroks as $index => $purok) {
            $firstName = $firstNames[$index % count($firstNames)];
            $lastName = $lastNames[$index % count($lastNames)];
            $fullName = $firstName . ' P. ' . $lastName;
            
            User::create([
                'name' => $fullName,
                'first_name' => $firstName,
                'middle_name' => 'P.',
                'last_name' => $lastName,
                'suffix' => null,
                'email' => 'president.purok' . $purok->id . '.dummy@test.com',
                'password' => Hash::make('password123'),
                'role' => 'purok_leader',
                'contact_number' => '09' . rand(100000000, 999999999),
                'date_of_birth' => now()->subYears(rand(35, 65))->format('Y-m-d'),
                'place_of_birth' => 'Barangay Kalawag II, Pasig City',
                'sex' => rand(0, 1) ? 'male' : 'female',
                'civil_status' => 'married',
                'nationality' => 'Filipino',
                'occupation' => 'Purok President',
                'house_number' => rand(1, 500),
                'street' => 'Purok Street',
                'purok_id' => $purok->id,
                'is_approved' => true,
                'is_dummy' => true,
            ]);
            
            $this->command->info("  ✓ Created Purok President for {$purok->name}: {$firstName} {$lastName}");
        }
    }
    
    /**
     * Create residents distributed across puroks
     */
    private function createResidents($puroks, $totalResidents): void
    {
        $this->command->info("🏘️  Creating {$totalResidents} Residents...");
        
        $firstNames = [
            'male' => ['John', 'Michael', 'David', 'James', 'Robert', 'William', 'Richard', 'Joseph', 'Thomas', 'Charles',
                      'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth'],
            'female' => ['Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen',
                        'Nancy', 'Lisa', 'Betty', 'Margaret', 'Sandra', 'Ashley', 'Dorothy', 'Kimberly', 'Emily', 'Donna']
        ];
        
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
                     'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
                     'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson'];
        
        $occupations = ['Teacher', 'Engineer', 'Nurse', 'Driver', 'Vendor', 'Security Guard', 'Sales Representative', 
                       'Accountant', 'Mechanic', 'Electrician', 'Carpenter', 'Cook', 'Cashier', 'Clerk', 'Technician',
                       'Self-employed', 'Student', 'Unemployed', 'Retired', 'Housewife'];
        
        $streets = ['Main Street', 'Second Street', 'Third Street', 'Fourth Street', 'Fifth Street', 
                   'Market Street', 'Church Street', 'School Street', 'Park Avenue', 'River Road'];
        
        $residentsPerPurok = ceil($totalResidents / $puroks->count());
        $progressBar = $this->command->getOutput()->createProgressBar($totalResidents);
        $progressBar->start();
        
        $created = 0;
        foreach ($puroks as $purok) {
            $purokResidents = min($residentsPerPurok, $totalResidents - $created);
            
            for ($i = 0; $i < $purokResidents; $i++) {
                $sex = rand(0, 1) ? 'male' : 'female';
                $firstName = $firstNames[$sex][array_rand($firstNames[$sex])];
                $lastName = $lastNames[array_rand($lastNames)];
                $middleName = chr(rand(65, 90)) . '.';
                $suffix = rand(0, 10) > 8 ? ['Jr.', 'Sr.', 'III'][rand(0, 2)] : null;
                $age = rand(18, 80);
                
                $fullName = $firstName . ' ' . $middleName . ' ' . $lastName;
                if ($suffix) {
                    $fullName .= ' ' . $suffix;
                }
                
                User::create([
                    'name' => $fullName,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'suffix' => $suffix,
                    'email' => strtolower($firstName . '.' . $lastName . '.' . $purok->id . '.' . $i . '.dummy@test.com'),
                    'password' => Hash::make('password123'),
                    'role' => 'resident',
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'date_of_birth' => now()->subYears($age)->subDays(rand(0, 364))->format('Y-m-d'),
                    'place_of_birth' => 'Barangay Kalawag II, Pasig City',
                    'sex' => $sex,
                    'civil_status' => ['single', 'married', 'widowed', 'separated'][rand(0, 3)],
                    'nationality' => 'Filipino',
                    'occupation' => $occupations[array_rand($occupations)],
                    'house_number' => rand(1, 500),
                    'street' => $streets[array_rand($streets)],
                    'purok_id' => $purok->id,
                    'is_approved' => rand(0, 10) > 1, // 90% approved, 10% pending
                    'is_dummy' => true,
                ]);
                
                $created++;
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info("  ✓ Created {$created} residents");
    }
    
    /**
     * Display summary of created users
     */
    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Summary of Dummy Users Created:');
        $this->command->table(
            ['Role', 'Count'],
            [
                ['Barangay Captain', User::where('role', 'barangay_captain')->where('is_dummy', true)->count()],
                ['Barangay Kagawad', User::where('role', 'barangay_kagawad')->where('is_dummy', true)->count()],
                ['Secretary', User::where('role', 'secretary')->where('is_dummy', true)->count()],
                ['SK Chairman', User::where('role', 'sk_chairman')->where('is_dummy', true)->count()],
                ['Purok Leaders', User::where('role', 'purok_leader')->where('is_dummy', true)->count()],
                ['Residents', User::where('role', 'resident')->where('is_dummy', true)->count()],
                ['TOTAL', User::where('is_dummy', true)->count()],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('🔑 Default password for all dummy users: password123');
        $this->command->info('📧 Email format: [role].dummy@test.com or [name].dummy@test.com');
        $this->command->newLine();
    }
}
