<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-600">
        <h3 class="text-lg font-semibold text-white">Update Profile Information</h3>
        <p class="text-sm text-indigo-100 mt-1">Update your account's profile information and contact details.</p>
    </div>
    <div class="p-6">
    
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There {{ $errors->count() > 1 ? 'are' : 'is' }} {{ $errors->count() }} {{ str('error')->plural($errors->count()) }} with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <form method="post" action="{{ route('profile.update') }}" style="margin-top: 20px;">
        @csrf
        @method('patch')
        <input type="hidden" name="_previous_url" value="{{ url()->current() }}">

        <!-- Name Fields -->
        <div class="space-y-6">
            <h4 class="text-base font-medium text-gray-900">Personal Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <x-input-label for="first_name" :value="__('First Name')" class="text-sm font-medium text-black" />
                    <div class="relative">
                        <x-text-input 
                            id="first_name" 
                            name="first_name" 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                            :value="old('first_name', $user->first_name)" 
                            required 
                            autofocus 
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('first_name')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="middle_name" :value="__('Middle Name')" class="text-sm font-semibold text-black" />
                    <x-text-input 
                        id="middle_name" 
                        name="middle_name" 
                        type="text" 
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        :value="old('middle_name', $user->middle_name)" 
                    />
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('middle_name')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="last_name" :value="__('Last Name')" class="text-sm font-semibold text-black" />
                    <x-text-input 
                        id="last_name" 
                        name="last_name" 
                        type="text" 
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        :value="old('last_name', $user->last_name)" 
                        required 
                    />
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('last_name')" />
                </div>
            
        </div>

        <!-- Contact Information -->
        <div class="space-y-6 pt-4 border-t border-gray-200">
            <h4 class="text-base font-medium text-gray-900">Contact Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <x-input-label for="contact_number" :value="__('Contact Number')" class="text-sm font-semibold text-black" />
                    <div class="relative">
                        <x-text-input 
                            id="contact_number" 
                            name="contact_number" 
                            type="tel" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                            :value="old('contact_number', $user->contact_number)" 
                            placeholder="+63 912 345 6789"
                            required 
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('contact_number')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-black" />
                    <div class="relative">
                        <x-text-input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                            :value="old('email', $user->email)" 
                            required 
                            autocomplete="username"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('email')" />

                    @php $authUser = auth()->user(); @endphp
                    @if ($authUser instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $authUser->hasVerifiedEmail())
                        <div class="mt-2 p-3 bg-yellow-50 border-l-4 border-yellow-400">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        {{ __('Your email address is unverified.') }}
                                        <button form="send-verification" class="font-medium underline text-yellow-600 hover:text-yellow-500">
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
        <div class="space-y-6 pt-4 border-t border-gray-200">
            <h4 class="text-base font-medium text-gray-900">Personal Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <x-input-label for="birth_date" :value="__('Date of Birth')" class="text-sm font-semibold text-black" />
                    <div class="relative">
                        <x-text-input 
                            id="birth_date" 
                            name="birth_date" 
                            type="date" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                            :value="old('birth_date', $user->birth_date?->format('Y-m-d'))" 
                            required 
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('birth_date')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="gender" :value="__('Gender')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <select 
                            id="gender" 
                            name="gender" 
                            class="appearance-none block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                        >
                            <option value="" disabled {{ !old('gender', $user->gender) ? 'selected' : '' }}>Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('gender')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="civil_status" :value="__('Civil Status')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <select 
                            id="civil_status" 
                            name="civil_status" 
                            class="appearance-none block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                        >
                            <option value="" disabled {{ !old('civil_status', $user->civil_status) ? 'selected' : '' }}>Select Status</option>
                            <option value="single" {{ old('civil_status', $user->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('civil_status', $user->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('civil_status', $user->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('civil_status', $user->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                            <option value="divorced" {{ old('civil_status', $user->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0116 8H4a5 5 0 014.5 7.5 6.96 6.96 0 00-1.5 4.33c0 .34.024.673.07 1h9.86z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
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
            <div class="space-y-4">
                <div class="space-y-2">
                    <x-input-label for="address" :value="__('Complete Address')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <textarea 
                            id="address" 
                            name="address" 
                            rows="3" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="House/Unit No., Street, Barangay, City/Municipality, Province"
                        >{{ old('address', $user->address) }}</textarea>
                        <div class="absolute top-3 left-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('address')" />
                </div>
                
                <div class="space-y-2">
                    <x-input-label for="purok_id" :value="__('Purok')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <select 
                            id="purok_id" 
                            name="purok_id" 
                            class="appearance-none block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                        >
                            <option value="" disabled {{ !old('purok_id', $user->purok_id) ? 'selected' : '' }}>Select your purok</option>
                            @foreach(\App\Models\Purok::all() as $purok)
                                <option value="{{ $purok->id }}" {{ old('purok_id', $user->purok_id) == $purok->id ? 'selected' : '' }}>
                                    {{ $purok->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('purok_id')" />
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-gray-200">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="relative inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                    id="submit-button"
                >
                    <span id="button-text">Save Changes</span>
                    <span id="button-spinner" class="hidden ml-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
            <div id="form-message" class="mt-3 text-sm text-gray-600"></div>
        </div>
        
        <!-- Add JavaScript for form submission -->
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const submitButton = document.getElementById('submit-button');
                const buttonText = document.getElementById('button-text');
                const buttonSpinner = document.getElementById('button-spinner');
                const formMessage = document.getElementById('form-message');
                
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    buttonText.textContent = 'Saving...';
                    buttonSpinner.classList.remove('hidden');
                    submitButton.disabled = true;
                    formMessage.textContent = '';
                    formMessage.className = 'mt-3 text-sm text-gray-600';
                    
                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            // Show success message
                            formMessage.textContent = data.message || 'Profile updated successfully!';
                            formMessage.className = 'mt-3 text-sm text-green-600';
                            
                            // Redirect after a short delay
                            setTimeout(() => {
                                window.location.href = data.redirect || '{{ route("profile.edit") }}';
                            }, 1000);
                        } else {
                            // Handle validation errors
                            if (data.errors) {
                                let errorMessages = [];
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    errorMessages = errorMessages.concat(messages);
                                }
                                formMessage.innerHTML = errorMessages.map(msg => `<div>${msg}</div>`).join('');
                                formMessage.className = 'mt-3 text-sm text-red-600';
                                scrollToFirstError();
                            } else {
                                formMessage.textContent = data.message || 'An error occurred while updating your profile.';
                                formMessage.className = 'mt-3 text-sm text-red-600';
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        formMessage.textContent = 'An unexpected error occurred. Please try again.';
                        formMessage.className = 'mt-3 text-sm text-red-600';
                    } finally {
                        // Reset button state
                        buttonText.textContent = 'Save Changes';
                        buttonSpinner.classList.add('hidden');
                        submitButton.disabled = false;
                    }
                });
                
                // Handle form submission errors (this would be triggered by your form submission logic)
                @if($errors->any())
                    scrollToFirstError();
                @endif
                
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
</div>
