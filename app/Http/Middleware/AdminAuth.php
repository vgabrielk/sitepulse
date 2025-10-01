<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $client = $request->user();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        // Check if client is admin (you can implement your own admin logic)
        if (!$this->isAdmin($client)) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required',
            ], 403);
        }
        
        return $next($request);
    }
    
    private function isAdmin(Client $client): bool
    {
        // Simple admin check - you can implement more sophisticated logic
        return $client->email === config('app.admin_email') || 
               $client->plan === 'enterprise';
    }
}
