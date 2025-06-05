<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Kalawag Dos – AKSYON AGAD!</title>

    <!-- Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 font-[Inter]">

    <!-- Header -->
    <header class="bg-white shadow-md py-4 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left text-lg md:text-xl font-semibold text-blue-600 leading-tight">
                INTEGRATED BARANGAY–PUROK TRANSACTION AND INCIDENT REPORT MANAGEMENT SYSTEM
            </div>

            @if (Route::has('login'))
                <nav class="flex flex-wrap justify-center md:justify-end gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-blue-600 hover:text-blue-800 transition">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 py-16">
        <div class="max-w-4xl w-full text-center space-y-10 animate-fade-in">
            <div class="space-y-6">
                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight">
                    KAYANG-KAYA BASTA'T SAMA-SAMA!
                    <span class="block text-blue-600 mt-2">SA BARANGAY KALAWAG DOS, AYOS!</span>
                </h1>
                <h2 class="text-2xl md:text-4xl font-bold text-gray-700">
                    SA BARANGAY KALAWAG DOS,<br>
                    <span class="text-blue-600">AKSYON AGAD!</span>
                </h2>
            </div>

            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Welcome to the official online portal of Barangay Kalawag Dos. Access services and submit reports quickly, anytime.
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Barangay Kalawag Dos. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Simple Fade-In Animation -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.7s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
