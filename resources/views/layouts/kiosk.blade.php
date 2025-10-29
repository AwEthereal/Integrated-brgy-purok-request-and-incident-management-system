<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Barangay Kalawag Dos - Information Kiosk</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Kiosk-specific styles */
        body {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Custom scrollbar for better UX */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Touch-friendly buttons */
        .kiosk-button {
            min-height: 80px;
            min-width: 200px;
            font-size: 1.25rem;
            transition: all 0.2s ease;
        }

        .kiosk-button:active {
            transform: scale(0.95);
        }

        /* Idle timeout overlay */
        .idle-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .idle-overlay.active {
            display: flex;
        }

        /* Screensaver animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .screensaver-logo {
            animation: float 3s ease-in-out infinite;
        }

        /* Prevent text selection */
        * {
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 to-blue-100">
    <!-- Header - Fixed at Top -->
    <header class="fixed top-0 left-0 right-0 bg-blue-600 text-white shadow-lg z-50">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <img src="{{ asset('images/Kal2Logo.png') }}" alt="Barangay Logo" class="h-12 sm:h-16 w-auto flex-shrink-0">
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold">Barangay Kalawag Dos</h1>
                        <p class="text-blue-100 text-xs sm:text-sm">Information Kiosk</p>
                    </div>
                </div>
                <div class="text-center sm:text-right">
                    <div class="text-xl sm:text-3xl font-bold" id="kiosk-time">{{ now()->format('h:i A') }}</div>
                    <div class="text-xs sm:text-sm text-blue-100" id="kiosk-date">{{ now()->format('l, F d, Y') }}</div>
                </div>
            </div>
        </div>
    </header>

    <div class="min-h-screen flex flex-col overflow-x-hidden">
        <!-- Spacer for fixed header -->
        <div class="h-20 sm:h-24"></div>

        <!-- Main Content -->
        <main class="flex-1 container mx-auto px-4 sm:px-6 py-4 sm:py-8 overflow-y-auto">
            <div class="w-full max-w-full">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-blue-600 text-white py-3 sm:py-4 flex-shrink-0">
            <div class="container mx-auto px-4 sm:px-6 text-center">
                <p class="text-xs sm:text-sm">Touch anywhere to continue • Automatic reset after 2 minutes of inactivity</p>
            </div>
        </footer>
    </div>

    <!-- Idle Timeout Overlay / Screensaver -->
    <div class="idle-overlay" id="idle-overlay">
        <div class="text-center text-white">
            <div class="screensaver-logo mb-8">
                <img src="{{ asset('images/Kal2Logo.png') }}" alt="Barangay Logo" class="h-32 w-auto mx-auto mb-4">
                <h2 class="text-4xl font-bold mb-2">Barangay Kalawag Dos</h2>
                <p class="text-xl text-blue-200">Information Kiosk</p>
            </div>
            <p class="text-2xl mt-8 animate-pulse">Touch anywhere to start</p>
        </div>
    </div>

    @stack('scripts')

    <!-- Kiosk JavaScript -->
    <script>
        // Update time and date
        function updateDateTime() {
            const now = new Date();
            
            // Update time
            const timeElement = document.getElementById('kiosk-time');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    hour12: true 
                });
            }
            
            // Update date
            const dateElement = document.getElementById('kiosk-date');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
        }

        // Update every second
        setInterval(updateDateTime, 1000);

        // Idle timeout functionality
        let idleTimer = null;
        const IDLE_TIMEOUT = 120000; // 2 minutes in milliseconds
        const overlay = document.getElementById('idle-overlay');

        function resetIdleTimer() {
            // Clear existing timer
            if (idleTimer) {
                clearTimeout(idleTimer);
            }

            // Hide overlay if active
            if (overlay.classList.contains('active')) {
                overlay.classList.remove('active');
            }

            // Set new timer
            idleTimer = setTimeout(() => {
                showScreensaver();
            }, IDLE_TIMEOUT);
        }

        function showScreensaver() {
            overlay.classList.add('active');
            
            // Reset to home page after showing screensaver
            setTimeout(() => {
                window.location.href = '{{ route('kiosk.index') }}';
            }, 5000); // Show screensaver for 5 seconds before reset
        }

        // Listen for user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, resetIdleTimer, true);
        });

        // Start idle timer on page load
        resetIdleTimer();

        // Prevent right-click context menu
        document.addEventListener('contextmenu', event => event.preventDefault());

        // Prevent text selection on double-click
        document.addEventListener('selectstart', event => event.preventDefault());

        // Log kiosk activity (optional)
        console.log('Kiosk mode active - Idle timeout: 2 minutes');
    </script>
</body>
</html>
