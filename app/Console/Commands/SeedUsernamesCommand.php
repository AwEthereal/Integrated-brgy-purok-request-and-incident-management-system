<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\AsCommand;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

#[AsCommand(name: 'user:seed-usernames')]
class SeedUsernamesCommand extends Command
{
    protected $signature = 'user:seed-usernames {--dry-run : Show what would be changed without saving} {--limit-purok=5 : Number of purok leader accounts to assign (default 5)}';

    protected $description = 'Assign placeholder numeric usernames (employee numbers) to Chairman, Secretary, and 5 Purok Leaders.';

    public function handle()
    {
        $dry = (bool) $this->option('dry-run');
        $limitPurok = (int) $this->option('limit-purok');

        $assignments = [];

        // Helper to set username safely
        $setUsername = function (User $user, string $desired) use (&$assignments, $dry) {
            $base = $desired;
            $candidate = $base;
            $i = 0;
            while (User::where('username', $candidate)->exists()) {
                $i++;
                $candidate = (string) ((int)$base + $i);
            }

            if (! $dry) {
                $user->username = $candidate;
                $user->save();
            }

            $assignments[] = [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'purok' => optional($user->purok)->name,
                'username' => $candidate,
            ];
        };

        // 1) Chairman / Super Admin (use role 'admin' first, else 'barangay_captain')
        $chairman = User::whereIn('role', ['admin','barangay_captain'])
            ->orderBy('id')->first();
        if ($chairman && empty($chairman->username)) {
            $setUsername($chairman, '100001');
        } else {
            $this->info('Chairman already has a username or no chairman found.');
        }

        // 2) Secretary
        $secretary = User::where('role', 'secretary')->orderBy('id')->first();
        if ($secretary && empty($secretary->username)) {
            $setUsername($secretary, '200001');
        } else {
            $this->info('Secretary already has a username or no secretary found.');
        }

        // 3) Five Purok Leaders
        $leaders = User::where('role', 'purok_leader')
            ->with('purok')
            ->orderBy('id')
            ->get();

        if ($leaders->isEmpty()) {
            $this->warn('No Purok Leader accounts found.');
        } else {
            // Preferred order: Tagumpay 3 (300001), Tagumpay 2 (300002), then first 3 others
            $selected = collect();
            $tag3 = $leaders->first(function ($u) {
                return $u->purok && Str::contains(Str::lower($u->purok->name), 'tagumpay 3');
            });
            if ($tag3 && empty($tag3->username)) $selected->push($tag3);

            $tag2 = $leaders->first(function ($u) {
                return $u->purok && Str::contains(Str::lower($u->purok->name), 'tagumpay 2');
            });
            if ($tag2 && empty($tag2->username) && $selected->doesntContain('id', $tag2->id)) $selected->push($tag2);

            foreach ($leaders as $u) {
                if ($selected->count() >= $limitPurok) break;
                if (!empty($u->username)) continue;
                if ($selected->contains('id', $u->id)) continue;
                $selected->push($u);
            }

            $start = 300001;
            $n = 0;
            foreach ($selected->take($limitPurok) as $u) {
                $setUsername($u, (string) ($start + $n));
                $n++;
            }
        }

        // Output mapping
        $this->table(['ID','Name','Role','Purok','Assigned Username'], $assignments);

        // Write mapping to file
        $lines = [
            'SEEDED USERNAMES ('.now().')',
            str_repeat('=', 40),
        ];
        foreach ($assignments as $a) {
            $lines[] = sprintf('id=%s | role=%s | purok=%s | username=%s | name=%s',
                $a['id'], $a['role'], $a['purok'] ?? '-', $a['username'], $a['name']);
        }
        File::put(base_path('SEEDED_USERNAMES.txt'), implode(PHP_EOL, $lines));

        $this->info(($dry ? '[DRY-RUN] ' : '').'Done. Mapping written to SEEDED_USERNAMES.txt');
        return self::SUCCESS;
    }
}
