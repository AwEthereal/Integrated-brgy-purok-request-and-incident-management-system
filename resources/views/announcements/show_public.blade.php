<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#16a34a">
  <title>{{ $announcement->title }}</title>
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css'])
  @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="py-6">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-4">
      <a href="{{ route('public.announcements.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to announcements
      </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $announcement->title }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
              <span class="px-2 py-1 rounded-full font-semibold
                @if($announcement->priority === 'urgent') bg-red-100 text-red-800
                @elseif($announcement->priority === 'high') bg-orange-100 text-orange-800
                @elseif($announcement->priority === 'low') bg-gray-100 text-gray-800
                @else bg-green-100 text-green-800
                @endif">
                {{ ucfirst($announcement->priority) }}
              </span>
              <span class="px-2 py-1 rounded-full font-semibold bg-blue-100 text-blue-800">
                {{ ucfirst($announcement->category) }}
              </span>
              @if($announcement->is_featured)
                <span class="px-2 py-1 rounded-full font-semibold bg-red-100 text-red-800">Featured</span>
              @endif
            </div>
          </div>
          <div class="text-right text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
            <div>{{ optional($announcement->published_at ?? $announcement->created_at)->format('M d, Y') }}</div>
            <div>{{ optional($announcement->published_at ?? $announcement->created_at)->format('h:i A') }}</div>
          </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
          <div class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $announcement->content }}</div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
          <div>Posted by: <span class="font-medium">{{ $announcement->creator->name ?? 'Barangay' }}</span></div>
          <div>Announcement ID: {{ $announcement->id }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
