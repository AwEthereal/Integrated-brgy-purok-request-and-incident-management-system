@extends('layouts.app')

@php
    $user = auth()->user();
    $isAuthorized = in_array($user->role, ['barangay_kagawad', 'barangay_captain', 'admin']);
@endphp

@push('styles')
<style>
    .info-card {
        @apply bg-white rounded-lg shadow-sm p-6 mb-6;
    }
    .info-label {
        @apply text-sm font-medium text-gray-500;
    }
    .info-value {
        @apply mt-1 text-sm text-gray-900;
    }
    
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
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }
    
    .photo-nav-btn:hover {
        background: rgba(0, 0, 0, 0.7);
        transform: translateY(-50%) scale(1.1);
    }
    
    .photo-nav-prev {
        left: 16px;
    }
    
    .photo-nav-next {
        right: 16px;
    }
    
    .photo-thumbnail {
        flex-shrink: 0;
        border: 3px solid transparent;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .photo-thumbnail:hover {
        border-color: #3b82f6;
        transform: scale(1.05);
    }
    
    .photo-thumbnail.active {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }
    
    .photo-thumbnail img {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-lg leading-6 font-medium text-gray-900">Incident Report</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        ID: <span class="font-mono">{{ str_pad($report->id, 2, '0', STR_PAD_LEFT) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Request Information -->
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <!-- Incident Details -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Incident Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ format_label($report->incident_type) }}
                    </dd>
                </div>

                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Reported On</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $report->created_at->format('F j, Y \a\t g:i A') }}
                    </dd>
                </div>

                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Location Details</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 space-y-3">
                        <!-- Address -->
                        <div class="bg-gray-50 p-3 rounded-md">
                            <div class="flex items-center text-gray-600 mb-1">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="font-medium">Reported Address</span>
                            </div>
                            <div class="ml-6">
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($report->location) }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="text-blue-600 hover:text-blue-800 hover:underline flex items-center">
                                    {{ $report->location }}
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Coordinates -->
                        @if($report->latitude && $report->longitude)
                        <div class="bg-gray-50 p-3 rounded-md">
                            <div class="flex items-center text-gray-600 mb-1">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">GPS Coordinates</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-6">
                                <div>
                                    <div class="text-xs text-gray-500">Latitude</div>
                                    <div class="font-mono">{{ number_format($report->latitude, 6) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">Longitude</div>
                                    <div class="font-mono">{{ number_format($report->longitude, 6) }}</div>
                                </div>
                            </div>
                            <div class="mt-2 ml-6">
                                <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 hover:underline"
                                   title="View on Google Maps">
                                    <span>View on Map</span>
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif
                    </dd>
                </div>

                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                        {{ $report->description }}
                    </dd>
                </div>

                @if($report->staff_notes)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Staff Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                        {{ $report->staff_notes }}
                    </dd>
                </div>
                @endif

                <!-- Reporter Information -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500">Reported By</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $report->user->name }}
                    </dd>
                </div>

                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Purok</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $report->purok->name ?? 'N/A' }}
                    </dd>
                </div>

                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $report->user->contact_number ?? 'N/A' }}
                    </dd>
                </div>

                @if($report->photo_path || $report->photo_paths)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                    <dt class="text-sm font-medium text-gray-500">
                        Photo Evidence
                        @php
                            $photos = $report->photo_paths ?? ($report->photo_path ? [$report->photo_path] : []);
                        @endphp
                        @if(count($photos) > 1)
                            <span class="text-xs text-gray-400">({{ count($photos) }} photos)</span>
                        @endif
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if(count($photos) === 1)
                            <!-- Single Photo -->
                            <img src="{{ asset('storage/' . $photos[0]) }}" 
                                 alt="Incident photo" 
                                 class="mt-2 rounded-lg shadow-sm max-h-64 object-cover">
                        @else
                            <!-- Multiple Photos - Carousel -->
                            <div class="relative mt-2">
                                <div class="photo-carousel mb-4">
                                    <div class="relative h-80 bg-gray-100 rounded-lg overflow-hidden">
                                        @foreach($photos as $index => $photo)
                                            <div class="photo-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                                <img src="{{ asset('storage/' . $photo) }}" 
                                                     alt="Incident photo {{ $index + 1 }}" 
                                                     class="w-full h-full object-contain">
                                            </div>
                                        @endforeach
                                        
                                        <!-- Navigation Arrows -->
                                        @if(count($photos) > 1)
                                            <button onclick="previousPhoto()" class="photo-nav-btn photo-nav-prev">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                            </button>
                                            <button onclick="nextPhoto()" class="photo-nav-btn photo-nav-next">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        
                                        <!-- Photo Counter -->
                                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                                            <span id="current-photo">1</span> / {{ count($photos) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Thumbnail Strip -->
                                <div class="flex gap-2 overflow-x-auto pb-2">
                                    @foreach($photos as $index => $photo)
                                        <button onclick="goToPhoto({{ $index }})" 
                                                class="photo-thumbnail {{ $index === 0 ? 'active' : '' }}" 
                                                data-index="{{ $index }}">
                                            <img src="{{ asset('storage/' . $photo) }}" 
                                                 alt="Thumbnail {{ $index + 1 }}" 
                                                 class="w-20 h-20 object-cover rounded-md">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </dd>
                </div>
                @endif

                <!-- Timeline -->
                
            </dl>
        </div>

        <!-- Status and Actions -->
        <div class="px-4 py-4 bg-gray-50 sm:px-6 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <!-- Status Badge -->
                <div class="mb-4 sm:mb-0">
                    @php
                        $statusClasses = [
                            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'Resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'Invalid Report' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                        ];
                        $displayStatus = $report->getDisplayStatusForResident();
                        $statusClass = $statusClasses[$displayStatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                    @endphp
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                        <span>Status: {{ $displayStatus }}</span>
                    </div>
                    @if($report->viewed_at)
                        <p class="mt-1 text-xs text-gray-500">First viewed: {{ $report->viewed_at->diffForHumans() }}</p>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                @if(in_array(auth()->user()->role, ['barangay_kagawad', 'barangay_captain', 'admin']))
                    @if(strtolower($report->status) === 'pending')
                    <div class="flex flex-wrap gap-2 sm:space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Mark as Invalid
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('inProgressModal').classList.remove('hidden')" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            Mark as In Progress
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('approveModal').classList.remove('hidden')" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Mark as Resolved
                        </button>
                    </div>
                    @elseif(strtolower($report->status) === 'in_progress')
                    <div class="flex flex-wrap gap-2 sm:space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Mark as Invalid
                        </button>
                        <form action="{{ route('barangay.incident_reports.resolve', $report) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Mark as Resolved
                            </button>
                        </form>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('approveModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Resolve Incident Report</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to mark this incident report as resolved? This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form action="{{ route('barangay.incident_reports.approve', $report) }}" method="POST" class="inline-flex">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Resolve
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- In Progress Modal -->
<div id="inProgressModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('inProgressModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Mark as In Progress</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to mark this incident report as In Progress? This will notify the resident that their report is being handled.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form action="{{ route('barangay.incident_reports.in_progress', $report) }}" method="POST" class="inline-flex">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Mark In Progress
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('inProgressModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Reject Incident Report</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Please provide a reason for rejecting this incident report.
                        </p>
                        <form action="{{ route('barangay.incident_reports.reject', $report) }}" method="POST" class="mt-4">
                            @csrf
                            <div>
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for rejection</label>
                                <div class="mt-1">
                                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></textarea>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Reject Report
                                </button>
                                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPhotoIndex = 0;
    const totalPhotos = document.querySelectorAll('.photo-slide').length;
    
    function goToPhoto(index) {
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
        const nextIndex = (currentPhotoIndex + 1) % totalPhotos;
        goToPhoto(nextIndex);
    }
    
    function previousPhoto() {
        const prevIndex = (currentPhotoIndex - 1 + totalPhotos) % totalPhotos;
        goToPhoto(prevIndex);
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
</script>
@endpush
