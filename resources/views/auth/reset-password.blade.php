@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <div class="flex justify-center -mt-4 mb-4">
        <img src="{{ asset('images/Kal2Logo.png') }}" alt="Logo" class="h-20 w-20 rounded-full bg-white p-1 shadow">
    </div>

    <h2 class="text-2xl font-semibold text-center text-green-700 mb-4">Reset Password</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input id="password" type="password" name="password" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
            Reset Password
        </button>
    </form>
@endsection
