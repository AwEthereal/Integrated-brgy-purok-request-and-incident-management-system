<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PurokLeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Debug logging
        \Log::info('PurokLeaderMiddleware - User check', [
            'user_id' => $user ? $user->id : null,
            'user_role' => $user ? $user->role : 'not logged in',
            'purok_id' => $user ? $user->purok_id : null,
            'allowed_roles' => ['purok_leader', 'purok_president']
        ]);

        if (!$user || !in_array($user->role, ['purok_leader', 'purok_president'])) {
            \Log::warning('PurokLeaderMiddleware - Access denied', [
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : 'not logged in'
            ]);
            abort(403, 'Unauthorized. You must be a Purok Leader or Purok President to access this page.');
        }

        return $next($request);
    }
}
