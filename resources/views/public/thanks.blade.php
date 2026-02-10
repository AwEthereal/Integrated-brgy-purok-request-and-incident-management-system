<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#16a34a">
    <title>Thank You</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="max-w-lg mx-auto px-4 md:px-6 py-12 text-center space-y-4">
    <div class="mx-auto w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
        <svg class="w-8 h-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h1 class="text-2xl md:text-3xl font-bold">Thank you!</h1>
    <p class="text-gray-600">Your submission was received. We will review it as soon as possible.</p>
    <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ '/' . ltrim(route('public.landing', [], false), '/') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Back to Public Services</a>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-lg border border-gray-300 hover:bg-gray-50">Go to Homepage</a>
    </div>
</div>
</body>
</html>
