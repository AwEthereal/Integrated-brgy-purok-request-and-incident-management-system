@extends('layouts.guest')

@section('content')
    <!-- Logo flush with box, centered -->
    <div class="flex justify-center -mt-4 mb-1">
        <img src="{{ asset('images/Kal2Logo.png') }}" alt="Logo" class="h-24 w-24 rounded-full bg-white p-1 shadow" />
    </div>

    <h2 class="text-2xl font-semibold text-center text-green-700 mb-5">Log in to your account</h2>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2" autocomplete="username">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2" autocomplete="current-password">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center mb-4">
            <input id="remember_me" type="checkbox" name="remember" class="rounded text-green-600 border-gray-300 focus:ring-green-500" />
            <label for="remember_me" class="ml-2 text-sm text-gray-700 select-none">Remember me</label>
        </div>

        <div class="flex flex-col items-center gap-3 mt-5">
            <a href="{{ route('password.request') }}" class="text-green-700 text-sm hover:underline">
                Forgot your password?
            </a>

            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded w-full">
                Log in
            </button>

            <a href="{{ route('register') }}" class="text-green-700 text-sm hover:underline">
                Don't have an account? Register
            </a>
        </div>
    </form>
@endsection
