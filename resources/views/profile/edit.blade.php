@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                Profile Settings
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage your personal information and account settings
            </p>
        </div>

        @if(isset($user))
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <!-- Content Header -->
                <div class="px-4 sm:px-6 py-5 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center w-full sm:w-auto">
                            @if($user->profile_photo_path)
                                <img class="w-16 h-16 rounded-full object-cover mr-4 flex-shrink-0" 
                                    src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                                    alt="{{ $user->name }}">
                            @else
                                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-xl font-semibold mr-4 flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-medium text-gray-900 truncate">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto sm:ml-auto">
                            <a href="{{ route('profile.password.edit') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 min-h-[44px]">
                                <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5a.75.75 0 01.75.75v6.75a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75v-6.75a.75.75 0 01.75-.75z" />
                                </svg>
                                <span class="whitespace-nowrap">Change Password</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="p-6">
                    <div>
                        @if(session('status') === 'profile-updated')
                            <div class="bg-green-50 border-l-4 border-green-400 p-4 m-6 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-700">
                                            Profile updated successfully!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 m-6 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-700">
                                            There {{ $errors->count() === 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} with your submission
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

                        <!-- Profile Update Form -->
                        <div class="px-6 py-6">
                            @include('profile.partials.update-profile-information-form')
                            

                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-red-600 mt-6">
                No user data available.
            </div>
        @endif
    </div>
</div>
@endsection
