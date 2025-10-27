@extends('layouts.app')

@section('title', 'Update Password')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.querySelector('input[name="password"]');
            const confirmPassword = document.querySelector('input[name="password_confirmation"]');
            const confirmIcon = document.getElementById('confirm-icon');
            
            function checkPasswordsMatch() {
                if (confirmPassword.value === '') {
                    confirmIcon.classList.remove('text-green-500');
                    confirmIcon.classList.add('text-gray-400');
                } else if (newPassword.value === confirmPassword.value) {
                    confirmIcon.classList.remove('text-gray-400');
                    confirmIcon.classList.add('text-green-500');
                } else {
                    confirmIcon.classList.remove('text-green-500');
                    confirmIcon.classList.add('text-gray-400');
                }
            }

            // Check on input for both fields
            newPassword.addEventListener('input', checkPasswordsMatch);
            confirmPassword.addEventListener('input', checkPasswordsMatch);
            
            // Initial check in case of autofill
            checkPasswordsMatch();
        });
    </script>
@endpush

@push('styles')
    <style>
        .password-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 1.5rem 2rem;
            color: white;
            border-radius: 0.5rem 0.5rem 0 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .password-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.2s;
            font-size: 0.9rem;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            background: rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        .back-btn:hover {
            color: white;
            transform: translateX(-2px);
            background: rgba(255, 255, 255, 0.15);
        }

        .back-btn svg {
            margin-right: 0.4rem;
            transition: transform 0.2s;
        }

        .back-btn:hover svg {
            transform: translateX(-2px);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="password-header">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div class="mr-0">
                                <h1 class="text-2xl font-bold flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Update Password
                                </h1>
                                <p class="text-white text-sm mt-1 ml-2">Secure your account with a new password</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="px-6 py-6">
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        {{ session('status') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        There
                                        {{ $errors->count() === 1 ? 'was an error' : 'were ' . $errors->count() . ' errors' }}
                                        with your submission
                                    </h3>
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

                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                        @csrf

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                Current Password
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" name="current_password" id="current_password" required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md bg-white shadow-sm transition duration-150 ease-in-out"
                                    placeholder="Enter your current password">
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                New Password
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2h-4a2 2 0 01-2-2V7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 7a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7z" />
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md bg-white shadow-sm transition duration-150 ease-in-out"
                                    placeholder="Enter your new password">
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm New Password
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg id="password-match-icon" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-3 sm:text-sm border-gray-300 rounded-md bg-white shadow-sm transition duration-150 ease-in-out"
                                    placeholder="Confirm your new password">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Password must be at least 8 characters long and include a mix of letters, numbers, and
                                symbols.
                            </p>
                        </div>
                        
                        <div class="flex justify-end pt-6">
                            <a href="{{ route('dashboard') }}"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Password
                            </button>
                        </div>
                    </form>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const password = document.getElementById('password');
                            const confirmPassword = document.getElementById('password_confirmation');
                            const passwordMatchIcon = document.getElementById('password-match-icon');
                            
                            function checkPasswordsMatch() {
                                if (confirmPassword.value === '') {
                                    passwordMatchIcon.classList.remove('text-green-500');
                                    passwordMatchIcon.classList.add('text-gray-400');
                                } else if (password.value === confirmPassword.value) {
                                    passwordMatchIcon.classList.remove('text-gray-400');
                                    passwordMatchIcon.classList.add('text-green-500');
                                } else {
                                    passwordMatchIcon.classList.remove('text-green-500');
                                    passwordMatchIcon.classList.add('text-gray-400');
                                }
                            }
                            
                            password.addEventListener('input', checkPasswordsMatch);
                            confirmPassword.addEventListener('input', checkPasswordsMatch);
                            
                            // Initial check in case of autofill
                            checkPasswordsMatch();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection