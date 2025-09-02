<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\PurokChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

        $user = $request->user();
        $purokChangeRequest = null;
        
        // Check if user has any pending or rejected purok change requests
        if ($user->role === 'resident' && $user->is_approved) {
            $purokChangeRequest = PurokChangeRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->latest()
                ->first();
        }

        return view('profile.edit', [
            'user' => $user,
            'purokChangeRequest' => $purokChangeRequest,
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
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
            
            // Check if purok is being changed for an active resident
            $purokChanged = isset($validated['purok_id']) && $user->purok_id != $validated['purok_id'];
            $isActiveResident = $user->role === 'resident' && $user->is_approved;
            
            if ($purokChanged && $isActiveResident) {
                // Check if there's already a pending purok change request
                $existingRequest = PurokChangeRequest::where('user_id', $user->id)
                    ->whereIn('status', ['pending'])
                    ->first();
                
                if ($existingRequest) {
                    // If there's already a pending request, show an error
                    return back()->with('error', 'You already have a pending purok change request. Please wait for it to be processed.');
                }
                
                // For active residents, create a purok change request instead of updating directly
                PurokChangeRequest::create([
                    'user_id' => $user->id,
                    'current_purok_id' => $user->purok_id,
                    'requested_purok_id' => $validated['purok_id'],
                    'status' => 'pending',
                    'requested_at' => now(),
                    'rejection_reason' => null
                ]);
                
                // Remove purok_id from validated data to prevent direct update
                unset($validated['purok_id']);
                
                // Show success message
                return back()->with('success', 'Your purok change request has been submitted for approval.');
            }
            
            // Update user data
            $user->fill($request->validated());
            
            // Save all changes
            if ($user->save()) {
                if ($emailChanged) {
                    Log::info('User record updated with new email', [
                        'user_id' => $user->id,
                        'email_updated' => true
                    ]);
                    
                    // Send verification email
                    $user->sendEmailVerificationNotification();
                    Log::info('Verification email sent to new address');
                }
                
                // Refresh the user's session data
                Auth::login($user);
                Log::info('User session refreshed after update');
                
                // Always redirect to dashboard after profile update
                $redirectUrl = route('dashboard');
                
                // Store success message in session
                session()->flash('success', 'Profile updated successfully!');
                
                // For AJAX requests, return JSON response
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Profile updated successfully!',
                        'redirect' => $redirectUrl,
                        'user' => [
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email
                        ]
                    ]);
                }
                
                // For regular form submissions, redirect to dashboard with success message
                return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
            } else {
                Log::error('Failed to update user profile', [
                    'user_id' => $user->id
                ]);
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update profile. Please try again.'
                    ], 422);
                }
                
                return back()->with('error', 'Failed to update profile. Please try again.');
            }
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
    
    /**
     * Delete the user's account (for rejected users).
     */
    public function destroyAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Only allow deletion for rejected accounts
        if (!$user->rejected_at) {
            return redirect()->back()
                ->with('error', 'This action is only allowed for rejected accounts.');
        }
        
        try {
            // Log out the user
            Auth::logout();
            
            // Delete the user
            $user->delete();
            
            // Invalidate the session and regenerate the CSRF token
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('welcome')
                ->with('status', 'Your account has been successfully deleted.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting rejected account: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting your account. Please try again.');
        }
    }
}
