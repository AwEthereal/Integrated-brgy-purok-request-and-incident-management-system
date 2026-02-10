<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\AsCommand;
use App\Models\User;

#[AsCommand(name: 'users:fill-usernames', description: 'Assign numeric usernames to any users missing one')]
class FillMissingUsernames extends Command
{
    protected $signature = 'users:fill-usernames {--dry-run : Preview changes without saving}';

    public function handle()
    {
        $dry = (bool) $this->option('dry-run');
        $assignments = [];

        $nextFor = function (string $prefixStart) {
            $start = (int) $prefixStart;
            $candidate = $start;
            while (User::where('username', (string)$candidate)->exists()) {
                $candidate++;
            }
            return (string)$candidate;
        };

        // Role buckets and starting numbers
        $roleStarts = [
            'admin' => 100001,
            'administrator' => 100001,
            'barangay_captain' => 110001,
            'secretary' => 200001,
            'purok_leader' => 300001,
        ];

        $missing = User::whereNull('username')->orWhere('username','')->with('purok')->get();
        foreach ($missing as $u) {
            $start = $roleStarts[$u->role] ?? 900001; // fallback range
            $desired = $nextFor($start);
            if (! $dry) {
                $u->username = $desired;
                $u->save();
            }
            $assignments[] = [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'purok' => optional($u->purok)->name,
                'assigned' => $desired,
            ];
        }

        if (empty($assignments)) {
            $this->info('All users already have numeric usernames.');
            return self::SUCCESS;
        }

        $this->table(['ID','Role','Purok','Assigned Username','Name'], array_map(function ($a) {
            return [$a['id'], $a['role'], $a['purok'] ?? '-', $a['assigned'], $a['name']];
        }, $assignments));

        $this->info(($dry ? '[DRY-RUN] ' : '').'Done.');
        return self::SUCCESS;
    }
}
