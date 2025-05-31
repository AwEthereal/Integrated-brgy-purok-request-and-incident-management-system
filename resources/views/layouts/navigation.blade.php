<nav x-data="{ open: false }" class="bg-white dark:bg-gray-900 border-b border-gray-300 dark:border-gray-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            @vite(['resources/css/app.css', 'resources/js/app.js'])



            <!-- Logo -->
            <a href="{{ url('images/Kal2Logo.png') }}" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {{ config('app.name', 'Laravel') }}
            </a>

            <!-- Desktop navigation -->
            <div class="hidden sm:flex space-x-8 items-center">

                <a href="{{ route('dashboard') }}"
                
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition font-medium">
                    Dashboard
                </a>

                <a href="{{ route('requests.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition font-medium">
                    Requests
                </a>

                <a href="{{ route('incident_reports.create') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition font-medium">
                    Report Incident
                </a>
            </div>

            <!-- User info and hamburger -->
            <div class="flex items-center space-x-4">

                @auth
                    <span class="hidden sm:block text-gray-700 dark:text-gray-300 font-medium truncate max-w-xs">
                        {{ Auth::user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit"
                            class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
                            Logout
                        </button>
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
    <div x-show="open" class="sm:hidden bg-white dark:bg-gray-900 border-t border-gray-300 dark:border-gray-700">
        <div class="flex flex-col px-4 py-3 space-y-1">

            <a href="{{ route('dashboard') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-3 py-2 rounded-md font-medium">
                Dashboard
            </a>

            <a href="{{ route('requests.index') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-3 py-2 rounded-md font-medium">
                Requests
            </a>

            <a href="{{ route('incident_reports.create') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-3 py-2 rounded-md font-medium">
                Report Incident
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition px-3 py-2 rounded-md font-medium">
                        Logout
                    </button>
                </form>
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
