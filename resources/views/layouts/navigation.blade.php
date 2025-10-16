<nav x-data="{ open: false }" class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            @vite(['resources/css/app.css', 'resources/js/app.js'])

            <!-- Logo -->
            <a href="{{ auth()->check() && in_array(auth()->user()->role, ['purok_leader', 'purok_president']) ? route('purok_leader.dashboard') : route('dashboard') }}" class="flex-shrink-0 flex items-center">
                <img src="{{ asset('images/Kal2Logo.png') }}" alt="Barangay Kalawag Logo" class="h-16 w-auto">
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-2">
                @if(auth()->check() && !in_array(auth()->user()->role, ['purok_leader', 'purok_president']))
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-green-100 text-green-800 font-semibold' : 'text-gray-800 hover:bg-gray-100 hover:text-green-800' }} transition-colors">
                        <svg class="h-5 w-5 mr-2 {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium">Dashboard</span>
                        @if(auth()->user()->role === 'resident')
                            @php
                                // Show dot if there are unread requests (not viewed since last update)
                                $hasUnreadRequests = \App\Models\Request::where('user_id', auth()->id())
                                    ->whereIn('status', ['purok_approved', 'barangay_approved', 'rejected'])
                                    ->where(function($query) {
                                        $query->whereNull('last_viewed_at')
                                              ->orWhereColumn('updated_at', '>', 'last_viewed_at');
                                    })
                                    ->exists();
                                
                                // Check for unread incident reports
                                $hasUnreadIncidents = \App\Models\IncidentReport::where('user_id', auth()->id())
                                    ->whereIn('status', ['in_progress', 'resolved'])
                                    ->where('updated_at', '>=', now()->subHours(2))
                                    ->exists();
                                    
                                $showDashboardDot = $hasUnreadRequests || $hasUnreadIncidents;
                            @endphp
                            @if($showDashboardDot && !request()->routeIs('dashboard'))
                                <span class="ml-2 relative inline-flex">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                </span>
                            @endif
                        @elseif(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin']))
                            @php
                                // Show dot if there are incidents/requests needing action
                                $hasIncidentsNeedingAction = \App\Models\IncidentReport::whereIn('status', ['pending', 'in_progress'])->exists();
                                $hasRequestsNeedingAction = \App\Models\Request::where('status', 'purok_approved')->exists();
                                $hasItemsNeedingAction = $hasIncidentsNeedingAction || $hasRequestsNeedingAction;
                            @endphp
                            @if($hasItemsNeedingAction && !request()->routeIs('dashboard'))
                                <span class="ml-2 relative inline-flex">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                </span>
                            @endif
                        @endif
                    </a>
                    {{-- Only show "My Purok Clearance Requests" for residents, not for barangay officials --}}
                    @if(auth()->user()->role === 'resident' && auth()->user()->is_approved)
                        <a href="{{ route('requests.index') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('requests.*') && !request()->routeIs('requests.pending-*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>My Purok Clearance Requests</span>
                            @php
                                // Show dot only for requests requiring ACTION (rejected = resubmit, completed = pickup)
                                $hasRequestsNeedingAttention = \App\Models\Request::where('user_id', auth()->id())
                                    ->whereIn('status', ['rejected', 'completed'])
                                    ->where('updated_at', '>=', now()->subHours(48))
                                    ->exists();
                            @endphp
                            @if($hasRequestsNeedingAttention && !request()->routeIs('requests.*'))
                                <span class="ml-2 relative inline-flex">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                </span>
                            @endif
                        </a>
                    @endif
                @endif

                @auth
                    @if(auth()->user()->role === 'purok_leader' || auth()->user()->role === 'purok_president')
                        <a href="{{ route('purok_leader.dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('purok_leader.dashboard') ? 'bg-green-100 text-green-800 font-semibold' : 'text-gray-800 hover:bg-gray-100 hover:text-green-800' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 {{ request()->routeIs('purok_leader.dashboard') ? 'text-green-600' : 'text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="font-medium">Purok Dashboard</span>
                            @php
                                // Show dot if there are requests needing purok leader's action (pending status)
                                $hasPendingRequests = \App\Models\Request::where('purok_id', auth()->user()->purok_id)
                                    ->where('status', 'pending')
                                    ->exists();
                            @endphp
                            @if($hasPendingRequests && !request()->routeIs('purok_leader.dashboard'))
                                <span class="ml-2 relative inline-flex">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('purok_leader.residents') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('purok_leader.residents') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Purok Residents</span>
                        </a>
                        
                        <a href="{{ route('purok_leader.purok_change_requests') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('purok_leader.purok_change_requests') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>Purok Change Requests</span>
                            @php
                                $pendingChangeRequestsCount = \App\Models\PurokChangeRequest::where('requested_purok_id', auth()->user()->purok_id)
                                    ->where('status', 'pending')
                                    ->count();
                            @endphp
                            @if($pendingChangeRequestsCount > 0)
                                <span class="ml-2 relative inline-flex items-center">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-yellow-500 rounded-full">
                                        {{ $pendingChangeRequestsCount }}
                                    </span>
                                </span>
                            @endif
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <!-- Admin Navigation Links -->
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>User Management</span>
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin']))
                        <!-- Barangay Official Links -->
                        @php
                            $isHistoryActive = request()->routeIs('barangay.approvals.*') && request('status') === 'completed';
                            $pendingBarangayCount = \App\Models\Request::where('status', 'purok_approved')->count();
                        @endphp

                        <a href="{{ route('barangay.approvals.index', ['status' => 'completed']) }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ $isHistoryActive ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Request History</span>
                        </a>
                        
                        <!-- Incident Reports History Link -->
                        <a href="{{ route('barangay.incident_reports.index') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('barangay.incident_reports.*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Incident History</span>
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'admin']))
                        <!-- Reports Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-green-700 focus:outline-none focus:bg-gray-100 focus:text-green-700 transition-colors">
                                <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Reports</span>
                                <svg class="ml-1 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <a href="{{ route('reports.residents') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-700">
                                        <span>Residents List</span>
                                    </a>
                                    <a href="{{ route('reports.purok-leaders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-700">
                                        <span>Purok Leaders</span>
                                    </a>
                                    <a href="{{ route('reports.purok-clearance') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-700">
                                        <span>Purok Clearance Requests</span>
                                    </a>
                                    <a href="{{ route('reports.incident-reports') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-700">
                                        <span>Incident Reports</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if(auth()->user()->role === 'resident' && auth()->user()->is_approved)
                        <!-- Resident Links - Only show for approved residents -->
                        <a href="{{ route('incident_reports.my_reports') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('incident_reports.my_reports') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>My Incident Reports</span>
                            @php
                                // Show dot for new incident updates (informational, disappears after viewing page)
                                $hasNewIncidentUpdates = \App\Models\IncidentReport::where('user_id', auth()->id())
                                    ->whereIn('status', ['in_progress', 'resolved'])
                                    ->where('updated_at', '>=', now()->subHours(24))
                                    ->exists();
                            @endphp
                            @if($hasNewIncidentUpdates && !request()->routeIs('incident_reports.my_reports'))
                                <span class="ml-2 relative inline-flex">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth
            </div>

            <!-- User info and hamburger -->
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('profile.edit') }}"
                        class="hidden sm:flex items-center text-sm font-medium text-gray-700 hover:text-green-700 px-3 py-2 rounded-md hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span
                            class="truncate max-w-[120px]">{{ trim(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="ml-2 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-red-600 transition-colors flex items-center">
                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Log Out
                        </a>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                        class="hidden sm:block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
                        Register
                    </a>
                @endauth

                <!-- Hamburger button -->
                <button @click="open = !open" aria-label="Toggle menu"
                    class="sm:hidden focus:outline-none text-gray-700 dark:text-gray-300">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="sm:hidden bg-white border-t border-gray-200 shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'admin']))
                <!-- Reports Section for Mobile -->
                <div class="px-3 pt-2 pb-1">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">REPORTS</h3>
                </div>
                <a href="{{ route('reports.residents') }}" class="block px-6 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-green-700">
                    Residents List
                </a>
                <a href="{{ route('reports.purok-leaders') }}" class="block px-6 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-green-700">
                    Purok Leaders
                </a>
                <a href="{{ route('reports.purok-clearance') }}" class="block px-6 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-green-700">
                    Purok Clearance Requests
                </a>
                <a href="{{ route('reports.incident-reports') }}" class="block px-6 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-green-700">
                    Incident Reports
                </a>
                <div class="border-t border-gray-200 my-1"></div>
            @endif
            
            @if(!in_array(auth()->user()->role, ['purok_leader', 'purok_president']))
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-800 font-semibold' : 'text-gray-800 hover:bg-gray-50 hover:text-green-800' }} transition-colors">
                    <span class="font-medium">Dashboard</span>
                </a>
                {{-- Only show for residents, not barangay officials --}}
                @if(auth()->user()->role === 'resident' && auth()->user()->is_approved)
                    <a href="{{ route('requests.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('requests.*') && !request()->routeIs('requests.pending-*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        <span>My Purok Clearance Requests</span>
                    </a>
                @endif
            @elseif(auth()->user()->role === 'purok_leader' || auth()->user()->role === 'purok_president')
                <a href="{{ route('purok_leader.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('purok_leader.dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span class="text-center">Purok Dashboard</span>
                </a>
                <a href="{{ route('purok_leader.residents') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('purok_leader.residents') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span class="text-center">Purok Residents</span>
                </a>
                <a href="{{ route('purok_leader.purok_change_requests') }}" class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('purok_leader.purok_change_requests') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span>Purok Change Requests</span>
                    @php
                        $pendingChangeRequestsCount = \App\Models\PurokChangeRequest::where('current_purok_id', auth()->user()->purok_id)
                            ->where('status', 'pending')
                            ->count();
                    @endphp
                    @if($pendingChangeRequestsCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                            {{ $pendingChangeRequestsCount }}
                        </span>
                    @endif
                </a>
            @endif

            @auth
                @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin']))
                    @php
                        $pendingBarangayCount = \App\Models\Request::where('status', 'purok_approved')->count();
                    @endphp
                    
                    <!-- Pending Approvals -->
                    <a href="{{ route('barangay.approvals.index', ['status' => 'pending']) }}"
                        class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('barangay.approvals.*') && request('status') !== 'completed' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        <span>Pending Approvals</span>
                        @if($pendingBarangayCount > 0)
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingBarangayCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Request History -->
                    <a href="{{ route('barangay.approvals.index', ['status' => 'completed']) }}"
                        class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('barangay.approvals.*') && request('status') === 'completed' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        Request History
                    </a>

                    <!-- Incident History -->
                    <a href="{{ route('barangay.incident_reports.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('barangay.incident_reports.*') && !request('status') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        Incident History
                    </a>
                @endif
                
                @if(auth()->user()->role === 'purok_leader' || auth()->user()->role === 'admin')
                    @php
                        $pendingPurokCount = \App\Models\Request::where('status', 'pending')->count();
                    @endphp
                    <a href="{{ route('requests.pending-purok') }}"
                        class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('requests.pending-purok') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        <span>Purok Approvals</span>
                        @if($pendingPurokCount > 0)
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingPurokCount }}
                            </span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->role === 'resident' && auth()->user()->is_approved)
                    <a href="{{ route('incident_reports.my_reports') }}"
                        class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('incident_reports.my_reports') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        <span>My Incident Reports</span>
                    </a>
                @endif
            @endauth

            @auth
                <a href="{{ route('profile.edit') }}"
                    class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('profile.edit') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span>Profile</span>
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-3 py-2 rounded-md font-medium">
                    Login
                </a>

                <a href="{{ route('register') }}"
                    class="block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-3 py-2 rounded-md font-medium">
                    Register
                </a>
            @endauth
        </div>
    </div>
</nav>