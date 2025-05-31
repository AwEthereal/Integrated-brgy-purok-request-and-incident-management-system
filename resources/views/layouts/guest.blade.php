<!-- guest.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="background-color:#bbf7d0;" class="font-sans text-gray-900 antialiased bg-green-50">
    <div class="min-h-screen flex items-center justify-center pt-10 pb-10">
        <div class="w-full max-w-sm p-6 bg-green-100 rounded-lg shadow-lg">
            @yield('content')
        </div>
    </div>
</body>

</html>
