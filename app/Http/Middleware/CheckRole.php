<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            return redirect()->route('login');
        }
        
        // Check if user has any of the allowed roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }
        
        // If user is a purok leader/president, redirect to their dashboard
        if (in_array($user->role, ['purok_leader', 'purok_president'])) {
            return redirect()->route('purok_leader.dashboard');
        }
        
        // For any other case, redirect to home with 403
        abort(403, 'Unauthorized action.');
    }
}
