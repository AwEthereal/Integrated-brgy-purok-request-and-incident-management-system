<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\AsCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

#[AsCommand(name: 'users:reset-passwords', description: 'Reset password for selected users (by --ids or --roles). If none provided, applies to all users.')]
class ResetPasswords extends Command
{
    protected $signature = 'users:reset-passwords {password : New plaintext password to set} {--ids= : Comma-separated user IDs} {--roles= : Comma-separated roles} {--dry-run : Preview only}';

    public function handle()
    {
        $password = (string) $this->argument('password');
        $ids = collect(array_filter(array_map('trim', explode(',', (string) $this->option('ids')))))->filter();
        $roles = collect(array_filter(array_map('trim', explode(',', (string) $this->option('roles')))))->filter();
        $dry = (bool) $this->option('dry-run');

        $q = User::query();
        if ($ids->isNotEmpty()) {
            $q->whereIn('id', $ids->map(fn($v)=> (int)$v));
        }
        if ($roles->isNotEmpty()) {
            $q->whereIn('role', $roles);
        }
        $targets = $q->get(['id','name','role','username']);

        if ($targets->isEmpty()) {
            $this->warn('No users matched the filter. Nothing to do.');
            return self::SUCCESS;
        }

        $this->table(['ID','Role','Username','Name'], $targets->map(fn($u)=>[$u->id,$u->role,$u->username,$u->name])->toArray());

        if ($dry) {
            $this->info('[DRY-RUN] No passwords changed.');
            return self::SUCCESS;
        }

        foreach ($targets as $user) {
            $user->password = Hash::make($password);
            $user->save();
        }

        $this->info('Passwords updated for '.$targets->count().' user(s).');
        return self::SUCCESS;
    }
}
