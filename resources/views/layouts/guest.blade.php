<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Barangay Kalawag II'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Kal2Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 bg-gradient-to-br from-green-50 to-blue-50">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-2xl fade-in">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="px-6 py-8 sm:p-10">
                    <div class="text-center">
                        <a href="{{ url('/') }}" class="block">
                            <img class="mx-auto h-24 w-24" src="{{ asset('images/Kal2Logo.png') }}" alt="Barangay Kalawag II Logo">
                        </a>
                        <h1 class="mt-4 text-2xl font-bold text-gray-900 sm:text-3xl">
                            @yield('heading', 'Barangay Kalawag II')
                        </h1>
                        <p class="mt-2 text-sm text-gray-600">
                            @yield('subheading', 'City of Isulan, Sultan Kudarat')
                        </p>
                    </div>

                    <div class="mt-8">
                        @yield('content')
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 sm:px-10 border-t border-gray-200">
                    <div class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} Barangay Kalawag II. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>