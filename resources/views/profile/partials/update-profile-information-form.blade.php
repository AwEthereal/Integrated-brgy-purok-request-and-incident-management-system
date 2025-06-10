<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')
    <input type="hidden" name="_previous_url" value="{{ url()->current() }}">

        <!-- Name Fields -->
        <div class="space-y-6">
            <div>
                <h4 class="text-base font-medium text-gray-900">Personal Information</h4>
                <p class="mt-1 text-sm text-gray-500">Update your name and other personal details</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <x-input-label for="first_name" :value="__('First Name')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="first_name" 
                            name="first_name" 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                            :value="old('first_name', $user->first_name)" 
                            required 
                            autofocus 
                        />
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('first_name')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="middle_name" :value="__('Middle Name')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        </div>
                        <x-text-input 
                            id="middle_name" 
                            name="middle_name" 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                            :value="old('middle_name', $user->middle_name)" 
                        />
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('middle_name')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="last_name" :value="__('Last Name')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <x-text-input 
                            id="last_name" 
                            name="last_name" 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                            :value="old('last_name', $user->last_name)" 
                            required 
                        />
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('last_name')" />
                </div>
            
        </div>

        <!-- Contact Information -->
        <div class="space-y-6 pt-6 border-t border-gray-200">
            <div>
                <h4 class="text-base font-medium text-gray-900">Contact Information</h4>
                <p class="mt-1 text-sm text-gray-500">Update your contact details for better communication</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <x-input-label for="contact_number" :value="__('Contact Number')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="contact_number" 
                            name="contact_number" 
                            type="tel" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                            :value="old('contact_number', $user->contact_number)" 
                            placeholder="+63 912 345 6789"
                            required 
                        />
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('contact_number')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                            :value="old('email', $user->email)" 
                            required 
                            autocomplete="username"
                        />
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('email')" />

                    @php $authUser = auth()->user(); @endphp
                    @if ($authUser instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $authUser->hasVerifiedEmail())
                        <div class="mt-3 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-amber-800">
                                        {{ __('Your email address is unverified.') }}
                                        <button form="send-verification" class="font-medium text-amber-700 hover:text-amber-600 underline">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-1 text-sm text-green-700">
                                            {{ __('A new verification link has been sent to your email address.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Personal Details -->
        <div class="space-y-6 pt-6 border-t border-gray-200">
            <div>
                <h4 class="text-base font-medium text-gray-900">Personal Details</h4>
                <p class="mt-1 text-sm text-gray-500">Update your personal information</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <x-input-label for="birth_date" :value="__('Date of Birth')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="birth_date" 
                            name="birth_date" 
                            type="date" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                            :value="old('birth_date', $user->birth_date?->format('Y-m-d'))" 
                            required 
                        />
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('birth_date')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="gender" :value="__('Gender')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <select 
                            id="gender" 
                            name="gender" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none"
                            required
                        >
                            <option value="" disabled {{ old('gender', $user->gender) ? '' : 'selected' }}>Select gender</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            <option value="prefer_not_to_say" {{ old('gender', $user->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('gender')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="civil_status" :value="__('Civil Status')" class="text-sm font-medium text-gray-700" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <select 
                            id="civil_status" 
                            name="civil_status" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none"
                            required
                        >
                            <option value="" disabled {{ old('civil_status', $user->civil_status) ? '' : 'selected' }}>Select status</option>
                            <option value="single" {{ old('civil_status', $user->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('civil_status', $user->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('civil_status', $user->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('civil_status', $user->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                            <option value="divorced" {{ old('civil_status', $user->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('civil_status')" />
                </div>
            </div>
        </div>

        <!-- Occupation & Address -->
        <div class="space-y-6 pt-4 border-t border-gray-200">
            <h4 class="text-base font-medium text-gray-900">Occupation & Address</h4>
            
            <!-- Occupation -->
            <div class="space-y-2">
                <x-input-label for="occupation" :value="__('Occupation')" class="text-sm font-medium text-gray-700" />
                <div class="relative">
                    <x-text-input 
                        id="occupation" 
                        name="occupation" 
                        type="text" 
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        :value="old('occupation', $user->occupation)" 
                        placeholder="e.g., Teacher, Engineer, Business Owner"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                        </svg>
                    </div>
                </div>
                <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('occupation')" />
            </div>

            <!-- Address -->
            <div class="space-y-6 pt-6 border-t border-gray-200">
                <div>
                    <h4 class="text-base font-medium text-gray-900">Address</h4>
                    <p class="mt-1 text-sm text-gray-500">Update your complete address</p>
                </div>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <x-input-label for="address" :value="__('Complete Address')" class="text-sm font-medium text-gray-700" />
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute top-3 left-3">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <textarea 
                                id="address" 
                                name="address" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" 
                                rows="3"
                                required
                            >{{ old('address', $user->address) }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Please include house number, street, barangay, city/municipality, and province</p>
                        <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('address')" />
                    </div>
                    
                    <div class="space-y-2">
                        <x-input-label for="purok_id" :value="__('Purok')" class="text-sm font-medium text-gray-700" />
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                            <select 
                                id="purok_id" 
                                name="purok_id" 
                                class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none"
                            >
                                <option value="" disabled {{ !old('purok_id', $user->purok_id) ? 'selected' : '' }}>Select your purok</option>
                                @foreach(\App\Models\Purok::all() as $purok)
                                    <option value="{{ $purok->id }}" {{ old('purok_id', $user->purok_id) == $purok->id ? 'selected' : '' }}>{{ $purok->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('purok_id')" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back
            </a>
            <button type="submit" class="ml-3 inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Save Changes
            </button>
        </div>
            <div id="form-message" class="mt-3 text-sm text-gray-600"></div>
        </div>
        
        <!-- Add JavaScript for form submission -->
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('profile-information-form');
                const submitButton = form.querySelector('button[type="submit"]');
                const buttonText = submitButton.querySelector('span:not(.spinner)');
                const spinner = document.createElement('span');
                
                // Create and append spinner
                spinner.className = 'spinner hidden';
                spinner.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
                submitButton.insertBefore(spinner, submitButton.firstChild);
                
                form.addEventListener('submit', function(e) {
                    // Only show loading state for form submission, not validation
                    if (form.checkValidity()) {
                        submitButton.disabled = true;
                        submitButton.classList.add('opacity-75', 'cursor-not-allowed');
                        spinner.classList.remove('hidden');
                        buttonText.textContent = 'Saving...';
                    }
                });
                
                // Show success message if there's a success message in session
                @if (session('status') === 'profile-updated')
                    showMessage('Profile updated successfully!', 'success');
                @endif
                
                // Handle form submission errors
                @if($errors->any())
                    scrollToFirstError();
                @endif
                
                function showMessage(message, type) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `mb-4 p-4 rounded-md ${type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}`;
                    messageDiv.innerHTML = `
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 ${type === 'success' ? 'text-green-400' : 'text-red-400'}" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">${message}</p>
                            </div>
                        </div>
                    `;
                    
                    const formContainer = form.parentElement;
                    formContainer.insertBefore(messageDiv, form);
                    
                    // Auto-hide after 5 seconds
                    setTimeout(() => {
                        messageDiv.style.opacity = '0';
                        setTimeout(() => messageDiv.remove(), 300);
                    }, 5000);
                }
                
                function scrollToFirstError() {
                    const firstError = document.querySelector('.text-red-600');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        </script>
        @endpush
    </form>
