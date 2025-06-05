<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Access\AuthorizationException;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  string  $hash
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Check if the hash is valid
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('verification.notice')
                ->with('error', 'The verification link is invalid.');
        }

        // Check if the user is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('status', 'Your email is already verified.');
        }

        // Mark the email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Log in the user if not already logged in
            if (!Auth::check()) {
                Auth::login($user);
            }

            return redirect()->route('dashboard')
                ->with('status', 'Thank you for verifying your email address!');
        }

        return redirect()->route('verification.notice')
            ->with('error', 'Unable to verify your email. Please try again.');
    }
}
