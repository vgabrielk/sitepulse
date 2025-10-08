<?php

namespace App\Helpers;

use App\Models\User;

class PermissionHelper
{
    /**
     * Check if current user has permission
     */
    public static function can(string $permission): bool
    {
        $user = auth()->user();
        return $user ? $user->hasPermission($permission) : false;
    }

    /**
     * Check if current user has any of the given permissions
     */
    public static function canAny(array $permissions): bool
    {
        $user = auth()->user();
        return $user ? $user->hasAnyPermission($permissions) : false;
    }

    /**
     * Check if current user has role
     */
    public static function hasRole(string $roleSlug): bool
    {
        $user = auth()->user();
        return $user ? $user->hasRole($roleSlug) : false;
    }

    /**
     * Check if current user has any of the given roles
     */
    public static function hasAnyRole(array $roleSlugs): bool
    {
        $user = auth()->user();
        return $user ? $user->hasAnyRole($roleSlugs) : false;
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin(): bool
    {
        $user = auth()->user();
        return $user ? $user->isAdmin() : false;
    }

    /**
     * Get current user's roles
     */
    public static function getUserRoles(): array
    {
        $user = auth()->user();
        return $user ? $user->roles->pluck('slug')->toArray() : [];
    }

    /**
     * Get current user's permissions
     */
    public static function getUserPermissions(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $permissions = [];
        foreach ($user->roles as $role) {
            if ($role->permissions) {
                $permissions = array_merge($permissions, $role->permissions);
            }
        }

        return array_unique($permissions);
    }
}
