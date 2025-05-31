@extends('layouts.guest')

@section('content')
    <!-- Logo flush with box, centered -->
    <div class="flex justify-center -mt-4 mb-1">
        <img src="{{ asset('images/Kal2Logo.png') }}" alt="Logo" class="h-24 w-24 rounded-full bg-white p-1 shadow">
    </div>

    <h2 class="text-2xl font-semibold text-center text-green-700 mb-5">Register</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
            <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('contact_number')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="purok_id" class="block text-sm font-medium text-gray-700">Purok</label>
            <select id="purok_id" name="purok_id" required class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
                <option value="" disabled selected>Select your purok</option>
                @foreach ($puroks as $purok)
                    <option value="{{ $purok->id }}" {{ old('purok_id') == $purok->id ? 'selected' : '' }}>
                        {{ $purok->name }}
                    </option>
                @endforeach
            </select>
            @error('purok_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div class="mb-3">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col items-center gap-3 mt-5">
            <a href="{{ route('login') }}" class="text-green-700 text-sm hover:underline">
                Already registered?
            </a>
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded w-full">
                Register
            </button>
        </div>
    </form>
@endsection
