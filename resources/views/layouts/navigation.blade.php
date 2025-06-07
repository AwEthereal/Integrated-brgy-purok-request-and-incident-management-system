<nav x-data="{ open: false }" class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            @vite(['resources/css/app.css', 'resources/js/app.js'])

            <!-- Logo -->
            <a href="{{ in_array(auth()->user()->role, ['purok_leader', 'purok_president']) ? route('purok_leader.dashboard') : route('dashboard') }}" class="flex-shrink-0 flex items-center">
                <img src="{{ asset('images/Kal2Logo.png') }}" alt="Barangay Kalawag Logo" class="h-16 w-auto">
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-2">
                @if(!in_array(auth()->user()->role, ['purok_leader', 'purok_president']))
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                    <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('requests.index') }}"
                    class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('requests.*') && !request()->routeIs('requests.pending-*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                    <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>My Purok Clearance Requests</span>
                </a>
@endif

                @auth
                    @if(auth()->user()->role === 'purok_leader' || auth()->user()->role === 'purok_president')
                        <a href="{{ route('purok_leader.dashboard') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('purok_leader.dashboard') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Purok Dashboard</span>
                        </a>
                        
                        <a href="{{ route('purok_leader.residents') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('purok_leader.residents') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Purok Residents</span>
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <!-- Admin can access purok approvals through the dashboard -->
                    @endif

                    @if(auth()->user()->role === 'barangay_official' || auth()->user()->role === 'admin')
                        <!-- Barangay Official Links -->
                        <a href="{{ route('requests.pending-barangay') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('requests.pending-barangay') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Barangay Approvals</span>
                            @php
                                $pendingBarangayCount = \App\Models\Request::where('status', 'purok_approved')->count();
                            @endphp
                            @if($pendingBarangayCount > 0)
                                <span
                                    class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                    {{ $pendingBarangayCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @if(auth()->user()->role === 'resident')
                        <!-- Resident Links -->
                        <a href="{{ route('incident_reports.my_reports') }}"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('incident_reports.my_reports') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100 hover:text-green-700' }} transition-colors">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>My Incident Reports</span>
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
            @if(!in_array(auth()->user()->role, ['purok_leader', 'purok_president']))
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('requests.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('requests.*') && !request()->routeIs('requests.pending-*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span>My Purok Clearance Requests</span>
                </a>
            @elseif(auth()->user()->role === 'purok_leader' || auth()->user()->role === 'purok_president')
                <a href="{{ route('purok_leader.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('purok_leader.dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span class="text-center">Purok Dashboard</span>
                </a>
                <a href="{{ route('purok_leader.residents') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('purok_leader.residents') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                    <span class="text-center">Purok Residents</span>
                </a>
            @endif

            @auth
                @if(auth()->user()->role === 'purok_leader' || auth()->user()->role === 'admin')
                    @php
                        $pendingPurokCount = \App\Models\Request::where('status', 'pending')->count();
                    @endphp
                    <a href="{{ route('requests.pending-purok') }}"
                        class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('requests.pending-purok') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        <span>Purok Approvals</span>
                        @if($pendingPurokCount > 0)
                            <span
                                class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingPurokCount }}
                            </span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->role === 'barangay_official' || auth()->user()->role === 'admin')
                    @php
                        $pendingBarangayCount = \App\Models\Request::where('status', 'purok_approved')->count();
                    @endphp
                    <a href="{{ route('requests.pending-barangay') }}"
                        class="flex justify-between items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('requests.pending-barangay') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-green-700' }} transition-colors">
                        <span>Barangay Approvals</span>
                        @if($pendingBarangayCount > 0)
                            <span
                                class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingBarangayCount }}
                            </span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->role === 'resident')
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