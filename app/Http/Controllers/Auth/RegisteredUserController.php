<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Purok;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $puroks = Purok::all();
        return view('auth.register', compact('puroks'));
    }


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Name fields
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            
            // Contact information
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{11}$/'],
            
            // Personal details
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'civil_status' => ['nullable', 'in:single,married,widowed,separated,divorced'],
            'occupation' => ['nullable', 'string', 'max:255'],
            
            // Address information
            'address' => ['required', 'string', 'max:1000'],
            'purok_id' => ['required', 'exists:puroks,id'],
            
            // Authentication
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Format contact number to remove any non-numeric characters
        $validated['contact_number'] = preg_replace('/[^0-9]/', '', $validated['contact_number']);
        
        // Combine first and last name for the name field
        $validated['name'] = trim(sprintf('%s %s', 
            $validated['first_name'], 
            $validated['last_name']
        ));
        
        // Set default role
        $validated['role'] = 'resident';
        
        // Hash the password
        $validated['password'] = Hash::make($validated['password']);
        
        // Create the user with all validated data
        $user = User::create($validated);

        // Log in the user
        Auth::login($user);

        // Redirect to dashboard after registration
        return redirect()->route('dashboard');
    }
}
