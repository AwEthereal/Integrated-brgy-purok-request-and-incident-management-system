@extends('layouts.app')

@section('title', 'Purok Leader Profile')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Back Button -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Purok Leader Profile</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">View detailed purok leader information</p>
            </div>
            <a href="{{ route('reports.purok-leaders') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                            <span class="text-purple-600 font-bold text-2xl">
                                {{ strtoupper(substr($user->first_name ?? 'P', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'L', 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-purple-100">Purok President</p>
                        <p class="text-purple-100 text-sm mt-1">{{ $user->purok->name ?? 'No Purok Assigned' }}</p>
                        <div class="mt-2">
                            @if($user->is_approved)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">
                                    ✓ Approved
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-500 text-white">
                                    ⏳ Pending Approval
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="px-6 py-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">First Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->first_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Middle Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->middle_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->last_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Suffix</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->suffix ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') : 'N/A' }}
                            @if($user->birth_date)
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                    ({{ \Carbon\Carbon::parse($user->birth_date)->age }} years old)
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Gender</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->gender ? ucfirst($user->gender) : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700"></div>

            <!-- Contact Information -->
            <div class="px-6 py-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Contact Number</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->contact_number ?? 'N/A' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Purok</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-semibold text-lg">
                            {{ $user->purok->name ?? 'Not Assigned' }}
                        </p>
                        @if($user->purok)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->purok->description ?? '' }}
                            </p>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700"></div>

            <!-- Leadership Information -->
            <div class="px-6 py-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Leadership Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Position</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">Purok President</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($user->is_approved)
                                <span class="text-green-600 dark:text-green-400 font-semibold">Approved</span>
                            @else
                                <span class="text-yellow-600 dark:text-yellow-400 font-semibold">Pending Approval</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($user->email_verified_at)
                                <span class="text-green-600 dark:text-green-400 font-semibold">Yes</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                    ({{ \Carbon\Carbon::parse($user->email_verified_at)->format('M d, Y') }})
                                </span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-semibold">No</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Appointed On</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $user->created_at->format('F d, Y') }}
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                ({{ $user->created_at->diffForHumans() }})
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
