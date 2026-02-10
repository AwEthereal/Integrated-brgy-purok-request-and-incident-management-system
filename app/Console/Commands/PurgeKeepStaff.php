<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Announcement;
use App\Models\Feedback;
use App\Models\Purok;

class PurgeKeepStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --keep-purok-names: Comma-separated list of Purok names whose leaders must be preserved.
     * --fill-purok-leaders: Integer number of extra Purok Leaders to keep (random) in addition to keep-purok-names.
     * --include-files: Also delete uploaded files in the public disk (requests/, incidents/).
     * --dry-run: Do not delete anything; only show what would be deleted/kept.
     * --force: Skip confirmation prompt.
     */
    protected $signature = 'data:purge-keep-staff '
        .'{--keep-purok-names= : CSV of Purok names to keep leaders for (e.g., "Tagumpay 3,Tagumpay 2")} '
        .'{--fill-purok-leaders=0 : Additional random Purok Leaders to keep} '
        .'{--include-files : Also delete uploaded files (public/requests, public/incidents)} '
        .'{--dry-run : Preview only, do not delete} '
        .'{--force : Skip confirmation}';

    protected $description = 'Purge test data, removing all residents and non-preserved users while keeping admins/secretary and selected Purok Leaders; optionally remove uploads.';

    public function handle()
    {
        $keepPurokNames = array_filter(array_map('trim', explode(',', (string)$this->option('keep-purok-names'))));
        $fillExtraLeaders = (int) $this->option('fill-purok-leaders');
        $includeFiles = (bool) $this->option('include-files');
        $dryRun = (bool) $this->option('dry-run');

        // Build keep list
        $keepUserIds = collect();

        // Always keep admins and secretary
        $adminKeep = User::query()
            ->whereIn('role', ['admin','administrator','secretary','barangay_captain'])
            ->pluck('id');
        $keepUserIds = $keepUserIds->merge($adminKeep);

        // Keep Purok Leaders for specified Purok names
        if (!empty($keepPurokNames)) {
            $purokIds = Purok::query()->whereIn('name', $keepPurokNames)->pluck('id');
            if ($purokIds->isNotEmpty()) {
                $leaders = User::query()
                    ->where('role', 'purok_leader')
                    ->whereIn('purok_id', $purokIds)
                    ->pluck('id');
                $keepUserIds = $keepUserIds->merge($leaders);
            }
        }

        // Fill with random additional Purok Leaders if requested
        if ($fillExtraLeaders > 0) {
            $additional = User::query()
                ->where('role', 'purok_leader')
                ->whereNotIn('id', $keepUserIds)
                ->inRandomOrder()
                ->limit($fillExtraLeaders)
                ->pluck('id');
            $keepUserIds = $keepUserIds->merge($additional);
        }

        $keepUserIds = $keepUserIds->unique()->values();

        // Compute stats
        $totalUsers = User::count();
        $roleCounts = User::select('role', DB::raw('count(*) as c'))
            ->groupBy('role')->pluck('c','role');
        $keptCount = User::whereIn('id', $keepUserIds)->count();
        $toDeleteCount = max(0, $totalUsers - $keptCount);

        $this->info('Purge plan:');
        $this->table(['Metric','Value'], [
            ['Total users', $totalUsers],
            ['Users to keep', $keptCount],
            ['Users to delete', $toDeleteCount],
            ['Include files', $includeFiles ? 'yes' : 'no'],
            ['Dry run', $dryRun ? 'yes' : 'no'],
        ]);

        $this->line('Users kept (IDs): '.($keepUserIds->isEmpty() ? '(none)' : $keepUserIds->implode(', ')));
        $this->newLine();
        $this->info('Counts by role:');
        foreach ($roleCounts as $role => $count) {
            $this->line(" - {$role}: {$count}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->info('Dry run complete. No data was deleted.');
            return Command::SUCCESS;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('This will DELETE data and users not in the keep list. Continue?')) {
                $this->warn('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $hadError = false;
        try {
            // Disable FK checks for truncate (TRUNCATE is DDL and auto-commits in MySQL)
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Delete domain data first
            if (class_exists(ServiceRequest::class)) {
                ServiceRequest::truncate();
            }
            if (class_exists(IncidentReport::class)) {
                IncidentReport::truncate();
            }
            if (class_exists(Feedback::class)) {
                Feedback::truncate();
            }
            if (class_exists(Announcement::class)) {
                // Announcements may have created_by FK; truncate is fine with FK disabled
                Announcement::truncate();
            }
            if (class_exists(\App\Models\PurokChangeRequest::class)) {
                \App\Models\PurokChangeRequest::truncate();
            }

            // Delete users not in keep list
            User::whereNotIn('id', $keepUserIds)->delete();
        } catch (\Throwable $e) {
            $hadError = true;
            $this->error('Error during purge: '.$e->getMessage());
        } finally {
            // Ensure FK checks are re-enabled
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $e2) {}
        }

        if ($hadError) {
            return Command::FAILURE;
        }

        // Optionally delete files
        if ($includeFiles) {
            // These are common public paths used by controllers
            Storage::disk('public')->deleteDirectory('requests');
            Storage::disk('public')->deleteDirectory('incidents');
        }

        $this->newLine();
        $this->info('Purge complete.');
        $this->table(['Metric','Value'], [
            ['Users remaining', User::count()],
            ['Requests remaining', class_exists(ServiceRequest::class) ? ServiceRequest::count() : 0],
            ['Incident reports remaining', class_exists(IncidentReport::class) ? IncidentReport::count() : 0],
        ]);

        return Command::SUCCESS;
    }
}
