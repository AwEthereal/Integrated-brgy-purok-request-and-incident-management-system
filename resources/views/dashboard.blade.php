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
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-700">Dashboard</h1>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('requests.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Request
                </a>
                <a href="{{ route('incident_reports.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Report Incident
                </a>
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
                        @forelse($recentActivities as $activity)
                            @php
                                $status = strtolower($activity->status);
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                ];
                                $statusClass = $statusClasses[$status] ?? $statusClasses['default'];
                                
                                $iconClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'approved' => 'text-blue-600 bg-blue-100',
                                    'barangay_approved' => 'text-blue-600 bg-blue-100',
                                    'completed' => 'text-green-600 bg-green-100',
                                    'resolved' => 'text-green-600 bg-green-100',
                                    'rejected' => 'text-red-600 bg-red-100',
                                    'default' => 'text-gray-600 bg-gray-100'
                                ];
                                $iconClass = $iconClasses[$status] ?? $iconClasses['default'];
                                
                                $activityType = $activity->type === 'Request' ? 'Barangay Request' : 'Incident Report';
                            @endphp
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150 shadow-sm mb-3 last:mb-0 border border-gray-100 dark:border-gray-600">
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
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white pr-16">
                                        <a href="{{ $activity->url }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $activity->description }}
                                        </a>
                                    </p>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">
                                            @if($activity->type === 'Incident' && isset($activity->incident_type))
                                                {{ \App\Models\IncidentReport::TYPES[$activity->incident_type] ?? ucfirst($activity->incident_type) }}
                                            @else
                                                {{ $activityType }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No recent activities found</p>
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
                        <a href="{{ route('requests.my_requests') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">View all</a>
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
                        <a href="{{ route('incident_reports.my_reports') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">View all</a>
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
        </div>
    </div>
</div>
@endsection