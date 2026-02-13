@extends('layouts.app')

@section('title', 'Purok Leader Dashboard')

@push('scripts')
    @vite(['resources/js/purok-notifications.js'])
@endpush

@section('content')
    <!-- Add purok ID for real-time notifications -->
    <meta name="purok-id" content="{{ auth()->user()->purok_id }}">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-8 rounded-lg shadow-lg mb-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center purok-leader-dashboard">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Purok Leader Dashboard
                    </h1>
                    <p class="text-purple-100 mt-2">Welcome back! Managing <span class="font-semibold">{{ $purokName }}</span></p>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $pendingCount }}</p>
                        <p class="text-sm">Pending Requests</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $stats['residents_count'] }}</p>
                        <p class="text-sm">Total Residents</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Clearance Analytics (Collapsible) -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <details>
                <summary class="cursor-pointer text-lg font-semibold text-gray-900">Clearance Analytics</summary>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4 mt-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Period</label>
                        <select id="pl-analytics-period" class="w-full border rounded p-2">
                            <option value="monthly" selected>Monthly (last 12)</option>
                            <option value="quarterly">Quarterly (last 4)</option>
                            <option value="annual">Annual (last 5)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Year</label>
                        <input type="number" id="pl-analytics-year" class="w-full border rounded p-2" value="{{ now()->year }}" />
                    </div>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="plAnalyticsBarChart"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const el = document.getElementById('plAnalyticsBarChart');
                        if (!el) return;
                        let chart = null;
                        function buildUrl() {
                            const url = new URL(`${window.location.origin}/analytics/clearances`);
                            url.searchParams.set('period', document.getElementById('pl-analytics-period').value);
                            url.searchParams.set('year', document.getElementById('pl-analytics-year').value);
                            return url.toString();
                        }
                        async function load() {
                            try {
                                const res = await fetch(buildUrl(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                                const payload = await res.json();
                                const ds = (payload.datasets && payload.datasets[0]) || { label: 'Data', data: [] };
                                const data = {
                                    labels: payload.labels || [],
                                    datasets: [{
                                        label: ds.label || 'Clearances',
                                        data: ds.data || [],
                                        backgroundColor: 'rgba(168, 85, 247, 0.8)',
                                        borderColor: 'rgba(168, 85, 247, 1)',
                                        borderWidth: 1
                                    }]
                                };
                                const options = { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } };
                                if (chart) chart.destroy();
                                chart = new Chart(el.getContext('2d'), { type: 'bar', data, options });
                            } catch (e) {
                                console.error('Failed to load clearance analytics', e);
                            }
                        }
                        ['pl-analytics-period','pl-analytics-year'].forEach(id => {
                            const c = document.getElementById(id);
                            if (c) c.addEventListener('change', load);
                        });
                        load();
                    });
                </script>
            </details>
        </div>
        <!-- Role Badges -->
        <div class="flex flex-wrap items-center gap-2 mb-6">

            @php
                $roleLabels = [
                    'purok_leader' => 'Purok Leader',
                    'admin' => 'Admin',
                    'barangay_kagawad' => 'Barangay Official',
                    'barangay_captain' => 'Barangay Captain',
                    'secretary' => 'Secretary',
                    'barangay_clerk' => 'Barangay Clerk',
                    'sk_chairman' => 'SK Chairman',
                ];

                $userRole = auth()->user()->role ?? 'unknown';
                $displayRole = $roleLabels[$userRole] ?? format_label($userRole);
            @endphp

            <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Purok ID: {{ auth()->user()->purok_id }}
            </span>
            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                {{ $displayRole }}
            </span>

            <a href="{{ route('feedback.general') }}"
               class="ml-auto inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.2-3.6A7.37 7.37 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Feedback
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Requests Card -->
            <a href="{{ route('purok_leader.dashboard') }}"
                class="block bg-white rounded-xl shadow-sm hover:shadow-lg p-6 transition-all duration-200 border-2 {{ !isset($activeFilter) ? 'border-blue-500 bg-blue-50' : 'border-transparent' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Requests</h3>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_requests'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Pending Requests Card -->
            <a href="{{ route('purok_leader.dashboard', ['filter' => 'status', 'value' => 'pending']) }}"
                class="pending-requests-card block bg-white rounded-xl shadow-sm hover:shadow-lg p-6 transition-all duration-200 border-2 {{ isset($activeFilter) && $activeFilter['type'] == 'status' && $activeFilter['value'] == 'pending' ? 'border-yellow-500 bg-yellow-50' : 'border-transparent' }} relative overflow-hidden">
                @if($pendingCount > 0)
                    <div class="absolute top-2 right-2">
                        <span class="flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                    </div>
                @endif
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide">Pending</h3>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            <span class="pending-requests-badge">{{ $pendingCount }}</span>
                        </p>
                    </div>
                </div>
            </a>

            <!-- Approved Requests Card -->
            <a href="{{ '/' . ltrim(route('purok_leader.dashboard', ['status_filter' => 'purok_approved', 'form_type_filter' => $formTypeFilter ?? 'barangay_clearance'], false), '/') }}"
                class="block bg-white rounded-xl shadow-sm hover:shadow-lg p-6 transition-all duration-200 border-2 {{ ($statusFilter ?? 'all') == 'purok_approved' ? 'border-green-500 bg-green-50' : 'border-transparent' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide">Purok Approved</h3>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['purok_approved_requests'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Rejected Requests Card -->
            <a href="{{ route('purok_leader.dashboard', ['filter' => 'status', 'value' => 'rejected']) }}"
                class="block bg-white rounded-xl shadow-sm hover:shadow-lg p-6 transition-all duration-200 border-2 {{ isset($activeFilter) && $activeFilter['type'] == 'status' && $activeFilter['value'] == 'rejected' ? 'border-red-500 bg-red-50' : 'border-transparent' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-red-500 to-red-600 text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide">Rejected</h3>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['rejected_requests'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Total Residents Card -->
            <a href="{{ route('purok_leader.resident_records.index') }}"
                class="block bg-white rounded-xl shadow-sm hover:shadow-lg p-6 transition-all duration-200 border-2 border-transparent relative">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-md relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        @if($stats['pending_residents'] > 0)
                            <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ $stats['pending_residents'] }}</span>
                                </span>
                            </span>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-600 text-xs font-medium uppercase tracking-wide">Residents</h3>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['residents_count'] }}</p>
                        @if($stats['pending_residents'] > 0)
                            <p class="text-xs text-red-600 font-semibold mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $stats['pending_residents'] }} pending
                            </p>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif


        <!-- Active Filter Badge -->
        @if(isset($activeFilter))
            <div class="mb-4 flex items-center">
                <span class="text-sm text-gray-600 mr-2">Filtered by:</span>
                @if($activeFilter['type'] == 'status')
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                                @if($activeFilter['value'] == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($activeFilter['value'] == 'approved') bg-green-100 text-green-800
                                                @elseif($activeFilter['value'] == 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                        {{ format_label($activeFilter['value']) }} Requests
                    </span>
                @elseif($activeFilter['type'] == 'resident')
                    @php
                        $resident = App\Models\User::find($activeFilter['value']);
                    @endphp
                    <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-sm font-medium">
                        {{ $resident ? $resident->name : 'Resident' }}'s Requests
                    </span>
                @endif
                <a href="{{ route('purok_leader.dashboard') }}" class="ml-2 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        @endif

        <div class="mb-8 bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        @if(isset($activeFilter))
                            @if($activeFilter['type'] == 'status')
                                {{ format_label($activeFilter['value']) }} Requests
                            @elseif($activeFilter['type'] == 'resident')
                                {{ $resident ? $resident->name . "'s" : 'Resident' }} Requests
                            @endif
                        @else
                            Purok Clearance Requests
                        @endif
                        @if(count($requests) > 0)
                            <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                {{ count($requests) }} {{ Str::plural('request', count($requests)) }}
                            </span>
                        @endif
                    </h2>

                    <div class="flex gap-2">
                        <button type="button" onclick="plPrintAll()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print All
                        </button>
                        <button type="button" id="plPrintSelectedBtn" onclick="plPrintSelected()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Print Selected
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Search and Filter Section -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <form method="GET" action="{{ route('purok_leader.dashboard') }}" class="space-y-4">
                    <input type="hidden" name="form_type_filter" value="barangay_clearance">
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       id="purokLiveSearch"
                                       value="{{ $search ?? '' }}" 
                                       placeholder="Live search: ID, name, address, or purpose..." 
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent sm:text-sm">
                            </div>
                        </div>

                        <div class="w-full md:w-56">
                            <select name="status_filter"
                                    id="purokStatusFilter"
                                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-lg">
                                <option value="all" {{ ($statusFilter ?? 'all') == 'all' ? 'selected' : '' }}>All</option>
                                <option value="pending" {{ ($statusFilter ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ ($statusFilter ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            @if($search || ($statusFilter ?? 'all') !== 'all')
                                <a href="{{ route('purok_leader.dashboard') }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="w-10 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="plSelectAllRequests" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Request ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Requested
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resident Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Document Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($requests as $request)
                            @php
                                // Show dot if request needs purok leader's action (status = pending)
                                $needsAction = $request->status === 'pending';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-2 py-4 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="pl-request-checkbox rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50" value="{{ $request->id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        @if($needsAction)
                                            {{-- Yellow dot indicator for requests needing action --}}
                                            <span class="relative inline-flex flex-shrink-0">
                                                <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                            </span>
                                        @endif
                                        <span>#{{ $request->id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $request->created_at->format('F j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $request->requester_name ?: ($request->user->name ?? 'N/A') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @php
                                        $formTypeLabel = ucfirst(str_replace('_', ' ', $request->form_type));
                                        if (isset($formTypes) && is_array($formTypes) && array_key_exists($request->form_type, $formTypes)) {
                                            $formTypeLabel = $formTypes[$request->form_type];
                                        } elseif ($request->form_type === 'barangay_clearance') {
                                            $formTypeLabel = 'Purok Clearance';
                                        } else {
                                            $formTypeLabel = \App\Models\Request::FORM_TYPES[$request->form_type] ?? $formTypeLabel;
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $formTypeLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $request->purpose }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'purok_approved' => 'bg-blue-100 text-blue-800',
                                            'barangay_approved' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $color = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                        $statusLabel = [
                                            'pending' => 'Pending',
                                            'purok_approved' => 'Purok Approved',
                                            'barangay_approved' => 'Barangay Approved',
                                            'completed' => 'Purok Approved',
                                            'rejected' => 'Rejected',
                                            'cancelled' => 'Cancelled'
                                        ][$request->status] ?? format_label($request->status);
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $color }} justify-center">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $isClearance = in_array($request->form_type, ['barangay_clearance','business_clearance','certificate_of_residency','certificate_of_indigency']);
                                    @endphp
                                    @if($isClearance)
                                        <a href="{{ route('purok_leader.clearance.view', $request) }}"
                                            class="inline-flex items-center justify-center p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-150"
                                            title="View Clearance">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('requests.show', $request) }}"
                                            class="inline-flex items-center justify-center p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-150"
                                            title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No clearance requests found</p>
                                        <p class="text-sm text-gray-400 mt-1">Requests will appear here when residents submit them</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="px-6 py-3 bg-white border-t border-gray-200">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="px-6 py-4 border-t border-gray-200 text-sm text-gray-500">
                        Showing {{ $requests->count() }} requests
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .pagination li {
            margin: 0 0.25rem;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            border: 1px solid #d1d5db; /* Tailwind gray-300 */
            border-radius: 0.25rem;
            color: #374151; /* Tailwind gray-700 */
            font-size: 0.875rem; /* text-sm */
            text-decoration: none;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .pagination a:hover {
            background-color: #f3f4f6; /* gray-100 */
            border-color: #9ca3af;     /* gray-400 */
        }

        .pagination .active span {
            background-color: #3b82f6; /* blue-500 */
            border-color: #3b82f6;
            color: white;
        }

        .pagination .disabled span {
            background-color: #f3f4f6; /* gray-100 */
            border-color: #d1d5db;     /* gray-300 */
            color: #9ca3af;            /* gray-400 */
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const searchInput = document.getElementById('purokLiveSearch');
            const statusSelect = document.getElementById('purokStatusFilter');
            const form = searchInput ? searchInput.closest('form') : null;

            if (!form) return;

            let t;
            const submitDebounced = () => {
                clearTimeout(t);
                t = setTimeout(() => {
                    form.submit();
                }, 350);
            };

            if (searchInput) {
                searchInput.addEventListener('input', submitDebounced);
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', () => form.submit());
            }
        })();

        function plSelectedRequestIds() {
            return Array.from(document.querySelectorAll('.pl-request-checkbox:checked')).map(cb => cb.value);
        }

        function plUpdatePrintSelectedButton() {
            const btn = document.getElementById('plPrintSelectedBtn');
            if (!btn) return;
            btn.disabled = plSelectedRequestIds().length === 0;
        }

        function plPrintAll() {
            const url = "{{ route('reports.pdf.purok-clearance') }}";
            window.open(url, '_blank');
        }

        function plPrintSelected() {
            const selected = plSelectedRequestIds();
            if (selected.length === 0) return;
            const url = "{{ route('reports.pdf.purok-clearance') }}" + '?ids=' + selected.join(',');
            window.open(url, '_blank');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('plSelectAllRequests');
            const boxes = Array.from(document.querySelectorAll('.pl-request-checkbox'));

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    boxes.forEach(cb => cb.checked = selectAll.checked);
                    plUpdatePrintSelectedButton();
                });
            }

            boxes.forEach(cb => cb.addEventListener('change', function () {
                if (selectAll) {
                    selectAll.checked = boxes.length > 0 && boxes.every(x => x.checked);
                    selectAll.indeterminate = boxes.some(x => x.checked) && !selectAll.checked;
                }
                plUpdatePrintSelectedButton();
            }));

            plUpdatePrintSelectedButton();
        });
    </script>
@endpush