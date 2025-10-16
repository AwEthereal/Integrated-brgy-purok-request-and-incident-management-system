<?php

namespace App\Console\Commands;

use App\Models\Request;
use App\Models\IncidentReport;
use App\Models\PurokChangeRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear-test {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all requests and incident reports while keeping users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL requests and incident reports. Do you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting data cleanup...');

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Count before deletion
            $requestCount = Request::count();
            $incidentCount = IncidentReport::count();
            $purokChangeCount = 0;
            
            if (class_exists(PurokChangeRequest::class)) {
                $purokChangeCount = PurokChangeRequest::count();
            }

            $this->info("Found {$requestCount} requests");
            $this->info("Found {$incidentCount} incident reports");
            if ($purokChangeCount > 0) {
                $this->info("Found {$purokChangeCount} purok change requests");
            }

            // Delete data
            $this->warn('Deleting requests...');
            Request::truncate();
            $this->info('✓ Requests deleted');

            $this->warn('Deleting incident reports...');
            IncidentReport::truncate();
            $this->info('✓ Incident reports deleted');

            if (class_exists(PurokChangeRequest::class)) {
                $this->warn('Deleting purok change requests...');
                PurokChangeRequest::truncate();
                $this->info('✓ Purok change requests deleted');
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Verify deletion
            $remainingRequests = Request::count();
            $remainingIncidents = IncidentReport::count();
            $remainingUsers = DB::table('users')->count();

            $this->newLine();
            $this->info('=== Cleanup Complete ===');
            $this->info("Requests remaining: {$remainingRequests}");
            $this->info("Incident reports remaining: {$remainingIncidents}");
            $this->info("Users kept: {$remainingUsers}");
            $this->newLine();
            $this->info('✓ All test data cleared successfully!');

            return 0;
        } catch (\Exception $e) {
            // Re-enable foreign key checks even if error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->error('Error clearing data: ' . $e->getMessage());
            return 1;
        }
    }
}
