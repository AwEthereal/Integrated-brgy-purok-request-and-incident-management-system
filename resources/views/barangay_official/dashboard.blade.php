@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-8 px-4 sm:px-6 lg:px-8 rounded-lg shadow-lg mb-8">
        <div class="max-w-7xl mx-auto">
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
                        <p class="text-2xl font-bold">{{ count($pendingRequests) }}</p>
                        <p class="text-sm">Pending Requests</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $incidents->whereIn('status', ['pending', 'in_progress'])->count() }}</p>
                        <p class="text-sm">Active Incidents</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Purok Filter Section -->
        <div class="mb-8 bg-white rounded-lg shadow-sm p-4">
            @include('barangay_official.partials.purok_filter', ['puroks' => $puroks, 'selectedPurok' => $selectedPurok])
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Pending Requests Section -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Requests Awaiting Barangay Approval
                        @if(count($pendingRequests) > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                {{ count($pendingRequests) }} {{ Str::plural('request', count($pendingRequests)) }}
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
                                    <!-- Request Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ $request->user->name }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ format_label($request->form_type) }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ format_label($request->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500" title="{{ $request->created_at->format('M d, Y h:i A') }}">
                                            {{ $request->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Request Purpose -->
                                    <p class="text-sm text-gray-600 mb-3">{{ $request->purpose }}</p>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                                        <!-- Approve Button -->
                                        <form action="{{ route('requests.approve-barangay', $request->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to approve this request?')">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <!-- View Details Button -->
                                        <a href="{{ route('requests.show', $request->id) }}" 
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                        
                                        <!-- Reject Button -->
                                        <button type="button" 
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors duration-200"
                                            onclick="openRejectModal({{ $request->id }})">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No pending clearance requests found.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Incidents Section -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Active Incidents
                        @if(count($incidents) > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                {{ count($incidents) }} {{ Str::plural('incident', count($incidents)) }}
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
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $incident->user->name }}</p>
                                        </div>
                                        <div class="flex-1 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $incident->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ format_label($incident->status) }}
                                            </span>
                                        </div>
                                        <div class="w-1/3 flex justify-end">
                                            <a href="{{ route('incident_reports.show', $incident->id) }}" class="text-gray-400 hover:text-blue-600" title="View Details">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ \App\Models\IncidentReport::TYPES[$incident->incident_type] ?? format_label($incident->incident_type) }}
                                        </span>
                                        <span class="text-xs text-gray-500" title="{{ $incident->created_at->format('M d, Y h:i A') }}">
                                            {{ $incident->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                    </style>
                @endpush

                @push('scripts')
                    <script>
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

                        // WebSocket listener for new incident reports
                        document.addEventListener('DOMContentLoaded', function() {
                            if (window.Echo) {
                                console.log('Setting up incident report listener...');
                                
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
                                        
                                        // Update incident count badge
                                        const badge = document.querySelector('.bg-white.bg-opacity-20 p:first-child');
                                        if (badge) {
                                            badge.textContent = data.incidentCount;
                                        }
                                        
                                        // Update yellow dot in navigation
                                        updateDashboardDot();
                                        
                                        // Show toast notification
                                        showToast('New Incident Report', `${data.incident_type} from ${data.purok_name}`);
                                    });
                            }
                            
                            // Request notification permission
                            if ('Notification' in window && Notification.permission === 'default') {
                                Notification.requestPermission();
                            }
                        });
                        
                        // Toast notification function
                        function showToast(title, message) {
                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-up';
                            toast.innerHTML = `
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
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
                form.action = `/requests/${requestId}/reject`;
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