@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Back Button -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Profile</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">View detailed user information</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                            <span class="text-blue-600 font-bold text-2xl">
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'N', 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-blue-100">{{ format_label($user->role) }}</p>
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
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Purok</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->purok->name ?? 'Not Assigned' }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700"></div>

            <!-- Account Information -->
            <div class="px-6 py-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Account Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">User ID</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Role</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ format_label($user->role) }}</p>
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
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Registered On</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $user->created_at->format('F d, Y') }}
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                ({{ $user->created_at->diffForHumans() }})
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $user->updated_at->format('F d, Y') }}
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                ({{ $user->updated_at->diffForHumans() }})
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
