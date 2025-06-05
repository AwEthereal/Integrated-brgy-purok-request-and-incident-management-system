<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Store the previous URL in the session if it's not the profile page
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        
        if ($previousUrl !== $currentUrl && 
            !str_contains($previousUrl, 'profile') &&
            !str_contains($previousUrl, 'login') &&
            !str_contains($previousUrl, 'register') &&
            !str_contains($previousUrl, 'password') &&
            !str_contains($previousUrl, 'email/verify')) {
            session(['profile_previous_url' => $previousUrl]);
        }

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            
            Log::info('Starting profile update', [
                'user_id' => $user->id,
                'current_email' => $user->email,
                'new_email' => $validated['email'] ?? 'not provided',
                'changed_fields' => array_keys(array_diff_assoc($validated, $user->toArray()))
            ]);
            
            // Check if email is being updated
            $emailChanged = $user->email !== $validated['email'];
            
            // Update all fields including the name
            $user->fill($validated);
            
            if ($emailChanged) {
                Log::info('Email changed, updating user record', [
                    'old_email' => $user->email,
                    'new_email' => $validated['email']
                ]);
                
                // Reset verification if email changed
                $user->email_verified_at = null;
                
                // Save all changes
                if ($user->save()) {
                    Log::info('User record updated with new email', [
                        'user_id' => $user->id,
                        'email_updated' => true
                    ]);
                    
                    // Send verification email
                    $user->sendEmailVerificationNotification();
                    Log::info('Verification email sent to new address');
                } else {
                    Log::error('Failed to update user email', [
                        'user_id' => $user->id
                    ]);
                }
            } else {
                // Save if email didn't change
                if (!$user->save()) {
                    Log::error('Failed to update user profile', [
                        'user_id' => $user->id
                    ]);
                }
            }
            
            // Refresh the user's session data
            Auth::login($user);
            Log::info('User session refreshed after update');
            
            // Get the previous URL from session or use a default
            $previousUrl = session()->pull('profile_previous_url', route('dashboard'));
            
            // If the previous URL is the current page, use dashboard as fallback
            if (url()->current() === $previousUrl) {
                $previousUrl = route('dashboard');
            }
            
            // Store success message in session
            session()->flash('success', 'Profile updated successfully!');
            
            // For AJAX requests, return JSON response
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'redirect' => $previousUrl,
                    'user' => [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email
                    ]
                ]);
            }
            
            // For regular form submissions, redirect to the previous URL
            return redirect($previousUrl)
                ->with('status', 'profile-updated');
                
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Profile update error: ' . $e->getMessage());
            
            // For AJAX requests, return JSON response
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update profile. Please try again.'
                ], 422);
            }
            
            // For regular form submissions, redirect back with error
            return back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
    /**
     * Show the password update form.
     */
    public function editPassword(): View
    {
        return view('profile.update-password');
    }
    
    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);        
        
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return back()->with('status', 'password-updated')
                     ->with('success', 'Password updated successfully!');
    }
}
