<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        // If no user is logged in, redirect to login
        if (!$user) {
            Log::warning('CheckRole: No authenticated user');
            return redirect()->route('login');
        }

        // Get the user's role safely
        $userRole = $user->role;
        $userId = $user->id;
        
        // Log the user's role and requested roles for debugging
        Log::debug('CheckRole: Checking access', [
            'user_id' => $userId,
            'user_role' => $userRole,
            'allowed_roles' => $roles,
            'path' => $request->path()
        ]);

        // Check if user has any of the allowed roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // For API requests, return 403
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Otherwise, show 403 page
        abort(403, 'You do not have permission to access this page.');
    }
}
