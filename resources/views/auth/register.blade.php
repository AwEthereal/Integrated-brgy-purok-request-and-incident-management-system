@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('images/Kal2Logo.png') }}" alt="Logo" class="h-20 w-20 rounded-full bg-white p-1 shadow-md mx-auto">
        <h2 class="mt-2 text-xl font-bold text-gray-900">Create an Account</h2>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name Fields -->
        <div class="flex flex-col gap-3">
            {{-- First Name --}}
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            {{-- Middle and Last Names --}}
            <div>
                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                @error('middle_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                    <input id="contact_number" type="tel" name="contact_number" value="{{ old('contact_number') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="09XXXXXXXXX">
                    @error('contact_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Personal Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                    <select id="civil_status" name="civil_status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="" disabled selected>Select Status</option>
                        <option value="single" {{ old('civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="widowed" {{ old('civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="separated" {{ old('civil_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                        <option value="divorced" {{ old('civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                    </select>
                    @error('civil_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                    @php
                        $minDate = now()->subYears(100)->format('Y-m-d');
                        $maxDate = now()->subYears(18)->format('Y-m-d');
                    @endphp
                    <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        min="{{ $minDate }}" max="{{ $maxDate }}">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Complete Address <span class="text-red-500">*</span></label>
                <textarea id="address" name="address" rows="2" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="House No., Street, Barangay, City/Municipality, Province">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Purok Dropdown -->
            <div>
                <label for="purok_id" class="block text-sm font-medium text-gray-700 mb-1">Purok <span class="text-red-500">*</span></label>
                <select id="purok_id" name="purok_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="" disabled selected>Select Purok</option>
                    @foreach ($puroks as $purok)
                        <option value="{{ $purok->id }}" {{ old('purok_id') == $purok->id ? 'selected' : '' }}>
                            {{ $purok->name }}
                        </option>
                    @endforeach
                </select>
                @error('purok_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">Occupation (Optional)</label>
                <input id="occupation" type="text" name="occupation" value="{{ old('occupation') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="e.g. Teacher, Engineer, Student, etc.">
                @error('occupation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        autocomplete="new-password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-md transition duration-150 ease-in-out">
                Create Account
            </button>
        </div>

        <div class="text-center mt-4 text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                Sign in here
            </a>
        </div>
    </form>
@endsection
