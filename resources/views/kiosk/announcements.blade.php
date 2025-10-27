@extends('layouts.kiosk')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('kiosk.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 rounded-xl shadow-lg text-lg font-semibold text-gray-700 transition">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Home
        </a>
    </div>

    <!-- Page Title -->
    <div class="text-center mb-10">
        <h2 class="text-5xl font-bold text-blue-900 mb-4">Announcements</h2>
        <p class="text-2xl text-gray-700">Latest news and updates - Tap to read more</p>
    </div>

    <!-- Announcements List -->
    <div class="space-y-8">
        @forelse($announcements as $announcement)
            @php
                $colors = [
                    'emergency' => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'icon' => 'text-red-600', 'iconBg' => 'bg-red-100'],
                    'event' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'icon' => 'text-blue-600', 'iconBg' => 'bg-blue-100'],
                    'notice' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-500', 'icon' => 'text-yellow-600', 'iconBg' => 'bg-yellow-100'],
                    'general' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-400', 'icon' => 'text-gray-600', 'iconBg' => 'bg-gray-100'],
                ];
                $color = $colors[$announcement->category] ?? $colors['general'];
            @endphp
            
            <button onclick="openModal({{ $announcement->id }})" class="w-full bg-white rounded-2xl shadow-xl p-8 border-l-8 {{ $color['border'] }} relative hover:shadow-2xl transition-shadow cursor-pointer text-left">
                @if($announcement->is_featured)
                    <!-- Red Dot Indicator for Featured Announcement -->
                    <span class="absolute top-4 right-4 flex h-5 w-5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500"></span>
                    </span>
                @endif
                
                <div class="flex items-start">
                    <div class="w-16 h-16 {{ $color['iconBg'] }} rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                        @if($announcement->category === 'emergency')
                            <svg class="w-10 h-10 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        @elseif($announcement->category === 'event')
                            <svg class="w-10 h-10 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="w-10 h-10 {{ $color['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 pr-8">
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $announcement->title }}</h3>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                        @if($announcement->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($announcement->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($announcement->priority === 'low') bg-gray-100 text-gray-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($announcement->priority) }} Priority
                                    </span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($announcement->category) }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm text-gray-500 block">{{ $announcement->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-400">{{ $announcement->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                        <p class="text-xl text-gray-700 leading-relaxed line-clamp-3">{{ $announcement->content }}</p>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                            <p class="text-sm text-gray-500">Posted by: {{ $announcement->creator->name }}</p>
                            <span class="text-blue-600 font-semibold flex items-center">
                                Tap to read more
                                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </button>

            <!-- Modal for this announcement -->
            <div id="modal-{{ $announcement->id }}" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeModal({{ $announcement->id }})">
                <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex items-center justify-between">
                        <h3 class="text-3xl font-bold text-gray-800">{{ $announcement->title }}</h3>
                        <button onclick="closeModal({{ $announcement->id }})" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($announcement->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($announcement->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($announcement->priority === 'low') bg-gray-100 text-gray-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($announcement->priority) }} Priority
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($announcement->category) }}
                            </span>
                            @if($announcement->is_featured)
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Featured
                                </span>
                            @endif
                        </div>
                        <div class="text-xl text-gray-700 leading-relaxed whitespace-pre-line mb-6">
                            {{ $announcement->content }}
                        </div>
                        <div class="pt-6 border-t border-gray-200 flex items-center justify-between text-sm text-gray-500">
                            <p>Posted by: <span class="font-semibold">{{ $announcement->creator->name }}</span></p>
                            <p>{{ $announcement->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-600 mb-2">No Announcements Yet</h3>
                <p class="text-lg text-gray-500">Check back later for updates and news from the barangay.</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endpush
@endsection
