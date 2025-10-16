@extends('layouts.app')

@section('title', 'Dashboard')

@push('scripts')
    @vite(['resources/js/resident-notifications.js'])
@endpush

@push('styles')
<style>
    .dashboard-container {
        min-height: calc(100vh - 4rem);
        padding: 1.5rem 0;
    }
    .dashboard-header {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .dashboard-card {
        height: 100%;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        border: 1px solid #e5e7eb;
        margin-top: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .dashboard-card:hover a {
        text-decoration: none;
    }
    .dashboard-card:hover a:hover {
        text-decoration: underline;
    }
    /* Remove colored borders */
    .card-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .card-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .card-title {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    .card-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
        line-height: 1.2;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    @media (max-width: 640px) {
        .dashboard-container {
            padding: 0.75rem;
        }
        .dashboard-header {
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .card-content {
            padding: 0.875rem;
        }
        .card-value {
            font-size: 1.25rem;
        }
        .card-icon {
            width: 2.5rem;
            height: 2.5rem;
        }
        /* Improve touch targets for mobile */
        .mobile-touch-target {
            min-height: 44px;
            min-width: 44px;
        }
        /* Better spacing for mobile cards */
        .dashboard-card {
            margin-top: 0.75rem;
        }
        /* Mobile-specific activity card adjustments */
        .activity-card-mobile {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .activity-card-mobile .activity-actions {
            width: 100%;
            margin-top: 0.5rem;
            justify-content: space-between;
        }
        /* Mobile accordion for sections */
        .mobile-accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .mobile-accordion-content.active {
            max-height: 2000px;
            transition: max-height 0.5s ease-in;
        }
    }
</style>
@endpush

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
<!-- Add user ID for real-time notifications -->
<meta name="user-id" content="{{ auth()->id() }}">

<div class="min-h-screen bg-gray-50 resident-dashboard">
    <div class="container mx-auto px-4 py-6 dashboard-container">
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
        
        <div class="space-y-6">
    <!-- Status Messages -->
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

<!-- Hero Section -->
<div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-4 sm:py-5 px-4 sm:px-6 rounded-lg shadow-lg mb-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold">
                        @if(auth()->user()->purok)
                            Purok {{ auth()->user()->purok->name }}
                        @else
                            Dashboard
                        @endif
                    </h1>
                </div>
                <p class="text-green-100 text-sm">Welcome back, {{ auth()->user()->name }}!</p>
                @if(auth()->user()->role === 'resident')
                    <div class="mt-2">
                        @if(auth()->user()->is_approved)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Active
                            </span>
                        @elseif(auth()->user()->rejected_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-white">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                Pending
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 sm:gap-3 w-full sm:w-auto">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2 sm:p-3 text-center">
                    <p class="text-lg sm:text-xl font-bold">{{ $pendingRequestsCount ?? 0 }}</p>
                    <p class="text-xs">Pending</p>
                </div>
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2 sm:p-3 text-center">
                    <p class="text-lg sm:text-xl font-bold">{{ $pendingIncidentsCount ?? 0 }}</p>
                    <p class="text-xs">Incidents</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
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
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <!-- Pending Requests Card -->
    <a href="{{ url('/my-requests?status=pending') }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md p-3 sm:p-4 transition-all duration-200 border border-gray-200 hover:border-yellow-500">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="p-2 rounded-lg bg-yellow-500 text-white flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide truncate">Pending</h3>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $pendingRequestsCount ?? 0 }}</p>
            </div>
        </div>
    </a>

    <!-- Completed Requests Card -->
    <a href="{{ url('/my-requests?status=barangay_approved') }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md p-3 sm:p-4 transition-all duration-200 border border-gray-200 hover:border-green-500">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="p-2 rounded-lg bg-green-500 text-white flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide truncate">Completed</h3>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $completedRequestsCount ?? 0 }}</p>
            </div>
        </div>
    </a>

    <!-- Pending Incident Reports Card -->
    <a href="{{ url('/incident-reports/my_reports?status=pending') }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md p-3 sm:p-4 transition-all duration-200 border border-gray-200 hover:border-orange-500">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="p-2 rounded-lg bg-orange-500 text-white flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide truncate">Incidents</h3>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $pendingIncidentsCount ?? 0 }}</p>
            </div>
        </div>
    </a>

    <!-- Resolved Incidents Card -->
    <a href="{{ url('/incident-reports/my_reports?status=resolved') }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md p-3 sm:p-4 transition-all duration-200 border border-gray-200 hover:border-blue-500">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="p-2 rounded-lg bg-blue-500 text-white flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide truncate">Resolved</h3>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $resolvedIncidentsCount ?? 0 }}</p>
            </div>
        </div>
    </a>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 gap-6">
            <!-- Recent Activity - Spans 2 columns -->
            <div class="h-full flex flex-col overflow-hidden rounded-xl ml-0">
                <div class="bg-green-600 px-6 py-4 rounded-t-xl">
                    <div class="flex justify-between items-center">
                        <button onclick="toggleMobileAccordion('recentActivity')" class="flex items-center justify-between w-full sm:pointer-events-none">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Recent Activity
                            </h3>
                            <svg class="w-5 h-5 text-white transition-transform duration-200 sm:hidden" id="recentActivity-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-b-xl">
                    <div class="mobile-accordion-content active" id="recentActivity-content">
                        <div class="px-6 py-6 flex-1">
                            <div class="space-y-3">
                        @forelse($recentActivity as $activity)
                            @php
                                $status = strtolower($activity->status);
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'purok_approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'barangay_approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
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
                            @php
                                // Show dot based on unread status for requests
                                if ($activity->type === 'request') {
                                    // Show dot if resident hasn't viewed the request since last update
                                    $isUnread = !$activity->last_viewed_at || 
                                               ($activity->updated_at && $activity->last_viewed_at && 
                                                $activity->updated_at->gt($activity->last_viewed_at));
                                    $isRelevantStatus = in_array($activity->status, ['purok_approved', 'barangay_approved', 'rejected']);
                                    $showDot = $isUnread && $isRelevantStatus;
                                } else {
                                    // For incidents, show dot for very recent updates only
                                    $showDot = in_array($activity->status, ['in_progress', 'resolved']) && 
                                               $activity->updated_at >= now()->subHours(2);
                                }
                            @endphp
                            <div class="bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 last:mb-0 hover:bg-gray-600 transition-colors duration-200 relative">
                                @if($showDot)
                                    {{-- Yellow dot indicator for items needing attention --}}
                                    <div class="absolute -top-1 -right-1 z-10">
                                        <span class="relative inline-flex">
                                            <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                            <span class="relative inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                                        </span>
                                    </div>
                                @endif
                                <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                                    <div class="flex items-start sm:items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            @if($activity->type === 'incident')
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-semibold text-white mb-1 line-clamp-2 sm:truncate">
                                                {{ $activity->title }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs text-gray-300">
                                                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full font-medium text-xs {{ $statusClass }}">
                                                    {{ $activity->formatted_status }}
                                                </span>
                                                <span class="hidden sm:inline">•</span>
                                                <span class="hidden sm:inline">{{ ucfirst($activity->type) }}</span>
                                                <span class="hidden sm:inline">•</span>
                                                <span class="text-xs">{{ $activity->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ $activity->type === 'request' ? route('requests.show', $activity->id) : route('incident_reports.show', $activity->id) }}" 
                                       class="flex-shrink-0 p-1.5 sm:p-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors" title="View Details">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-16 w-16 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-3 text-lg font-medium text-gray-300">No recent activity</h3>
                                <p class="mt-1 text-sm text-gray-400">
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
                </div>
            </div>

            <!-- 2-Column Layout for Pending Incidents and Recent Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pending Incidents -->
                @if(isset($pendingIncidents) && $pendingIncidents->isNotEmpty())
                <div class="h-full flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
                    <div class="p-6 pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <button onclick="toggleMobileAccordion('pendingIncidents')" class="flex items-center justify-between w-full sm:pointer-events-none">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pending Incidents</h3>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('barangay.incident_reports.index', ['status' => 'pending']) }}" class="text-sm font-medium text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 hidden sm:block" onclick="event.stopPropagation()">View all</a>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 sm:hidden" id="pendingIncidents-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="mobile-accordion-content active" id="pendingIncidents-content">
                    <div class="px-6 pb-6 flex-1">
                        <div class="space-y-3">
                            @foreach($pendingIncidents as $incident)
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150">
                                    <div class="flex-shrink-0 mt-1">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('barangay.incident_reports.show', $incident->id) }}" class="hover:underline">
                                                {{ $incident->title }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Reported by {{ $incident->user->name ?? 'Unknown' }} • {{ $incident->created_at->diffForHumans() }}
                                        </p>
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            {{ $incident->formatted_status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    </div>
                </div>
                @endif
                
                <!-- Recent Requests -->
                <div class="h-full flex flex-col overflow-hidden rounded-xl">
                    <div class="bg-green-600 px-6 py-4 rounded-t-xl">
                        <div class="flex justify-between items-center">
                            <button onclick="toggleMobileAccordion('recentRequests')" class="flex items-center justify-between w-full sm:pointer-events-none">
                                <h3 class="text-lg font-semibold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Recent Requests
                                </h3>
                                <div class="flex items-center gap-2">
                                    @if(auth()->user()->is_approved)
                                        <a href="{{ route('requests.index') }}" class="text-sm font-medium text-white hover:text-green-100 hidden sm:block" onclick="event.stopPropagation()">View all →</a>
                                    @endif
                                    <svg class="w-5 h-5 text-white transition-transform duration-200 sm:hidden" id="recentRequests-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                </div>
                <div class="bg-gray-800 rounded-b-xl">
                    <div class="mobile-accordion-content active" id="recentRequests-content">
                        <div class="px-6 py-6 flex-1">
                    @if($recentRequests->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">No recent requests found</p>
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
                                <div class="bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 last:mb-0 hover:bg-gray-600 transition-colors duration-200">
                                    <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                                        <div class="flex items-start sm:items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0">
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs sm:text-sm font-semibold text-white mb-1 line-clamp-2 sm:truncate">
                                                    {{ $request->purpose }}
                                                </p>
                                                <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs text-gray-300">
                                                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full font-medium text-xs {{ $statusClass }}">
                                                        {{ $request->formatted_status }}
                                                    </span>
                                                    <span class="hidden sm:inline">•</span>
                                                    <span class="hidden sm:inline">{{ format_label($request->form_type ?? 'Request') }}</span>
                                                    <span class="hidden sm:inline">•</span>
                                                    <span class="text-xs">{{ $request->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('requests.show', $request->id) }}" class="flex-shrink-0 p-1.5 sm:p-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors" title="View Details">
                                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
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
                </div>
            </div>
                
                <!-- Pending Reports -->
                <div class="h-full flex flex-col overflow-hidden rounded-xl mr-0">
                <div class="bg-green-600 px-6 py-4 rounded-t-xl">
                    <div class="flex justify-between items-center">
                        <button onclick="toggleMobileAccordion('pendingReports')" class="flex items-center justify-between w-full sm:pointer-events-none">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Pending Reports
                            </h3>
                            <div class="flex items-center gap-2">
                                @if(auth()->user()->is_approved)
                                    <a href="{{ route('incident_reports.my_reports') }}" class="text-sm font-medium text-white hover:text-green-100 hidden sm:block" onclick="event.stopPropagation()">View all →</a>
                                @endif
                                <svg class="w-5 h-5 text-white transition-transform duration-200 sm:hidden" id="pendingReports-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-b-xl">
                <div class="mobile-accordion-content active" id="pendingReports-content">
                <div class="px-6 py-6 flex-1">
                    @if($pendingReports->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">No pending reports found</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($pendingReports as $report)
                                @php
                                    $status = strtolower($report->status);
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
                                    $isIncident = isset($report->type) ? $report->type === 'incident' : false;
                                    $routeName = $isIncident ? 'incident_reports.show' : 'requests.show';
                                    $typeLabel = $isIncident ? (\App\Models\IncidentReport::TYPES[$report->incident_type] ?? 'Incident') : 'Request';
                                @endphp
                                <div class="bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 last:mb-0 hover:bg-gray-600 transition-colors duration-200">
                                    <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                                        <div class="flex items-start sm:items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0">
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs sm:text-sm font-semibold text-white mb-1 line-clamp-2 sm:truncate">
                                                    {{ $report->description }}
                                                </p>
                                                <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs text-gray-300">
                                                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full font-medium text-xs {{ $statusClass }}">
                                                        {{ format_label($status) }}
                                                    </span>
                                                    <span class="hidden sm:inline">•</span>
                                                    <span class="hidden sm:inline">{{ $typeLabel }}</span>
                                                    <span class="hidden sm:inline">•</span>
                                                    <span class="text-xs">{{ $report->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route($routeName, $report->id) }}" class="flex-shrink-0 p-1.5 sm:p-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors" title="View Details">
                                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($pendingReports->count() > 5)
                            <div class="mt-4 text-right">
                                <a href="{{ route('requests.my_requests') }}" class="inline-block px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                                    View all
                                </a>
                            </div>
                        @endif
                    @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Feedback Prompt -->
    @auth
        <x-feedback-prompt />
    @endauth

@push('scripts')
<script>
    function toggleMobileAccordion(sectionId) {
        // Only work on mobile (screen width < 640px)
        if (window.innerWidth >= 640) return;
        
        const content = document.getElementById(sectionId + '-content');
        const icon = document.getElementById(sectionId + '-icon');
        
        if (content && icon) {
            content.classList.toggle('active');
            icon.classList.toggle('rotate-180');
        }
    }
    
    // Initialize accordion state on page load for mobile
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth < 640) {
            // Start with sections expanded on mobile
            const sections = ['pendingIncidents', 'recentRequests', 'pendingReports', 'recentActivity'];
            sections.forEach(sectionId => {
                const content = document.getElementById(sectionId + '-content');
                if (content) {
                    content.classList.add('active');
                }
            });
        }

        // Setup WebSocket listener for request updates
        if (window.Echo) {
            const userId = {{ auth()->id() }};
            console.log('Setting up request update listener for user:', userId);
            
            window.Echo.private(`App.Models.User.${userId}`)
                .listen('.request-updated', (data) => {
                    console.log('Request updated:', data);
                    
                    // Play notification sound
                    const audio = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
                    audio.play().catch(e => console.log('Could not play sound:', e));
                    
                    // Show browser notification if permitted
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('Request Update', {
                            body: data.message,
                            icon: '/images/logo.png',
                            badge: '/images/logo.png'
                        });
                    }
                    
                    // Update yellow dot in navigation
                    updateDashboardDot();
                    
                    // Show toast notification
                    showToast('Request Update', data.message, data.status);
                    
                    // Reload page after 2 seconds to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                });
            
            // Request notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }
    });

    // Toast notification function
    function showToast(title, message, status) {
        const toast = document.createElement('div');
        const bgColor = status === 'rejected' ? 'bg-red-600' : 'bg-green-600';
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-up`;
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <div class="font-bold">${title}</div>
                    <div class="text-sm">${message}</div>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
    
    // Function to update dashboard yellow dot
    function updateDashboardDot() {
        const dashboardLink = document.querySelector('a[href*="dashboard"]');
        if (!dashboardLink || window.location.pathname.includes('dashboard')) return;
        
        // Remove existing dot
        const existingDot = dashboardLink.querySelector('.bg-yellow-500');
        if (existingDot && existingDot.parentElement) {
            existingDot.parentElement.remove();
        }
        
        // Add new dot
        const dot = document.createElement('span');
        dot.className = 'ml-2 relative inline-flex';
        dot.innerHTML = `
            <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
        `;
        dashboardLink.appendChild(dot);
    }
    
    // Function to check and remove dot if no unread items
    function checkAndRemoveDashboardDot() {
        // Check if there are any yellow dots on the page
        const hasYellowDots = document.querySelectorAll('.bg-yellow-500').length > 1; // >1 because nav also has one
        
        if (!hasYellowDots) {
            const dashboardLink = document.querySelector('a[href*="dashboard"]');
            if (dashboardLink) {
                const existingDot = dashboardLink.querySelector('.bg-yellow-500');
                if (existingDot && existingDot.parentElement) {
                    existingDot.parentElement.remove();
                }
            }
        }
    }
    
    // Call this when page loads to check initial state
    document.addEventListener('DOMContentLoaded', function() {
        checkAndRemoveDashboardDot();
    });
</script>
@endpush
@endsection