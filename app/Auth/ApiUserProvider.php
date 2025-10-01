<?php

namespace App\Auth;

use App\Models\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ApiUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return Client::find($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return Client::where('id', $identifier)
            ->where('api_key', $token)
            ->where('is_active', true)
            ->first();
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        // Not needed for API authentication
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (!isset($credentials['api_key'])) {
            return null;
        }

        return Client::where('api_key', $credentials['api_key'])
            ->where('is_active', true)
            ->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user->api_key === $credentials['api_key'] && $user->is_active;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // Not needed for API authentication
    }
}
