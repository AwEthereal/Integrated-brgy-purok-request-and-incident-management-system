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
        $userRole = $user->getAttribute('role');
        $userId = $user->getAttribute('id');
        
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
        
        // If user is a purok leader/president, redirect to their dashboard
        if (in_array($userRole, ['purok_leader', 'purok_president'])) {
            Log::debug('CheckRole: Redirecting purok leader to dashboard', [
                'user_id' => $userId,
                'role' => $userRole
            ]);
            return redirect()->route('purok_leader.dashboard');
        }

        // If user is a barangay official but not allowed for this route
        if (in_array($userRole, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin'])) {
            Log::warning('CheckRole: Barangay official not authorized for route', [
                'user_id' => $userId,
                'role' => $userRole,
                'path' => $request->path()
            ]);
            
            // Redirect to barangay dashboard if trying to access purok leader routes
            if (str_contains($request->path(), 'purok-leader')) {
                return redirect()->route('dashboard');
            }
            
            // For API requests, return 403
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            
            // Otherwise, show 403 page
            abort(403, 'You do not have permission to access this page.');
        }
        
        // If user is a resident, allow access to dashboard
        if ($userRole === 'resident' && $request->path() === 'dashboard') {
            return $next($request);
        }
        
        // For any other case, redirect to home with 403
        Log::warning('CheckRole: Unauthorized access attempt', [
            'user_id' => $userId,
            'role' => $userRole,
            'path' => $request->path()
        ]);
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        
        return redirect('/')->with('error', 'You do not have permission to access this page.');
    }
}
