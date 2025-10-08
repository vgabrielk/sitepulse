<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required');
        }
        
        if (!$user->hasPermission($permission)) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this resource');
        }
        
        return $next($request);
    }
}