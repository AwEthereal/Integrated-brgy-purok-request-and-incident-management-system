<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#dc2626">
    <title>Public Incident Report</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="max-w-2xl mx-auto px-4 md:px-6 py-6">
    <div class="bg-gradient-to-br from-red-600 to-red-800 text-white py-6 md:py-8 rounded-lg shadow-lg mb-6 md:mb-8">
        <div class="px-4 sm:px-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">Report an Incident</h1>
                <p class="text-red-100 mt-1">Share details so the barangay can take action.</p>
            </div>
            <a href="{{ route('public.landing') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-white/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('public.incident.store') }}" class="space-y-4" novalidate>
        @csrf
        <input type="text" name="website" value="" class="hidden" autocomplete="off">

        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border border-gray-100 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Your Full Name</label>
                <input type="text" name="reporter_name" value="{{ old('reporter_name') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" required autocomplete="name">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Contact Number</label>
                <input type="tel" name="contact_number" inputmode="numeric" pattern="[0-9]*" value="{{ old('contact_number') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" required autocomplete="tel">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email (optional)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" autocomplete="email">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Incident Description</label>
                <textarea name="description" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" required>{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Location (optional)</label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 active:bg-red-800 font-semibold shadow-sm">Submit Incident</button>
        </div>
    </form>

    @if ($errors->any())
        <div class="mt-4 text-red-600 text-sm">
            <ul class="list-disc pl-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<a href="{{ route('public.landing') }}" class="sm:hidden fixed left-4 bottom-4 z-40 inline-flex items-center px-4 py-3 rounded-full bg-white shadow-lg border border-gray-200 text-gray-700 active:bg-gray-50">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Back
</a>
</body>
</html>
