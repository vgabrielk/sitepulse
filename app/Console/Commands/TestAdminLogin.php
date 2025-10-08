<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestAdminLogin extends Command
{
    protected $signature = 'test:admin-login';
    protected $description = 'Test admin login and permissions';

    public function handle()
    {
        $user = User::where('email', 'vitorfelixdev@gmail.com')->first();
        
        if (!$user) {
            $this->error('Admin user not found');
            return 1;
        }

        $this->info('Testing admin user: ' . $user->email);
        
        // Test authentication
        Auth::login($user);
        $this->info('User logged in successfully');
        
        // Test permissions
        $permissions = [
            'admin.dashboard',
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'sites.view_all',
            'reviews.moderate'
        ];
        
        foreach ($permissions as $permission) {
            $hasPermission = $user->hasPermission($permission);
            $this->line("Permission '{$permission}': " . ($hasPermission ? 'YES' : 'NO'));
        }
        
        // Test roles
        $this->line('Roles: ' . $user->roles->pluck('slug')->join(', '));
        $this->line('Is admin: ' . ($user->isAdmin() ? 'YES' : 'NO'));
        
        return 0;
    }
}