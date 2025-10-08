<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required');
        }
        
        // Check if user is admin
        if (!$this->isAdmin($user)) {
            return redirect()->route('dashboard')->with('error', 'Admin access required');
        }
        
        return $next($request);
    }
    
    private function isAdmin(User $user): bool
    {
        // Check if user has admin permissions through roles
        if ($user->hasPermission('admin.access')) {
            return true;
        }
        
        // Fallback: Check if user email is admin email (for backward compatibility)
        if ($user->email === config('app.admin_email')) {
            return true;
        }
        
        // Fallback: Check if user's client has enterprise plan (for backward compatibility)
        $client = $user->client;
        if ($client && $client->plan === 'enterprise') {
            return true;
        }
        
        return false;
    }
}
