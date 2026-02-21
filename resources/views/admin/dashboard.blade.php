@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Back Button -->
        <div class="bg-gray-800 dark:bg-gray-900 rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col gap-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ auth()->user()->role === 'barangay_captain' ? 'Barangay Captain Dashboard' : 'Admin Dashboard' }}</h1>
                        <p class="mt-2 text-base text-gray-300 font-medium">System overview and analytics</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="(function(){ const d=document.getElementById('advanced-analytics-details'); if(d){ d.open = true; } const a=document.getElementById('advanced-analytics'); if(a){ a.scrollIntoView({behavior:'smooth'}); } })();" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-150">
                            Analytics
                        </button>

                        @if(auth()->user()->role !== 'barangay_captain')
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Main
                            </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-3" id="advanced-analytics">
                    <details id="advanced-analytics-details">
                        <summary class="cursor-pointer text-sm md:text-base font-semibold text-gray-900 dark:text-white">Advanced Analytics</summary>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3 mt-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Dataset</label>
                                <select id="analytics-dataset" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                    <option value="clearances" selected>Clearance Requests</option>
                                    <option value="incidents">Reported Incidents</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Period</label>
                                <select id="analytics-period" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                    <option value="monthly" selected>Monthly (last 12)</option>
                                    <option value="quarterly">Quarterly (last 4)</option>
                                    <option value="annual">Annual (last 5)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Grouping</label>
                                <select id="analytics-group" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                    <option value="total" selected>Total</option>
                                    <option value="per_purok">Per Purok (clearances)</option>
                                    <option value="per_type">Per Incident Type (incidents)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Year</label>
                                <input type="number" id="analytics-year" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white" value="{{ now()->year }}" />
                            </div>
                        </div>
                        <div class="relative" style="height: 280px;">
                            <canvas id="analyticsBarChart"></canvas>
                        </div>
                    </details>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-800 dark:text-green-200 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-800 dark:text-red-200 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Users</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalUsers }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending Requests</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $pendingRequests }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Incidents -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Incidents</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $activeIncidents }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Puroks -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Puroks</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalPuroks }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'barangay_captain')
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-3 mb-8">
                <details id="captain-snapshot-details">
                    <summary class="cursor-pointer text-sm md:text-base font-semibold text-gray-900 dark:text-white">Dashboard Snapshot</summary>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                User Distribution by Role
                            </h2>
                            <div class="relative" style="height: 300px;">
                                <canvas id="userDistributionChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                Population by Purok
                            </h2>
                            <div class="relative" style="height: 300px;">
                                <canvas id="purokPopulationChart"></canvas>
                            </div>
                            <div id="purokPopulationLegend" class="mt-3 max-h-44 overflow-auto text-xs"></div>
                        </div>
                    </div>
                </details>
            </div>
        @endif

        @if(auth()->user()->role !== 'barangay_captain')
            <!-- Analytics Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- User Distribution Pie Chart -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        User Distribution by Role
                    </h2>
                    <div class="relative" style="height: 300px;">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Request Status Pie Chart -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Request Status Distribution
                    </h2>
                    <div class="relative" style="height: 300px;">
                        <canvas id="requestStatusChart"></canvas>
                    </div>
                </div>

                <!-- Incident Status Pie Chart -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Incident Status Distribution
                    </h2>
                    <div class="relative" style="height: 300px;">
                        <canvas id="incidentStatusChart"></canvas>
                    </div>
                </div>

                <!-- Purok Population Chart -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        Population by Purok
                    </h2>
                    <div class="relative" style="height: 300px;">
                        <canvas id="purokPopulationChart"></canvas>
                    </div>
                    <div id="purokPopulationLegend" class="mt-3 max-h-44 overflow-auto text-xs"></div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Management -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('captain.secretaries.index') }}" class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">Manage Users</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('reports.purok-clearance') }}" class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">View Requests</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('reports.incident-reports') }}" class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">View Incidents</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- <a href="{{ route('reports.incident-reports') }}" class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">Generate Reports</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a> -->
                </div>
            </div>

            <!-- System Statistics -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Statistics</h2>
                <div class="space-y-4">
                    <a href="{{ route('reports.purok-clearance', ['ui_status' => 'pending']) }}" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md px-2 py-1 transition">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pending Purok Clearance Requests</span>
                        <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingRequests }}</span>
                    </a>
                    <a href="{{ route('reports.purok-clearance', ['ui_status' => 'completed']) }}" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md px-2 py-1 transition">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Completed Requests</span>
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $completedRequests }}</span>
                    </a>
                    <a href="{{ route('reports.incident-reports', ['ui_status' => 'closed']) }}" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md px-2 py-1 transition">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Closed/Completed Incidents</span>
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $resolvedIncidents }}</span>
                    </a>
                    <a href="{{ route('captain.secretaries.index') }}" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md px-2 py-1 transition">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Manage Accounts</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ($purokLeaders ?? 0) + ($barangayOfficials ?? 0) }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Users</h2>
                    <a href="{{ route('captain.secretaries.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentUsers as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ format_label($user->role) }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent users</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Requests</h2>
                    <a href="{{ route('reports.purok-clearance') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentRequests as $request)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'purok_approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'barangay_approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                ];
                                $statusClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                $requesterLabel = optional($request->user)->name ?? ($request->requester_name ?? 'Public Applicant');
                            @endphp

                            <div class="flex items-center justify-between">
                                <div class="w-1/3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $requesterLabel }}</p>
                                </div>
                                <div class="flex-1 text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ format_label($request->status) }}
                                    </span>
                                </div>
                                <div class="w-1/3 flex justify-end">
                                    <a href="{{ route('official.clearance.view', $request->id) }}" class="text-gray-400 hover:text-green-600" title="View Details">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Purok Clearance
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-300" title="{{ $request->created_at?->format('M d, Y h:i A') }}">
                                    {{ optional($request->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent requests</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Distribution Pie Chart
    const userDistEl = document.getElementById('userDistributionChart');
    if (userDistEl) {
        const userDistCtx = userDistEl.getContext('2d');
        new Chart(userDistCtx, {
            type: 'pie',
            data: {
                labels: ['Residents', 'Purok Leaders', 'Barangay Officials', 'Admins'],
                datasets: [{
                    data: [{{ $residents }}, {{ $purokLeaders }}, {{ $barangayOfficials }}, {{ $admins }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',  // Blue
                        'rgba(168, 85, 247, 0.8)',  // Purple
                        'rgba(34, 197, 94, 0.8)',   // Green
                        'rgba(239, 68, 68, 0.8)'    // Red
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Request Status Pie Chart
    const requestStatusEl = document.getElementById('requestStatusChart');
    if (requestStatusEl) {
        const requestStatusCtx = requestStatusEl.getContext('2d');
        new Chart(requestStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Awaiting Approval', 'Completed', 'Rejected'],
                datasets: [{
                    data: [{{ $pendingRequests }}, {{ $awaitingApprovalRequests ?? 0 }}, {{ $completedRequests }}, {{ $rejectedRequests }}],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',  // Yellow
                        'rgba(59, 130, 246, 0.8)',  // Blue
                        'rgba(34, 197, 94, 0.8)',   // Green
                        'rgba(239, 68, 68, 0.8)'    // Red
                    ],
                    borderColor: [
                        'rgba(251, 191, 36, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Incident Status Pie Chart
    const incidentStatusEl = document.getElementById('incidentStatusChart');
    if (incidentStatusEl) {
        const incidentStatusCtx = incidentStatusEl.getContext('2d');
        new Chart(incidentStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'In Progress', 'Resolved'],
                datasets: [{
                    data: [{{ $pendingIncidents }}, {{ $inProgressIncidents }}, {{ $resolvedIncidents }}],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',  // Yellow
                        'rgba(59, 130, 246, 0.8)',  // Blue
                        'rgba(34, 197, 94, 0.8)'    // Green
                    ],
                    borderColor: [
                        'rgba(251, 191, 36, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Purok Population Chart
    const purokPopEl = document.getElementById('purokPopulationChart');
    if (purokPopEl) {
        const purokPopCtx = purokPopEl.getContext('2d');
        const purokData = @json($purokData);

        const htmlLegendPlugin = {
            id: 'htmlLegend',
            afterUpdate(chart, args, options) {
                const container = document.getElementById(options.containerID);
                if (!container) return;
                container.innerHTML = '';
                const ul = document.createElement('ul');
                ul.className = 'space-y-1';

                const items = chart.options.plugins.legend.labels.generateLabels(chart);
                items.forEach((item) => {
                    const li = document.createElement('li');
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'w-full flex items-center gap-2 rounded-md px-2 py-1 hover:bg-gray-50 dark:hover:bg-gray-700 text-left';

                    const box = document.createElement('span');
                    box.className = 'inline-block h-3 w-3 rounded-sm flex-shrink-0';
                    box.style.background = item.fillStyle;
                    box.style.border = '1px solid ' + item.strokeStyle;

                    const text = document.createElement('span');
                    text.className = 'truncate';
                    text.textContent = item.text;

                    if (item.hidden) {
                        box.style.opacity = '0.35';
                        text.style.opacity = '0.5';
                    }

                    button.onclick = () => {
                        if (typeof chart.toggleDataVisibility === 'function') {
                            chart.toggleDataVisibility(item.index);
                        } else {
                            const meta = chart.getDatasetMeta(0);
                            if (meta && meta.data && meta.data[item.index]) {
                                meta.data[item.index].hidden = !meta.data[item.index].hidden;
                            }
                        }
                        chart.update();
                    };

                    button.appendChild(box);
                    button.appendChild(text);
                    li.appendChild(button);
                    ul.appendChild(li);
                });

                container.appendChild(ul);
            }
        };

        function generateColors(n) {
            const bg = [];
            const border = [];
            for (let i = 0; i < n; i++) {
                const hue = Math.round((360 * i) / Math.max(n, 1));
                bg.push(`hsla(${hue}, 75%, 55%, 0.85)`);
                border.push(`hsla(${hue}, 75%, 40%, 1)`);
            }
            return { bg, border };
        }

        const colors = generateColors(purokData.length);

        new Chart(purokPopCtx, {
            type: 'doughnut',
            data: {
                labels: purokData.map(p => p.name),
                datasets: [{
                    data: purokData.map(p => p.count),
                    backgroundColor: colors.bg,
                    borderColor: colors.border,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    htmlLegend: {
                        containerID: 'purokPopulationLegend'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' residents (' + percentage + '%)';
                            }
                        }
                    }
                }
            },
            plugins: [htmlLegendPlugin]
        });
    }

    // Advanced Analytics Bar Chart
    const analyticsCtx = document.getElementById('analyticsBarChart');
    let analyticsChart = null;

    function buildAnalyticsUrl() {
        const dataset = document.getElementById('analytics-dataset').value;
        const period = document.getElementById('analytics-period').value;
        const group = document.getElementById('analytics-group').value;
        const year = document.getElementById('analytics-year').value;
        const url = new URL(`${window.location.origin}/analytics/${dataset}`);
        url.searchParams.set('period', period);
        if (group) url.searchParams.set('group', group);
        if (year) url.searchParams.set('year', year);
        return url.toString();
    }

    async function loadAnalytics() {
        if (!analyticsCtx) return;
        try {
            const res = await fetch(buildAnalyticsUrl(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            const payload = await res.json();
            const colors = 'rgba(59, 130, 246, 0.8)';
            const border = 'rgba(59, 130, 246, 1)';
            const ds = payload.datasets?.[0] || { label: 'Data', data: [] };
            const chartData = {
                labels: payload.labels || [],
                datasets: [{
                    label: ds.label || 'Data',
                    data: ds.data || [],
                    backgroundColor: colors,
                    borderColor: border,
                    borderWidth: 1
                }]
            };
            const options = { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } };
            if (analyticsChart) { analyticsChart.destroy(); }
            analyticsChart = new Chart(analyticsCtx.getContext('2d'), { type: 'bar', data: chartData, options });
        } catch (e) {
            console.error('Failed to load analytics', e);
        }
    }

    ['analytics-dataset','analytics-period','analytics-group','analytics-year'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', loadAnalytics);
    });
    loadAnalytics();
});
</script>
@endpush
@endsection
