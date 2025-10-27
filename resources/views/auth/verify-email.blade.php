@extends('layouts.guest')

@section('title', 'Verify Email')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resendButton = document.getElementById('resend-button');
        const timerElement = document.getElementById('resend-timer');
        let countdown = 60; // 1 minute cooldown
        
        function updateTimer() {
            if (countdown <= 0) {
                resendButton.disabled = false;
                timerElement.classList.add('hidden');
                resendButton.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }
            
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;
            timerElement.textContent = `Resend available in ${minutes}:${seconds.toString().padStart(2, '0')}`;
            countdown--;
            setTimeout(updateTimer, 1000);
        }
        
        if (resendButton) {
            resendButton.addEventListener('click', function() {
                resendButton.disabled = true;
                resendButton.classList.add('opacity-50', 'cursor-not-allowed');
                timerElement.classList.remove('hidden');
                countdown = 60; // Reset countdown
                updateTimer();
            });
            
            // Start the timer on page load
            updateTimer();
        }
    });
</script>
@endpush

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900 p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-green-600 dark:bg-green-700 p-6 text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/Kal2Logo.png') }}" alt="Logo" class="h-16 w-16 rounded-full bg-white p-1">
            </div>
            <h1 class="text-2xl font-bold text-white">
                {{ __('Verify Your Email') }}
            </h1>
        </div>

        <!-- Content -->
        <div class="p-6">
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-0.5">
                        <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Check your email</h3>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300 space-y-2">
                            <p>We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.</p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Didn't receive the email? Check your spam folder or request a new link below.</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="space-y-3">
                        <div>
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button
                                    id="resend-button"
                                    type="submit"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150"
                                >
                                    Resend Verification Email
                                </button>
                            </form>
                            <p id="resend-timer" class="mt-2 text-center text-sm text-gray-500 dark:text-gray-400"></p>
                        </div>
                        
                        <div class="text-center">
                            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none transition-colors duration-150">
                                    {{ __('Sign out') }}
                                </button>
                            </form>
                            <span class="mx-2 text-gray-400">•</span>
                            <a href="{{ url('/') }}" class="text-sm text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300 transition-colors duration-150">
                                Return to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
