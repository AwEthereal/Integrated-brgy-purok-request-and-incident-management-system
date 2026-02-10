<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#16a34a">
    <title>Barangay Announcements</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Header -->
        <div class="bg-gray-800 dark:bg-gray-900 rounded-lg shadow-md p-3 sm:p-6 mb-4 sm:mb-8">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-3xl font-bold text-white">Barangay Announcements</h1>
                    <p class="mt-1 sm:mt-2 text-xs sm:text-base text-gray-300">Stay updated with the latest news and announcements</p>
                </div>
                <a href="{{ route('public.landing') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-white/40 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Announcements List -->
        <div class="space-y-4 sm:space-y-6">
            @forelse($announcements as $announcement)
                @php
                    $colors = [
                        'emergency' => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'icon' => 'text-red-600', 'iconBg' => 'bg-red-100'],
                        'event' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'icon' => 'text-blue-600', 'iconBg' => 'bg-blue-100'],
                        'notice' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-500', 'icon' => 'text-yellow-600', 'iconBg' => 'bg-yellow-100'],
                        'general' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-500', 'icon' => 'text-gray-600', 'iconBg' => 'bg-gray-100'],
                    ];
                    $color = $colors[$announcement->category] ?? $colors['general'];
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 {{ $color['border'] }} relative overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    @if($announcement->is_featured)
                        <!-- Featured Badge -->
                        <div class="absolute top-2 right-2 sm:top-3 sm:right-3 z-10">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-500 text-white flex items-center shadow-sm">
                                <span class="w-1.5 h-1.5 bg-white rounded-full mr-1 animate-pulse"></span>
                                <span>New</span>
                            </span>
                        </div>
                    @endif

                    <div class="p-3 sm:p-5">
                        <div class="flex items-start gap-3">
                            <!-- Icon -->
                            <div class="w-10 h-10 sm:w-12 sm:h-12 {{ $color['iconBg'] }} rounded-full flex items-center justify-center flex-shrink-0">
                                @if($announcement->category === 'emergency')
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                @elseif($announcement->category === 'event')
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($announcement->category === 'notice')
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="mb-2">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-1.5 pr-12 sm:pr-0 line-clamp-2 break-words">{{ $announcement->title }}</h3>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="px-1.5 py-0.5 text-xs font-medium rounded
                                            @if($announcement->priority === 'urgent') bg-red-100 text-red-700
                                            @elseif($announcement->priority === 'high') bg-orange-100 text-orange-700
                                            @elseif($announcement->priority === 'low') bg-gray-100 text-gray-700
                                            @else bg-green-100 text-green-700
                                            @endif">
                                            {{ ucfirst($announcement->priority) }}
                                        </span>
                                        <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-700">
                                            {{ ucfirst($announcement->category) }}
                                        </span>
                                    </div>
                                </div>

                                @php
                                    $contentLength = strlen($announcement->content);
                                    $isLong = $contentLength > 150;
                                    $preview = $isLong ? substr($announcement->content, 0, 150) . '...' : $announcement->content;
                                @endphp

                                <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-2">
                                    <p class="line-clamp-3 break-words">{{ $preview }}</p>
                                </div>

                                @if($isLong)
                                    <button onclick="openAnnouncementModal({{ $announcement->id }})"
                                        class="inline-flex items-center text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors mb-2">
                                        <span>Read More</span>
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @endif

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('public.announcements.show', $announcement) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">View announcement</a>
                                    <div class="flex items-center gap-4 flex-wrap">
                                        <div class="flex items-center max-w-full sm:max-w-[12rem] min-w-0">
                                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="truncate">{{ $announcement->creator->name }}</span>
                                        </div>
                                        <div class="flex items-center flex-shrink-0">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $announcement->created_at->format('M d') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow-lg p-8 sm:p-12 text-center">
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-600 dark:text-gray-400 mb-2">No Announcements Yet</h3>
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-500">Check back later for updates and news from the barangay.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="mt-6 sm:mt-8">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Announcement Modal -->
<div id="announcementModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeAnnouncementModal(event)">
    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="bg-gray-800 dark:bg-gray-900 p-4 sm:p-6 flex items-start justify-between">
            <div class="flex-1 pr-4">
                <h2 id="modalTitle" class="text-xl sm:text-2xl font-bold text-white"></h2>
                <div id="modalBadges" class="flex items-center gap-2 flex-wrap mt-2"></div>
            </div>
            <button onclick="closeAnnouncementModal()" class="text-gray-400 hover:text-white transition-colors flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4 sm:p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
            <div id="modalContent" class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line"></div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 dark:bg-gray-900 p-4 sm:p-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span id="modalAuthor"></span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="modalDate"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const announcements = @json($announcements->items());

    function openAnnouncementModal(id) {
        const announcement = announcements.find(a => a.id === id);
        if (!announcement) return;

        // Set modal content
        document.getElementById('modalTitle').textContent = announcement.title;
        document.getElementById('modalContent').textContent = announcement.content;
        document.getElementById('modalAuthor').textContent = 'Posted by: ' + announcement.creator.name;
        document.getElementById('modalDate').textContent = new Date(announcement.created_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // Set badges
        const badgesHtml = `
            <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                announcement.priority === 'urgent' ? 'bg-red-100 text-red-800' :
                announcement.priority === 'high' ? 'bg-orange-100 text-orange-800' :
                announcement.priority === 'low' ? 'bg-gray-100 text-gray-800' :
                'bg-green-100 text-green-800'
            }">
                ${announcement.priority.charAt(0).toUpperCase() + announcement.priority.slice(1)} Priority
            </span>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                ${announcement.category.charAt(0).toUpperCase() + announcement.category.slice(1)}
            </span>
        `;
        document.getElementById('modalBadges').innerHTML = badgesHtml;

        // Show modal
        document.getElementById('announcementModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAnnouncementModal(event) {
        if (event && event.target.id !== 'announcementModal') return;
        document.getElementById('announcementModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAnnouncementModal();
        }
    });
</script>

</body>
</html>
