@extends('layouts.guest')

@section('content')
    <div class="flex justify-center -mt-4 mb-4">
        <img src="{{ asset('images/Kal2Logo.png') }}" alt="Logo" class="h-20 w-20 rounded-full bg-white p-1 shadow">
    </div>

    <h2 class="text-2xl font-semibold text-center text-green-700 mb-4">Forgot Password</h2>

    @if (session('status'))
        <p class="mb-3 text-sm text-green-600">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full mt-1 border border-gray-300 rounded px-3 py-2">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
            Send Password Reset Link
        </button>
    </form>
@endsection
