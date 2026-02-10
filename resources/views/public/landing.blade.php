<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <title>Barangay Services</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="max-w-6xl mx-auto px-4 md:px-6 py-6 space-y-8">
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-5 py-8 md:px-8 md:py-10">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl md:text-4xl font-bold">Public Services</h1>
                    <p class="mt-2 text-blue-100">Quick access to common services without logging in.</p>
                </div>
                <a href="{{ route('public.feedback.general') }}"
                   class="hidden sm:inline-flex shrink-0 items-center px-4 py-2 bg-white/15 hover:bg-white/20 text-white rounded-lg text-sm font-semibold border border-white/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.2-3.6A7.37 7.37 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Feedback
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            @isset($announcements)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-4 py-3 flex items-center justify-between">
                        <h2 class="text-base md:text-lg font-semibold text-gray-900">Announcements</h2>
                        <a href="{{ route('public.announcements.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                    </div>

                    <div class="sm:hidden px-4 pb-4">
                        <details class="bg-gray-50 rounded-lg border border-gray-100">
                            <summary class="cursor-pointer select-none px-3 py-2 font-semibold text-gray-900">Recent announcements</summary>
                            <div class="px-3 pb-3">
                                @if($announcements->isEmpty())
                                    <p class="text-gray-600 text-sm">No announcements at the moment.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach($announcements->take(3) as $a)
                                            <a href="{{ route('public.announcements.show', $a) }}" class="block bg-white rounded-lg border border-gray-100 p-3 hover:border-gray-200 transition">
                                                <div class="flex items-center gap-2 mb-1">
                                                    @if($a->is_featured)
                                                        <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                                                    @endif
                                                    <h3 class="font-semibold text-gray-900">{{ $a->title }}</h3>
                                                </div>
                                                <p class="text-gray-600 text-sm">{{ $a->excerpt }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </details>
                    </div>

                    <div class="hidden sm:block px-4 pb-4">
                        @if($announcements->isEmpty())
                            <p class="text-gray-600 text-sm">No announcements at the moment.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($announcements->take(3) as $a)
                                    <a href="{{ route('public.announcements.show', $a) }}" class="block bg-gray-50 rounded-lg border border-gray-100 p-3 hover:bg-white hover:border-gray-200 transition">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($a->is_featured)
                                                <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                                            @endif
                                            <h3 class="font-semibold text-gray-900">{{ $a->title }}</h3>
                                        </div>
                                        <p class="text-gray-600 text-sm">{{ $a->excerpt }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endisset

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="text-lg md:text-xl font-semibold mb-2">Request Purok Clearance</h2>
                <p class="text-gray-600 mb-4">Submit a clearance request with optional identity verification.</p>
                <a href="{{ route('public.clearance.create') }}" class="inline-flex items-center justify-center w-full px-4 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Start Request</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="text-lg md:text-xl font-semibold mb-2">Report an Incident</h2>
            <p class="text-gray-600 mb-4">Send details about emergencies or community concerns for action.</p>
            <a href="{{ route('public.incident.create') }}" class="inline-flex items-center justify-center w-full px-4 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700">Report Now</a>
        </div>
    </div>
</div>

<a href="{{ route('public.feedback.general') }}"
   class="fixed bottom-5 right-5 z-50 sm:hidden inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-600 text-white shadow-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
   aria-label="Send Feedback">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.2-3.6A7.37 7.37 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
</a>
</body>
</html>
