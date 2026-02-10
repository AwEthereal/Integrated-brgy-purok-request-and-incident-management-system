@extends('layouts.app')

@section('title', 'Barangay Official Dashboard')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-8 rounded-lg shadow-lg mb-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2m-6 0h6" />
                        </svg>
                        Barangay Official Dashboard
                    </h1>
                    <p class="text-green-100 mt-2">Welcome back, {{ auth()->user()->name }}! Here's what's happening in your barangay today.</p>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $approvedClearancesCount ?? count($pendingRequests) }}</p>
                        <p class="text-sm">Recently Approved Clearances</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $activeIncidentsCount ?? count($incidents) }}</p>
                        <p class="text-sm">Active Incidents</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Clearance Analytics (Collapsible) -->
        <div class="bg-white rounded-lg shadow-sm p-2 mb-3 analytics-card">
            <details id="bo-clearance-analytics">
                <summary class="cursor-pointer text-sm md:text-base font-semibold text-gray-900">Clearance Analytics</summary>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3 mt-3">
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Period</label>
                        <select id="bo-analytics-period" class="w-full border rounded p-2 text-sm">
                            <option value="monthly" selected>Monthly (last 12)</option>
                            <option value="quarterly">Quarterly (last 4)</option>
                            <option value="annual">Annual (last 5)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Grouping</label>
                        <select id="bo-analytics-group" class="w-full border rounded p-2 text-sm">
                            <option value="total" selected>Total</option>
                            <option value="per_purok">Per Purok</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Year</label>
                        <input type="number" id="bo-analytics-year" class="w-full border rounded p-2 text-sm" value="{{ now()->year }}" />
                    </div>
                </div>
                <div class="relative" style="height: 200px;">
                    <canvas id="boAnalyticsBarChart"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const el = document.getElementById('boAnalyticsBarChart');
                        if (!el) return;
                        let chart = null;
                        function buildUrl() {
                            const url = new URL(`${window.location.origin}/analytics/clearances`);
                            url.searchParams.set('period', document.getElementById('bo-analytics-period').value);
                            url.searchParams.set('group', document.getElementById('bo-analytics-group').value);
                            url.searchParams.set('year', document.getElementById('bo-analytics-year').value);
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
                                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                        borderColor: 'rgba(34, 197, 94, 1)',
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
                        ['bo-analytics-period','bo-analytics-group','bo-analytics-year'].forEach(id => {
                            const c = document.getElementById(id);
                            if (c) c.addEventListener('change', load);
                        });
                        load();
                    });
                </script>
            </details>
        </div>

        <!-- Incident Analytics (Collapsible) -->
        <div class="bg-white rounded-lg shadow-sm p-2 mb-3 analytics-card">
            <details id="bo-incident-analytics">
                <summary class="cursor-pointer text-sm md:text-base font-semibold text-gray-900">Incident Analytics</summary>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3 mt-3">
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Period</label>
                        <select id="bo-inc-period" class="w-full border rounded p-2 text-sm">
                            <option value="monthly" selected>Monthly (last 12)</option>
                            <option value="quarterly">Quarterly (last 4)</option>
                            <option value="annual">Annual (last 5)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Grouping</label>
                        <select id="bo-inc-group" class="w-full border rounded p-2 text-sm">
                            <option value="total" selected>Total</option>
                            <option value="per_type">Per Type</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Year</label>
                        <input type="number" id="bo-inc-year" class="w-full border rounded p-2 text-sm" value="{{ now()->year }}" />
                    </div>
                </div>
                <div class="relative" style="height: 200px;">
                    <canvas id="boIncidentsBarChart"></canvas>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const el = document.getElementById('boIncidentsBarChart');
                        if (!el) return;
                        let chart = null;
                        function buildUrl() {
                            const url = new URL(`${window.location.origin}/analytics/incidents`);
                            url.searchParams.set('period', document.getElementById('bo-inc-period').value);
                            url.searchParams.set('group', document.getElementById('bo-inc-group').value);
                            url.searchParams.set('year', document.getElementById('bo-inc-year').value);
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
                                        label: ds.label || 'Incidents',
                                        data: ds.data || [],
                                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                                        borderColor: 'rgba(99, 102, 241, 1)',
                                        borderWidth: 1
                                    }]
                                };
                                const options = { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } };
                                if (chart) chart.destroy();
                                chart = new Chart(el.getContext('2d'), { type: 'bar', data, options });
                            } catch (e) {
                                console.error('Failed to load incident analytics', e);
                            }
                        }
                        ['bo-inc-period','bo-inc-group','bo-inc-year'].forEach(id => {
                            const c = document.getElementById(id);
                            if (c) c.addEventListener('change', load);
                        });
                        load();
                    });
                </script>
            </details>
        </div>

        <!-- Purok Filter Section -->
        <div class="mb-8 bg-white rounded-lg shadow-sm p-4">
            @include('barangay_official.partials.purok_filter', ['puroks' => $puroks, 'selectedPurok' => $selectedPurok])
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 items-start">
            <!-- Approved Purok Clearances Section -->
            <div id="approvedClearancesSection" class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Recently Approved Clearances
                        @if(($approvedClearancesCount ?? count($pendingRequests)) > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                {{ $approvedClearancesCount ?? count($pendingRequests) }} {{ Str::plural('request', $approvedClearancesCount ?? count($pendingRequests)) }}
                            </span>
                        @endif
                    </h2>
                </div>
                <div class="p-6">
                    @if(count($pendingRequests) > 0)
                        <div class="space-y-3">
                            @foreach($pendingRequests as $request)
                                @php
                                    // Show dot if request needs barangay approval (status = purok_approved)
                                    $needsAction = $request->status === 'purok_approved';
                                @endphp
                                <div class="border border-gray-200 rounded-lg px-4 py-3 relative">
                                    @if($needsAction)
                                        {{-- Yellow dot for items needing action --}}
                                        <div class="absolute -top-1 -right-1 z-10">
                                            <span class="relative inline-flex">
                                                <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                                <span class="relative inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="w-1/3">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ optional($request->user)->name ?? ($request->requester_name ?? 'Public Applicant') }}</p>
                                        </div>
                                        <div class="flex-1 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
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
                                        <span class="text-xs text-gray-500" title="{{ $request->created_at->format('M d, Y h:i A') }}">
                                            {{ $request->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($pendingRequests->hasPages())
                            <div class="mt-4 flex justify-center">
                                {{ $pendingRequests->onEachSide(1)->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No recently approved clearances found.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Incidents Section -->
            <div id="activeIncidentsSection" class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Active Incidents
                        @if(($activeIncidentsCount ?? count($incidents)) > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                {{ $activeIncidentsCount ?? count($incidents) }} {{ Str::plural('incident', $activeIncidentsCount ?? count($incidents)) }}
                            </span>
                        @endif
                    </h2>
                </div>

                <div class="p-6">
                    @if(count($incidents) > 0)
                        <div class="space-y-3">
                            @foreach($incidents as $incident)
                                @php
                                    // Show dot if incident needs action (pending or in_progress)
                                    $needsAction = in_array($incident->status, ['pending', 'in_progress']);
                                @endphp
                                <div class="border border-gray-200 rounded-lg px-4 py-3 relative">
                                    @if($needsAction)
                                        {{-- Yellow dot for items needing action --}}
                                        <div class="absolute -top-1 -right-1 z-10">
                                            <span class="relative inline-flex">
                                                <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                                <span class="relative inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="w-1/3">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ optional($incident->user)->name ?? ($incident->reporter_name ?? 'Public Reporter') }}</p>
                                        </div>
                                        <div class="flex-1 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $incident->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ format_label($incident->status) }}
                                            </span>
                                        </div>
                                        <div class="w-1/3 flex justify-end">
                                            <a href="{{ route('incident_reports.show', ['id' => $incident->id, 'redirect_to' => url()->full()]) }}" class="text-gray-400 hover:text-blue-600" title="View Details">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $incident->incident_type === 'other' ? ($incident->incident_type_other ?? 'Other') : (\App\Models\IncidentReport::TYPES[$incident->incident_type] ?? format_label($incident->incident_type)) }}
                                        </span>
                                        <span class="text-xs text-gray-500" title="{{ $incident->created_at->format('M d, Y h:i A') }}">
                                            {{ $incident->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($incidents->hasPages())
                            <div class="mt-4 flex justify-center">
                                {{ $incidents->onEachSide(1)->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No active incidents found.</p>
                        </div>
                    @endif
                </div>


                @push('styles')
                    <style>
                        /* Smooth scrolling for better mobile experience */
                        html {
                            scroll-behavior: smooth;
                        }
                        /* Improve focus states for keyboard navigation */
                        a:focus, button:focus, [tabindex="0"]:focus {
                            outline: 2px solid #059669;
                            outline-offset: 2px;
                            border-radius: 0.25rem;
                        }

                        /* Scoped pagination styling (dashboard only) */
                        #approvedClearancesSection nav[aria-label="Pagination Navigation"] a,
                        #approvedClearancesSection nav[aria-label="Pagination Navigation"] span {
                            background-color: rgba(255, 255, 255, 0.65) !important;
                            border-color: rgba(16, 185, 129, 0.45) !important; /* emerald */
                            color: rgba(6, 95, 70, 0.95) !important;
                            backdrop-filter: blur(4px);
                        }
                        #approvedClearancesSection nav[aria-label="Pagination Navigation"] a:hover {
                            background-color: rgba(236, 253, 245, 0.95) !important;
                            border-color: rgba(16, 185, 129, 0.75) !important;
                        }
                        #approvedClearancesSection nav[aria-label="Pagination Navigation"] span[aria-current="page"] span {
                            background-color: rgba(16, 185, 129, 0.95) !important;
                            border-color: rgba(16, 185, 129, 0.95) !important;
                            color: #ffffff !important;
                        }

                        #activeIncidentsSection nav[aria-label="Pagination Navigation"] a,
                        #activeIncidentsSection nav[aria-label="Pagination Navigation"] span {
                            background-color: rgba(255, 255, 255, 0.65) !important;
                            border-color: rgba(59, 130, 246, 0.45) !important; /* blue */
                            color: rgba(30, 64, 175, 0.95) !important;
                            backdrop-filter: blur(4px);
                        }
                        #activeIncidentsSection nav[aria-label="Pagination Navigation"] a:hover {
                            background-color: rgba(239, 246, 255, 0.95) !important;
                            border-color: rgba(59, 130, 246, 0.75) !important;
                        }
                        #activeIncidentsSection nav[aria-label="Pagination Navigation"] span[aria-current="page"] span {
                            background-color: rgba(59, 130, 246, 0.95) !important;
                            border-color: rgba(59, 130, 246, 0.95) !important;
                            color: #ffffff !important;
                        }
                    </style>
                @endpush

                @push('scripts')
                    <script>
                        async function fetchAndReplaceSection(url, sectionId) {
                            const res = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!res.ok) {
                                window.location.href = url;
                                return;
                            }

                            const html = await res.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newSection = doc.getElementById(sectionId);
                            const currentSection = document.getElementById(sectionId);

                            if (!newSection || !currentSection) {
                                window.location.href = url;
                                return;
                            }

                            currentSection.replaceWith(newSection);
                            window.history.pushState({}, '', url);
                        }

                        document.addEventListener('click', function (e) {
                            const a = e.target.closest('a');
                            if (!a) return;

                            const clearancesSection = a.closest('#approvedClearancesSection');
                            const incidentsSection = a.closest('#activeIncidentsSection');
                            if (!clearancesSection && !incidentsSection) return;

                            const href = a.getAttribute('href');
                            if (!href || href === '#') return;

                            if (!a.closest('nav[aria-label="Pagination Navigation"]')) return;

                            e.preventDefault();
                            fetchAndReplaceSection(href, clearancesSection ? 'approvedClearancesSection' : 'activeIncidentsSection');
                        });

                        // Add ARIA attributes for better accessibility
                        document.addEventListener('DOMContentLoaded', function() {
                            // Add aria-live to dynamic content areas
                            const dynamicContent = document.querySelectorAll('.dynamic-content');
                            dynamicContent.forEach(el => {
                                el.setAttribute('aria-live', 'polite');
                                el.setAttribute('aria-atomic', 'true');
                            });
                        });

                        function switchTab(tabName) {
                            // If clicking the already active tab, do nothing
                            const currentTab = new URLSearchParams(window.location.search).get('tab') || 'pending';
                            if (currentTab === tabName) return;

                            // Build the new URL with the correct tab parameter
                            const url = new URL(window.location.href);
                            url.searchParams.set('tab', tabName);

                            // If switching to pending tab, remove the status filter
                            if (tabName === 'pending') {
                                url.searchParams.delete('status');
                            }

                            // Navigate to the new URL
                            window.location.href = url.toString();
                        }

                        // Function to update URL parameters
                        function updateUrlParameter(param, value) {
                            // Get current search params
                            const searchParams = new URLSearchParams(window.location.search);

                            // Update or remove the parameter
                            if (value === '') {
                                searchParams.delete(param);
                                // If removing status filter, also remove the page parameter
                                if (param === 'status') {
                                    searchParams.delete('page');
                                }
                            } else {
                                searchParams.set(param, value);
                                // When changing status, reset to first page
                                if (param === 'status') {
                                    searchParams.set('page', '1');
                                }
                            }

                            // Always set tab to history when filtering status
                            if (param === 'status') {
                                searchParams.set('tab', 'history');
                            }

                            // Preserve other query parameters
                            const currentTab = searchParams.get('tab') || 'pending';
                            const purok = searchParams.get('purok') || '';

                            // Build the new URL with all parameters
                            let newUrl = '/dashboard?';
                            if (currentTab) newUrl += `tab=${currentTab}&`;
                            if (purok) newUrl += `purok=${purok}&`;
                            if (param === 'status' && value) newUrl += `status=${value}&`;

                            // Remove trailing & or ? if no parameters
                            newUrl = newUrl.replace(/[&?]$/, '');

                            // Navigate to the new URL
                            window.location.href = newUrl;
                        }

                        // Initialize the correct tab on page load
                        document.addEventListener('DOMContentLoaded', function () {
                            const urlParams = new URLSearchParams(window.location.search);
                            const tab = urlParams.get('tab') || 'pending';
                            const status = urlParams.get('status');

                            // Update tab buttons active state
                            document.querySelectorAll('button[onclick^="switchTab"]').forEach(button => {
                                const tabName = button.getAttribute('onclick').match(/'([^']+)'/)[1];
                                if (tabName === tab) {
                                    button.classList.add('border-green-500', 'text-green-600');
                                    button.classList.remove('border-transparent', 'text-gray-500');
                                } else {
                                    button.classList.remove('border-green-500', 'text-green-600');
                                    button.classList.add('border-transparent', 'text-gray-500');
                                }
                            });

                            // Show the correct tab content
                            document.querySelectorAll('[id^="pending"], [id^="history"]').forEach(tabElement => {
                                tabElement.classList.add('hidden');
                            });

                            const activeTab = document.getElementById(tab);
                            if (activeTab) {
                                activeTab.classList.remove('hidden');
                            } else {
                                // Fallback to pending tab if specified tab doesn't exist
                                document.getElementById('pending').classList.remove('hidden');
                            }

                            // Update status filter if it exists
                            if (document.getElementById('status-filter') && status) {
                                document.getElementById('status-filter').value = status;
                            }
                        });

                        // WebSocket listeners for real-time updates
                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('DOM Content Loaded - Checking for Echo...');
                            
                            if (window.Echo) {
                                console.log('✅ Echo is available! Setting up real-time listeners for barangay officials...');
                                console.log('Echo object:', window.Echo);
                                
                                // Listen for new incident reports
                                window.Echo.private('barangay-officials')
                                    .listen('.new-incident', (data) => {
                                        console.log('New incident reported:', data);
                                        
                                        // Play notification sound
                                        const audio = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
                                        audio.play().catch(e => console.log('Could not play sound:', e));
                                        
                                        // Show browser notification if permitted
                                        if ('Notification' in window && Notification.permission === 'granted') {
                                            new Notification('New Incident Report', {
                                                body: `${data.incident_type} reported by ${data.reporter_name} from ${data.purok_name}`,
                                                icon: '/images/logo.png',
                                                badge: '/images/logo.png'
                                            });
                                        }
                                        
                                        // Update incident count badge in hero section
                                        const incidentBadges = document.querySelectorAll('.bg-white.bg-opacity-20');
                                        if (incidentBadges.length > 1) {
                                            const incidentCountElement = incidentBadges[1].querySelector('p:first-child');
                                            if (incidentCountElement) {
                                                incidentCountElement.textContent = data.incidentCount;
                                            }
                                        }
                                        
                                        // Update incident section count
                                        const incidentSectionBadge = document.querySelector('.bg-gradient-to-r.from-blue-600 .bg-white.bg-opacity-20');
                                        if (incidentSectionBadge) {
                                            const countText = incidentSectionBadge.textContent.trim();
                                            incidentSectionBadge.textContent = `${data.incidentCount} ${data.incidentCount === 1 ? 'incident' : 'incidents'}`;
                                        }
                                        
                                        // Update yellow dot in navigation
                                        updateDashboardDot();
                                        
                                        // Show toast notification
                                        showToast('New Incident Report', `${data.incident_type} from ${data.purok_name}`, 'blue');
                                        
                                        // Reload page after 2 seconds to show new incident
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 2000);
                                    })
                                    // Listen for new barangay requests (purok approved)
                                    .listen('.new-barangay-request', (data) => {
                                        console.log('🔔 NEW BARANGAY REQUEST EVENT RECEIVED!');
                                        console.log('Event data:', data);
                                        console.log('Request Count:', data.requestCount);
                                        console.log('Form Type:', data.form_type);
                                        console.log('Resident:', data.resident_name);
                                        
                                        // Play notification sound
                                        const audio = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
                                        audio.play().catch(e => console.log('Could not play sound:', e));
                                        
                                        // Show browser notification if permitted
                                        if ('Notification' in window && Notification.permission === 'granted') {
                                            new Notification('New Request Pending Approval', {
                                                body: `${data.form_type} from ${data.resident_name} (${data.purok_name})`,
                                                icon: '/images/logo.png',
                                                badge: '/images/logo.png'
                                            });
                                        }
                                        
                                        // Update request count badge in hero section
                                        const requestBadges = document.querySelectorAll('.bg-white.bg-opacity-20');
                                        if (requestBadges.length > 0) {
                                            const requestCountElement = requestBadges[0].querySelector('p:first-child');
                                            if (requestCountElement) {
                                                requestCountElement.textContent = data.requestCount;
                                            }
                                        }
                                        
                                        // Update request section count
                                        const requestSectionBadge = document.querySelector('.bg-gradient-to-r.from-green-600 .bg-white.bg-opacity-20');
                                        if (requestSectionBadge) {
                                            requestSectionBadge.textContent = `${data.requestCount} ${data.requestCount === 1 ? 'request' : 'requests'}`;
                                        }
                                        
                                        // Update yellow dot in navigation
                                        updateDashboardDot();
                                        
                                        // Show toast notification
                                        showToast('New Request Pending Approval', `${data.form_type} from ${data.purok_name}`, 'green');
                                        
                                        // Reload page after 2 seconds to show new request
                                        console.log('⏱️ Scheduling page reload in 2 seconds...');
                                        setTimeout(() => {
                                            console.log('🔄 Reloading page now...');
                                            window.location.reload();
                                        }, 2000);
                                    })
                                    .error((error) => {
                                        console.error('❌ WebSocket Error:', error);
                                    });
                                    
                                console.log('✅ All listeners registered successfully!');
                            } else {
                                console.error('❌ Echo is not available! WebSocket notifications will not work.');
                                console.log('Make sure Laravel Reverb is running and Echo is properly configured.');
                            }
                            
                            // Request notification permission
                            if ('Notification' in window && Notification.permission === 'default') {
                                Notification.requestPermission();
                            }
                        });
                        
                        // Toast notification function
                        function showToast(title, message, color = 'green') {
                            const colorClasses = {
                                'green': 'bg-green-600',
                                'blue': 'bg-blue-600',
                                'red': 'bg-red-600',
                                'yellow': 'bg-yellow-600'
                            };
                            
                            const bgColor = colorClasses[color] || 'bg-green-600';
                            
                            const toast = document.createElement('div');
                            toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-up`;
                            toast.innerHTML = `
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
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
                    </script>
                @endpush

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Request</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="rejection_reason" 
                            id="rejection_reason" 
                            rows="4" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button 
                            type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors duration-200">
                            Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openRejectModal(requestId) {
                const modal = document.getElementById('rejectModal');
                const form = document.getElementById('rejectForm');
                form.action = '{{ route("requests.reject", "") }}' + '/' + requestId;
                modal.classList.remove('hidden');
            }

            function closeRejectModal() {
                const modal = document.getElementById('rejectModal');
                const form = document.getElementById('rejectForm');
                form.reset();
                modal.classList.add('hidden');
            }

            // Close modal when clicking outside
            document.getElementById('rejectModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRejectModal();
                }
            });
        </script>
    @endpush

@endsection