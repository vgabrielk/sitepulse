<?php

namespace App\Auth;

use App\Models\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class ApiGuard implements Guard
{
    protected $request;
    protected $provider;
    protected $user;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->user = null;
    }

    public function check(): bool
    {
        return !is_null($this->user());
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $apiKey = $this->request->header('X-API-Key') ?? $this->request->query('api_key');
        
        if (!$apiKey) {
            return null;
        }

        $this->user = $this->provider->retrieveByCredentials(['api_key' => $apiKey]);
        
        return $this->user;
    }

    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
        
        return null;
    }

    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['api_key'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);
        
        return !is_null($user) && $user->is_active;
    }

    public function setUser(Authenticatable $user): void
    {
        $this->user = $user;
    }
}
