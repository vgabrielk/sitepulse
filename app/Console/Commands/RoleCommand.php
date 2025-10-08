<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class RoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign {user_email} {role_slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->argument('user_email');
        $roleSlug = $this->argument('role_slug');

        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            $this->error("User with email {$userEmail} not found.");
            return 1;
        }

        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            $this->error("Role with slug {$roleSlug} not found.");
            $this->info("Available roles:");
            Role::all()->each(function ($role) {
                $this->line("- {$role->slug} ({$role->name})");
            });
            return 1;
        }

        $user->assignRole($roleSlug);
        $this->info("Role '{$role->name}' assigned to user '{$user->name}' ({$user->email})");

        return 0;
    }
}