<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if user should see feedback prompt
        $user = $request->user();
        
        // Count resolved items without feedback
        $resolvedIncidents = $user->incidentReports()
            ->whereIn('status', ['Resolved', 'Rejected'])
            ->whereDoesntHave('feedback')
            ->count();

        $resolvedRequests = $user->requests()
            ->whereIn('status', ['Completed', 'Rejected'])
            ->whereDoesntHave('feedback')
            ->count();

        $totalResolved = $resolvedIncidents + $resolvedRequests;
        $showFeedbackPrompt = $totalResolved >= 3 && $totalResolved <= 5 && !$request->cookie('skip_feedback');
        
        if ($showFeedbackPrompt) {
            $request->session()->flash('show_feedback_prompt', true);
            $request->session()->flash('resolved_count', $totalResolved);
            $request->session()->put('recently_shown_feedback_prompt', true);
        }

        // Redirect based on role
        if ($user->role === 'purok_leader') {
            return redirect()->route('purok_leader.dashboard');
        }
        if ($user->role === 'barangay_clerk') {
            return redirect('/');
        }
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
