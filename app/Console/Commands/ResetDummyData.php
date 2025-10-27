<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class ResetDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:reset {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all dummy test data (users and related records) without affecting real data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️  Dummy Data Reset Tool');
        $this->newLine();
        
        // Count dummy data
        $dummyUsersCount = User::where('is_dummy', true)->count();
        
        if ($dummyUsersCount === 0) {
            $this->warn('⚠️  No dummy data found to reset.');
            return Command::SUCCESS;
        }
        
        // Show what will be deleted
        $this->info('📊 Dummy Data to be Removed:');
        $this->table(
            ['Type', 'Count'],
            [
                ['Dummy Users', $dummyUsersCount],
                ['  - Barangay Officials', User::where('is_dummy', true)->whereIn('role', ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman'])->count()],
                ['  - Purok Presidents', User::where('is_dummy', true)->where('role', 'purok_president')->count()],
                ['  - Residents', User::where('is_dummy', true)->where('role', 'resident')->count()],
            ]
        );
        
        $this->newLine();
        $this->warn('⚠️  This will also delete all related data (requests, incident reports, etc.) created by dummy users.');
        $this->newLine();
        
        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all dummy data?', false)) {
                $this->info('❌ Operation cancelled.');
                return Command::SUCCESS;
            }
        }
        
        $this->info('🔄 Deleting dummy data...');
        $this->newLine();
        
        DB::beginTransaction();
        
        try {
            // Get dummy user IDs
            $dummyUserIds = User::where('is_dummy', true)->pluck('id');
            
            // Delete related data
            $deletedRequests = ServiceRequest::whereIn('user_id', $dummyUserIds)->delete();
            $this->info("  ✓ Deleted {$deletedRequests} service requests");
            
            $deletedIncidents = IncidentReport::whereIn('user_id', $dummyUserIds)->delete();
            $this->info("  ✓ Deleted {$deletedIncidents} incident reports");
            
            // Delete announcements created by dummy users
            $deletedAnnouncements = Announcement::whereIn('created_by', $dummyUserIds)->delete();
            $this->info("  ✓ Deleted {$deletedAnnouncements} announcements");
            
            // Delete dummy users
            $deletedUsers = User::where('is_dummy', true)->delete();
            $this->info("  ✓ Deleted {$deletedUsers} dummy users");
            
            DB::commit();
            
            $this->newLine();
            $this->info('✅ Dummy data reset completed successfully!');
            $this->info('💡 Real user data remains intact.');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error resetting dummy data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
