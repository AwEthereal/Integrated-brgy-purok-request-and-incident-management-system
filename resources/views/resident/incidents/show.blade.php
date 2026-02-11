@extends('layouts.app')

@section('title', 'Incident Report Details')

@push('styles')
<style>
    /* Photo Carousel Styles */
    .photo-carousel {
        position: relative;
    }
    
    .photo-slide {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .photo-slide.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .photo-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        min-width: 44px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (min-width: 640px) {
        .photo-nav-btn {
            padding: 12px;
        }
    }
    
    .photo-nav-btn:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: translateY(-50%) scale(1.05);
    }
    
    .photo-nav-btn:active {
        transform: translateY(-50%) scale(0.95);
    }
    
    .photo-nav-prev {
        left: 8px;
    }
    
    @media (min-width: 640px) {
        .photo-nav-prev {
            left: 16px;
        }
    }
    
    .photo-nav-next {
        right: 8px;
    }
    
    @media (min-width: 640px) {
        .photo-nav-next {
            right: 16px;
        }
    }
    
    .photo-thumbnail {
        flex-shrink: 0;
        border: 2px solid transparent;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        cursor: pointer;
        min-width: 44px;
        min-height: 44px;
        padding: 2px;
    }
    
    @media (min-width: 640px) {
        .photo-thumbnail {
            border: 3px solid transparent;
        }
    }
    
    .photo-thumbnail:hover {
        border-color: #3b82f6;
        transform: scale(1.03);
    }
    
    .photo-thumbnail:active {
        transform: scale(0.97);
    }
    
    .photo-thumbnail.active {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
    }
    
    .photo-thumbnail img {
        display: block;
        border-radius: 0.25rem;
    }
    
    /* Custom scrollbar for webkit browsers */
    .scrollbar-thin::-webkit-scrollbar {
        height: 6px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    /* Lightbox Modal Styles */
    .photo-lightbox {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
        animation: fadeIn 0.2s ease-in;
    }
    
    .photo-lightbox.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lightbox-content {
        max-width: 95%;
        max-height: 95%;
        object-fit: contain;
        animation: zoomIn 0.3s ease-out;
    }
    
    .lightbox-close {
        position: absolute;
        top: 15px;
        right: 25px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        background: rgba(0, 0, 0, 0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 10000;
    }
    
    .lightbox-close:hover {
        background: rgba(255, 0, 0, 0.7);
        transform: rotate(90deg);
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
        background: rgba(0, 0, 0, 0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        user-select: none;
        z-index: 10000;
    }
    
    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) scale(1.1);
    }
    
    .lightbox-prev {
        left: 25px;
    }
    
    .lightbox-next {
        right: 25px;
    }
    
    .lightbox-counter {
        position: absolute;
        bottom: 25px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: 18px;
        background: rgba(0, 0, 0, 0.7);
        padding: 10px 20px;
        border-radius: 25px;
        z-index: 10000;
    }
    
    @keyframes zoomIn {
        from { 
            transform: scale(0.5);
            opacity: 0;
        }
        to { 
            transform: scale(1);
            opacity: 1;
        }
    }
    
    /* Make photos clickable */
    .photo-slide img {
        cursor: pointer;
    }
    
    .photo-thumbnail img:hover {
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
                <!-- Mobile-Optimized Header -->
                <div class="mb-6">
                    <!-- Title and Back Button -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-3">
                        <div class="flex-1">
                            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Incident Report</h2>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                <span class="inline-block">ID: <span class="font-mono">{{ str_pad($report->id, 2, '0', STR_PAD_LEFT) }}</span></span>
                                <span class="hidden sm:inline"> • </span>
                                <span class="block sm:inline mt-1 sm:mt-0">{{ $report->created_at->format('M j, Y') }} at {{ $report->created_at->format('h:i A') }}</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4">
                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'Resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'Invalid Report' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                ];
                                $displayStatus = $report->getDisplayStatusForResident();
                                $statusClass = $statusClasses[$displayStatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <!-- Status Badge -->
                            <span class="px-2 sm:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }} whitespace-nowrap">
                                {{ $displayStatus }}
                            </span>
                            <!-- Back Button -->
                            <a href="{{ $redirectTo ?? route('incident_reports.my_reports') }}" 
                               class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-md shadow-sm text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600 min-h-[44px] sm:min-h-0">
                                <svg class="-ml-1 mr-1 sm:mr-2 h-4 w-4 sm:h-5 sm:w-5 text-gray-500 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="hidden sm:inline">Back to My Reports</span>
                                <span class="sm:hidden">Back</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <!-- Incident Details -->
                    <div>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3">Incident Details</h3>
                        <dl class="space-y-2">
                            <div class="bg-gray-50 px-3 sm:px-4 py-2 sm:py-3 rounded">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm sm:text-base text-gray-900 font-medium">{{ format_label($report->incident_type) }}</dd>
                            </div>
                            <div class="bg-gray-50 px-3 sm:px-4 py-2 sm:py-3 rounded">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500">Date Reported</dt>
                                <dd class="mt-1 text-sm sm:text-base text-gray-900">{{ $report->created_at->format('F j, Y') }}</dd>
                            </div>
                            <div class="bg-gray-50 px-3 sm:px-4 py-2 sm:py-3 rounded">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500">Time Reported</dt>
                                <dd class="mt-1 text-sm sm:text-base text-gray-900">{{ $report->created_at->format('h:i A') }}</dd>
                            </div>
                            @if($report->location)
                                <div class="bg-gray-50 px-3 sm:px-4 py-2 sm:py-3 rounded">
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm sm:text-base text-gray-900">{{ $report->location }}</dd>
                                </div>
                            @endif
                            @if($report->latitude && $report->longitude)
                                <div class="bg-gray-50 px-3 sm:px-4 py-2 sm:py-3 rounded">
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 mb-2">Coordinates</dt>
                                    <dd>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                                                <input type="text" readonly 
                                                       value="{{ $report->latitude }}" 
                                                       class="block w-full rounded-md border-gray-300 shadow-sm text-xs sm:text-sm bg-gray-100 px-2 py-1.5">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                                                <input type="text" readonly 
                                                       value="{{ $report->longitude }}" 
                                                       class="block w-full rounded-md border-gray-300 shadow-sm text-xs sm:text-sm bg-gray-100 px-2 py-1.5">
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" 
                                               target="_blank" 
                                               class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium transition-colors min-h-[44px] sm:min-h-0">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                View on Google Maps
                                            </a>
                                        </div>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if($report->photo_path || $report->photo_paths)
                        <!-- Photo Evidence -->
                        <div class="lg:col-span-1">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3">
                                Photo Evidence 
                                @if($report->photo_paths && count($report->photo_paths) > 1)
                                    <span class="text-xs sm:text-sm text-gray-500">({{ count($report->photo_paths) }} photos)</span>
                                @endif
                            </h3>
                            
                            @php
                                $photos = $report->photo_paths ?? ($report->photo_path ? [$report->photo_path] : []);
                                $imagePhotos = array_values(array_filter($photos, function ($p) {
                                    $ext = strtolower(pathinfo((string) $p, PATHINFO_EXTENSION));
                                    return $ext !== 'pdf';
                                }));
                                $pdfPhotos = array_values(array_filter($photos, function ($p) {
                                    $ext = strtolower(pathinfo((string) $p, PATHINFO_EXTENSION));
                                    return $ext === 'pdf';
                                }));
                            @endphp
                            
                            @if(count($imagePhotos) === 1)
                                <!-- Single Photo -->
                                <div class="border rounded-md overflow-hidden shadow-sm">
                                    <img src="{{ asset('storage/' . $imagePhotos[0]) }}" 
                                         alt="Incident photo" 
                                         class="w-full h-auto cursor-pointer hover:opacity-90 transition"
                                         onclick="openPhotoModal(0)">
                                </div>
                            @elseif(count($imagePhotos) > 1)
                                <!-- Multiple Photos - Carousel -->
                                <div class="relative">
                                    <!-- Photo Carousel -->
                                    <div class="photo-carousel mb-3">
                                        <div class="relative h-48 sm:h-64 lg:h-80 bg-gray-100 rounded-lg overflow-hidden">
                                            @foreach($imagePhotos as $index => $photo)
                                                <div class="photo-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                                    <img src="{{ asset('storage/' . $photo) }}" 
                                                         alt="Incident photo {{ $index + 1 }}" 
                                                         class="w-full h-full object-contain"
                                                         onclick="openPhotoModal({{ $index }})">
                                                </div>
                                            @endforeach
                                            
                                            <!-- Navigation Arrows - Larger touch targets for mobile -->
                                            @if(count($imagePhotos) > 1)
                                                <button onclick="previousPhoto()" class="photo-nav-btn photo-nav-prev" aria-label="Previous photo">
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="nextPhoto()" class="photo-nav-btn photo-nav-next" aria-label="Next photo">
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                            
                                            <!-- Photo Counter -->
                                            <div class="absolute bottom-2 sm:bottom-4 right-2 sm:right-4 bg-black bg-opacity-70 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                                                <span id="current-photo">1</span> / {{ count($imagePhotos) }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Thumbnail Strip - Optimized for mobile scrolling -->
                                    <div class="flex gap-2 overflow-x-auto pb-2 -mx-1 px-1 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                        @foreach($imagePhotos as $index => $photo)
                                            <button onclick="goToPhoto({{ $index }})" 
                                                    class="photo-thumbnail {{ $index === 0 ? 'active' : '' }} flex-shrink-0" 
                                                    data-index="{{ $index }}"
                                                    aria-label="View photo {{ $index + 1 }}">
                                                <img src="{{ asset('storage/' . $photo) }}" 
                                                     alt="Thumbnail {{ $index + 1 }}" 
                                                     class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-md">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-gray-600">No image photos uploaded.</div>
                            @endif

                            @if(count($pdfPhotos) > 0)
                                <div class="mt-3 space-y-2">
                                    @foreach($pdfPhotos as $pdf)
                                        <a href="{{ asset('storage/' . $pdf) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-sm text-blue-600 hover:underline">
                                            View PDF attachment
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-6 sm:mb-8">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3">Description</h3>
                    <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                        <p class="text-sm sm:text-base text-gray-700 whitespace-pre-line leading-relaxed">{{ $report->description }}</p>
                    </div>
                </div>

                @if($report->status === 'rejected' && $report->rejection_reason)
                    <!-- Rejection Reason Notice -->
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-red-800">Rejection Notice</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="font-medium mb-1">Reason for Rejection:</p>
                                    <p class="whitespace-pre-line">{{ $report->rejection_reason }}</p>
                                    @if($report->rejected_at)
                                        <p class="text-xs text-red-600 mt-3 pt-3 border-t border-red-200">
                                            Rejected on {{ $report->rejected_at->format('F j, Y \a\t h:i A') }}
                                            @if($report->rejectedBy)
                                                by {{ $report->rejectedBy->name }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($report->staff_notes)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Staff Notes</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p class="whitespace-pre-line">{{ $report->staff_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Feedback Prompt (shown after report submission) -->
                @if(session('success') && in_array($report->status, ['Pending', 'In Progress']))
                    <div id="feedbackPrompt" class="feedback-prompt">
                        <button type="button" class="feedback-prompt-close" aria-label="Close">&times;</button>
                        <p class="text-gray-800">Thank you for submitting your report! We'll review it shortly.</p>
                        <p class="text-sm text-gray-600 mt-1">You'll be able to provide feedback once it's resolved.</p>
                    </div>
                @endif
                
                <!-- Feedback Form -->
                @if($report->status === 'Resolved' && !$report->feedback_submitted_at)
                    @php
                        // Check if feedback already exists for this incident
                        $hasFeedback = \App\Models\Feedback::where('incident_report_id', $report->id)
                            ->where('user_id', auth()->id())
                            ->exists();
                    @endphp
                    
                    <x-feedback-form 
                        type="incident" 
                        :itemId="$report->id"
                        :hasFeedback="$hasFeedback"
                    />
                @endif

                @if($report->feedback_submitted_at)
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Feedback</h3>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Submitted on {{ $report->feedback_submitted_at->format('F j, Y \a\t h:i A') }}@if($report->is_anonymous) (Submitted Anonymously)@endif</h4>
                                
                                <div class="space-y-4">
                                    @php
                                        $questions = [
                                            'sqd0_rating' => 'I am satisfied with the service that I availed.',
                                            'sqd1_rating' => 'I spent an acceptable amount of time for my transaction.',
                                            'sqd2_rating' => 'The office accurately informed me and followed the transaction\'s requirements and steps.',
                                            'sqd3_rating' => 'My online transaction (including steps and payment) was simple and convenient.',
                                            'sqd4_rating' => 'I easily found information about my transaction from the office or its website.',
                                            'sqd5_rating' => 'I paid an acceptable amount of fees for my transaction.',
                                            'sqd6_rating' => 'I am confident that my online transaction was secure.',
                                            'sqd7_rating' => 'The office\'s online support was available, or (if asked questions) was quick to respond.',
                                            'sqd8_rating' => 'I got what I needed from the government office.',
                                        ];
                                    @endphp
                                    
                                    @foreach($questions as $field => $question)
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-700">{{ $loop->iteration }}. {{ $question }}</p>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <span class="text-xl" title="{{ \App\Models\IncidentReport::getRatingDescription($report->$field) }}">
                                                    {{ \App\Models\IncidentReport::getRatingEmoji($report->$field) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($report->comments)
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Additional Comments:</h4>
                                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $report->comments }}</p>
                                    </div>
                                @endif
                                
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Overall Satisfaction:</span> 
                                        {{ number_format($report->average_rating, 1) }} out of 5
                                        <span class="text-yellow-500 ml-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($report->average_rating))
                                                    ★
                                                @elseif($i - 0.5 <= $report->average_rating)
                                                    ½
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Photo Lightbox Modal -->
<div id="photoLightbox" class="photo-lightbox">
    <span class="lightbox-close" onclick="closePhotoModal()">&times;</span>
    <span class="lightbox-nav lightbox-prev" onclick="changeLightboxPhoto(-1)" id="lightboxPrev">&#10094;</span>
    <span class="lightbox-nav lightbox-next" onclick="changeLightboxPhoto(1)" id="lightboxNext">&#10095;</span>
    <img class="lightbox-content" id="lightboxImage" alt="Full size photo">
    <div class="lightbox-counter">
        <span id="lightboxCounter">1 / 1</span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPhotoIndex = 0;
    const totalPhotos = document.querySelectorAll('.photo-slide').length;
    
    function goToPhoto(index) {
        if (!totalPhotos) return;
        // Hide all slides
        document.querySelectorAll('.photo-slide').forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.photo-thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        
        // Show selected slide
        const selectedSlide = document.querySelector(`.photo-slide[data-index="${index}"]`);
        if (selectedSlide) {
            selectedSlide.classList.add('active');
        }
        
        // Highlight selected thumbnail
        const selectedThumb = document.querySelector(`.photo-thumbnail[data-index="${index}"]`);
        if (selectedThumb) {
            selectedThumb.classList.add('active');
            // Scroll thumbnail into view
            selectedThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
        
        // Update counter
        currentPhotoIndex = index;
        const counterElement = document.getElementById('current-photo');
        if (counterElement) {
            counterElement.textContent = index + 1;
        }
    }
    
    function nextPhoto() {
        if (!totalPhotos) return;
        currentPhotoIndex = (currentPhotoIndex + 1) % totalPhotos;
        goToPhoto(currentPhotoIndex);
    }
    
    function previousPhoto() {
        if (!totalPhotos) return;
        currentPhotoIndex = (currentPhotoIndex - 1 + totalPhotos) % totalPhotos;
        goToPhoto(currentPhotoIndex);
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (totalPhotos > 1) {
            if (e.key === 'ArrowLeft') {
                previousPhoto();
            } else if (e.key === 'ArrowRight') {
                nextPhoto();
            }
        }
    });
    
    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    const carousel = document.querySelector('.photo-carousel');
    if (carousel) {
        carousel.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        carousel.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swiped left - next photo
                nextPhoto();
            } else {
                // Swiped right - previous photo
                previousPhoto();
            }
        }
    }
    
    // Lightbox functionality
    let currentLightboxIndex = 0;
    const photoUrls = @json(isset($imagePhotos) ? array_map(fn($p) => asset('storage/' . $p), $imagePhotos) : []);
    
    function openPhotoModal(index) {
        const lightbox = document.getElementById('photoLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');
        const lightboxPrev = document.getElementById('lightboxPrev');
        const lightboxNext = document.getElementById('lightboxNext');
        
        if (!lightbox || !lightboxImage || photoUrls.length === 0) return;
        
        currentLightboxIndex = index;
        
        // Set image source
        lightboxImage.src = '{{ asset("storage/") }}/' + photoUrls[index];
        
        // Update counter
        lightboxCounter.textContent = `${index + 1} / ${photoUrls.length}`;
        
        // Show/hide navigation arrows
        if (photoUrls.length <= 1) {
            if (lightboxPrev) lightboxPrev.style.display = 'none';
            if (lightboxNext) lightboxNext.style.display = 'none';
        } else {
            if (lightboxPrev) lightboxPrev.style.display = 'flex';
            if (lightboxNext) lightboxNext.style.display = 'flex';
        }
        
        // Show lightbox
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
    
    function closePhotoModal() {
        const lightbox = document.getElementById('photoLightbox');
        if (lightbox) {
            lightbox.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }
    }
    
    function changeLightboxPhoto(direction) {
        if (photoUrls.length === 0) return;
        
        currentLightboxIndex += direction;
        
        // Wrap around
        if (currentLightboxIndex < 0) {
            currentLightboxIndex = photoUrls.length - 1;
        } else if (currentLightboxIndex >= photoUrls.length) {
            currentLightboxIndex = 0;
        }
        
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');
        
        if (lightboxImage && photoUrls[currentLightboxIndex]) {
            lightboxImage.src = '{{ asset("storage/") }}/' + photoUrls[currentLightboxIndex];
        }
        
        if (lightboxCounter) {
            lightboxCounter.textContent = `${currentLightboxIndex + 1} / ${photoUrls.length}`;
        }
    }
    
    // Keyboard controls for lightbox
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('photoLightbox');
        if (lightbox && lightbox.classList.contains('active')) {
            if (e.key === 'Escape') {
                closePhotoModal();
            } else if (e.key === 'ArrowLeft') {
                changeLightboxPhoto(-1);
            } else if (e.key === 'ArrowRight') {
                changeLightboxPhoto(1);
            }
        }
    });
    
    // Close lightbox when clicking outside the image
    document.addEventListener('click', function(e) {
        const lightbox = document.getElementById('photoLightbox');
        if (e.target === lightbox) {
            closePhotoModal();
        }
    });
    
    // Touch/swipe support for lightbox
    let lightboxTouchStartX = 0;
    let lightboxTouchEndX = 0;
    
    const photoLightbox = document.getElementById('photoLightbox');
    if (photoLightbox) {
        photoLightbox.addEventListener('touchstart', function(e) {
            lightboxTouchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        photoLightbox.addEventListener('touchend', function(e) {
            lightboxTouchEndX = e.changedTouches[0].screenX;
            handleLightboxSwipe();
        }, { passive: true });
    }
    
    function handleLightboxSwipe() {
        const swipeThreshold = 50;
        const diff = lightboxTouchStartX - lightboxTouchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swiped left - next photo
                changeLightboxPhoto(1);
            } else {
                // Swiped right - previous photo
                changeLightboxPhoto(-1);
            }
        }
    }
</script>
@endpush
