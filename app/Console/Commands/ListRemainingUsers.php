<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\AsCommand;
use App\Models\User;

#[AsCommand(name: 'users:list-remaining', description: 'List remaining users with ID, role, purok, and numeric username')]
class ListRemainingUsers extends Command
{
    protected $signature = 'users:list-remaining';
    protected $description = 'List remaining users with ID, role, purok, and numeric username';

    public function handle()
    {
        $users = User::with('purok')->orderBy('role')->orderBy('id')->get(['id','name','role','username','purok_id']);

        $rows = $users->map(function ($u) {
            return [
                'ID' => $u->id,
                'Role' => $u->role,
                'Purok' => optional($u->purok)->name,
                'Username' => $u->username ?: '(none)',
                'Name' => $u->name,
            ];
        })->toArray();

        $this->table(['ID','Role','Purok','Username','Name'], $rows);
        return self::SUCCESS;
    }
}
