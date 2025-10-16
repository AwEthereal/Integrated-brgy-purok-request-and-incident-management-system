<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BP Transaction & Report System') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Kal2Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- User Meta Tags -->
    @auth
        @if(auth()->check() && auth()->user())
            <meta name="user-role" content="{{ auth()->user()->role }}">
            <meta name="purok-id" content="{{ auth()->user()->purok_id }}">
        @endif
    @endauth
    
    <!-- Pusher JS (CDN) -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

    <!-- Custom CSS for Incident Reports -->
    <link href="{{ asset('css/incident-reports.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/global-notifications.js'
    ])
    
    <!-- Notification Sound Script -->
    @auth
        @if(auth()->check() && auth()->user() && in_array(auth()->user()->role, ['purok_leader', 'purok_president']))
            <script>
                window.Laravel = {!! json_encode([
                    'user' => [
                        'id' => auth()->id(),
                        'purok_id' => auth()->user()->purok_id,
                    ]
                ]) !!};
            </script>
        @endif
    @endauth
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
    
</head>
<body class="font-sans antialiased bg-green-50">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Content -->
        <main class="py-6">
            @yield('content')
        </main>
    </div>
    
    <!-- Feedback Prompt -->
    @auth
        <x-feedback-prompt />
    @endauth

    @stack('scripts')
</body>
</html>
