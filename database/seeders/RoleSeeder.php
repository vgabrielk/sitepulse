<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        Role::createDefaultRoles();
        
        // Assign super admin role to the first user (if exists)
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole('super-admin');
        }
        
        // Assign admin role to users with admin email
        $adminEmail = config('app.admin_email');
        if ($adminEmail) {
            $adminUser = User::where('email', $adminEmail)->first();
            if ($adminUser && !$adminUser->hasRole('super-admin')) {
                $adminUser->assignRole('admin');
            }
        }
        
        // Assign admin role to enterprise users (for backward compatibility)
        $enterpriseUsers = User::whereHas('client', function($query) {
            $query->where('plan', 'enterprise');
        })->get();
        
        foreach ($enterpriseUsers as $user) {
            if (!$user->hasAnyRole(['super-admin', 'admin'])) {
                $user->assignRole('admin');
            }
        }
    }
}