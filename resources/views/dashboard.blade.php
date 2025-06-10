@extends('layouts.app')

@php
// Debug session data
$showDebug = false;
if ($showDebug && auth()->check()) {
    echo '<!-- Debug: Session Data -->';
    echo '<!-- show_feedback_prompt: ' . (session('show_feedback_prompt') ? 'true' : 'false') . ' -->';
    echo '<!-- pending_feedback: ' . (session('pending_feedback') ? json_encode(session('pending_feedback')) : 'none') . ' -->';
    echo '<!-- feedback_submitted cookie: ' . (request()->cookie('feedback_submitted') ? 'true' : 'false') . ' -->';
    echo '<!-- feedback_skipped cookie: ' . (request()->cookie('feedback_skipped') ? 'true' : 'false') . ' -->';
}
@endphp

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-100">
    <div class="w-full">
        @if(auth()->user()->role === 'resident')
            @if(auth()->user()->rejected_at)
                <div class="bg-red-200 border-l-4 border-red-600 text-gray-900 p-4 dark:bg-red-500 dark:border-red-700 dark:text-white">

                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 dark:text-red-200">
                                Your account has been rejected. 
                                @if(auth()->user()->rejection_reason)
                                    <span class="font-bold">Reason: {{ auth()->user()->rejection_reason }}</span>
                                @endif
                                Please contact your purok president for assistance.
                            </p>
                            <div class="mt-4">
                                <form id="delete-account-form" action="{{ route('account.destroy') }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            onclick="confirmDelete()" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Delete My Account
                                    </button>
                                </form>
                            </div>
                            <script>
                                function confirmDelete() {
                                    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                                        document.getElementById('delete-account-form').submit();
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            @elseif(!auth()->user()->is_approved)
                <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900 dark:border-yellow-600">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                Your account is pending approval. You won't be able to submit requests or report incidents until your account is approved by your purok leader.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
@php
if (!function_exists('formatStatus')) {
    function formatStatus($status) {
        if (empty($status)) {
            return '';
        }
        return ucwords(str_replace('_', ' ', $status));
    }
}
@endphp

<div class="w-full">
    <div class="w-full">
        <!-- This empty div ensures consistent spacing with the navigation bar -->
        <div class="h-16"></div>
        <!-- Header with Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-semibold text-black dark:text-black">Dashboard</h1>
                @if(auth()->user()->role === 'resident')
                    @if(auth()->user()->is_approved)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Active
                        </span>
                    @elseif(auth()->user()->rejected_at)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Rejected
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Pending Approval
                        </span>
                    @endif
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                @php
                    $isResident = auth()->user()->role === 'resident';
                    $isApproved = $isResident && auth()->user()->is_approved;
                    $isRejected = $isResident && auth()->user()->rejected_at;
                    $isDisabled = $isResident && !$isApproved;
                    
                    // Button base classes
                    $buttonClasses = 'inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150';
                    $primaryButtonClasses = 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 border border-transparent';
                    $secondaryButtonClasses = 'text-gray-700 bg-white hover:bg-gray-50 focus:ring-blue-500 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600';
                    $disabledClasses = 'opacity-50 cursor-not-allowed';
                    
                    // Tooltips
                    $tooltipMessage = $isRejected ? 'Your account has been rejected. Please contact the barangay office for assistance.' : 'Your account is pending approval';
                @endphp

                @if($isResident && !$isApproved)
                    <!-- New Request Button with Tooltip -->
                    <div class="relative group">
                        <button class="{{ $buttonClasses }} {{ $disabledClasses }} bg-blue-600 text-white w-full sm:w-auto" disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            New Request
                        </button>
                        <div class="absolute z-10 invisible group-hover:visible bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap">
                            {{ $tooltipMessage }}
                            <div class="absolute w-2 h-2 -bottom-1 left-1/2 transform -translate-x-1/2 rotate-45 bg-gray-900"></div>
                        </div>
                    </div>

                    <!-- Report Incident Button with Tooltip -->
                    <div class="relative group">
                        <button class="{{ $buttonClasses }} {{ $disabledClasses }} bg-white text-gray-700 border border-gray-300 w-full sm:w-auto" disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Report Incident
                        </button>
                        <div class="absolute z-10 invisible group-hover:visible bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap">
                            {{ $tooltipMessage }}
                            <div class="absolute w-2 h-2 -bottom-1 left-1/2 transform -translate-x-1/2 rotate-45 bg-gray-900"></div>
                        </div>
                    </div>
                @else
                    <!-- Active Buttons for Approved Users -->
                    <a href="{{ route('requests.create') }}" class="{{ $buttonClasses }} {{ $primaryButtonClasses }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Request
                    </a>
                    <a href="{{ route('incident_reports.create') }}" class="{{ $buttonClasses }} {{ $secondaryButtonClasses }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Report Incident
                    </a>
                @endif
            </div>
        </div>

        @if(isset($error))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-200">
            <p class="font-bold">Error</p>
            <p>{{ $error }}</p>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Pending Requests Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium">Pending Requests</h3>
                                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $pendingRequestsCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Requests Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium">Completed Requests</h3>
                                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $completedRequestsCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Incident Reports Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium">Pending Incident Reports</h3>
                                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $pendingIncidentsCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resolved Incidents Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-blue-200 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium">Resolved Incidents</h3>
                                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $resolvedIncidentsCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Recent Activity - Spans 2 columns -->
            <div class="h-full flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl ml-0">
                <div class="p-6 pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Activity</h3>
                    </div>
                </div>
                <div class="px-6 pb-6 flex-1">
                    <div class="space-y-4">
                        @forelse($recentActivity as $activity)
                            @php
                                $status = strtolower($activity->status);
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                ];

                                $iconClasses = [
                                    'pending' => 'text-yellow-500',
                                    'in_progress' => 'text-blue-500',
                                    'completed' => 'text-green-500',
                                    'resolved' => 'text-green-500',
                                    'rejected' => 'text-red-500',
                                    'cancelled' => 'text-gray-500',
                                    'default' => 'text-gray-500',
                                ];

                                $statusClass = $statusClasses[$status] ?? $statusClasses['default'];
                                $iconClass = $iconClasses[$status] ?? $iconClasses['default'];
                                
                                // Set icon based on activity type
                                $icon = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'; // Default clock icon
                                if ($activity->type === 'incident') {
                                    $icon = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Warning icon
                                } elseif ($activity->type === 'request') {
                                    $icon = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'; // Document icon
                                }
                            @endphp
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150 shadow-sm mb-3 last:mb-0 border border-gray-100 dark:border-gray-600">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $iconClass }} dark:bg-opacity-20">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="ml-4 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $activity->title }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $activity->formatted_status }}
                                    </span>
                                </div>
                                <div class="ml-2">
                                    <a href="{{ $activity->type === 'request' ? route('requests.show', $activity->id) : route('incident_reports.show', $activity->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">No recent activity</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Your recent requests and incident reports will appear here.
                                </p>
                                @if(auth()->user()->is_approved)
                                    <div class="mt-6">
                                        <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            New Request
                                        </a>
                                        <a href="{{ route('incident_reports.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Report Incident
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 2-Column Layout for Recent Requests and Incidents -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Requests -->
                <div class="h-full flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl ml-0">
                <div class="p-6 pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Barangay Requests</h3>
                        @if(auth()->user()->is_approved || auth()->user()->role !== 'resident')
                            <a href="{{ route('requests.my_requests') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                        @endif
                    </div>
                </div>
                <div class="px-6 pb-6 flex-1">
                    @if($recentRequests->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No recent requests found</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentRequests as $request)
                                @php
                                    $status = strtolower($request->status);
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    ][$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <div class="flex items-center p-4 bg-white dark:bg-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 shadow-sm mb-3 last:mb-0 border border-gray-100 dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300 dark:bg-opacity-20">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0 relative">
                                        <div class="absolute right-0 top-0">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                                {{ $request->formatted_status }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white pr-16">
                                            <a href="{{ route('requests.show', $request->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $request->purpose }}
                                            </a>
                                        </p>
                                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                                            {{ $request->request_type ?? 'Barangay Request' }}
                                        </p>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                                            {{ $request->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($recentRequests->count() > 5)
                            <div class="mt-4 text-right">
                                <a href="{{ route('requests.my_requests') }}" class="text-sm font-medium text-blue-600 hover:underline">View all requests</a>
                            </div>
                        @endif
                    @endif
                </div>
                </div>

                <!-- Recent Incidents -->
                <div class="h-full flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl mr-0">
                <div class="p-6 pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Incident Reports</h3>
                        @if(auth()->user()->is_approved)
                            <a href="{{ route('incident_reports.my_reports') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                        @endif
                    </div>
                </div>
                <div class="px-6 pb-6 flex-1">
                    @if($recentIncidents->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No recent incidents found</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentIncidents as $incident)
                                @php
                                    $status = strtolower($incident->status);
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                    ][$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    
                                    $iconClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'resolved' => 'text-green-600 bg-green-100',
                                        'default' => 'text-gray-600 bg-gray-100'
                                    ];
                                    $iconClass = $iconClasses[$status] ?? $iconClasses['default'];
                                @endphp
                                <!-- Debug: {{ json_encode(['incident_type' => $incident->incident_type, 'all' => $incident->toArray()]) }} -->
                                <div class="flex items-center p-4 bg-white dark:bg-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 shadow-sm mb-3 last:mb-0 border border-gray-100 dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $iconClass }} dark:bg-opacity-20">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0 relative">
                                        <div class="absolute right-0 top-0">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                                {{ format_label($incident->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white pr-16">
                                            <a href="{{ route('incident_reports.show', $incident->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $incident->description }}
                                            </a>
                                        </p>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">
                                                {{ \App\Models\IncidentReport::TYPES[$incident->incident_type] ?? ucfirst($incident->incident_type) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                                            {{ $incident->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($recentIncidents->count() > 5)
                            <div class="mt-4 text-right">
                                <a href="{{ route('incident_reports.index') }}" class="inline-block px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                                    View all incidents
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Feedback Prompt -->
    @auth
        <x-feedback-prompt />
    @endauth
@endsection