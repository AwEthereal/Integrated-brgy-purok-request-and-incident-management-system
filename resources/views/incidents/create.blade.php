<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Incident</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Set ipinfo.io token -->
    <script>
        window.ipinfoToken = '{{ config('services.ipinfo.token') }}';
    </script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin="anonymous" />
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="anonymous"></script>
    <style>
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .border-error {
            border-color: #ef4444;
        }
        
        /* Fix Leaflet map display on mobile */
        #map-container {
            position: relative;
            z-index: 1;
        }
        
        #map {
            position: relative;
            z-index: 1;
            background: #e5e7eb;
        }
        
        /* Ensure Leaflet tiles load properly */
        .leaflet-container {
            font-family: inherit;
            background: #e5e7eb;
        }
        
        .leaflet-tile-container {
            z-index: 1;
        }
        
        .leaflet-pane {
            z-index: auto;
        }
        
        /* Enhanced Styles */
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        @media (min-width: 768px) {
            .section-card {
                padding: 24px;
                margin-bottom: 20px;
            }
        }
        
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        @media (min-width: 768px) {
            .section-title {
                font-size: 1.125rem;
                margin-bottom: 16px;
                gap: 8px;
            }
        }
        
        .section-title svg {
            width: 20px;
            height: 20px;
            color: #10b981;
            flex-shrink: 0;
        }
        
        @media (min-width: 768px) {
            .section-title svg {
                width: 24px;
                height: 24px;
            }
        }
        
        .photo-limit-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 500;
            margin-left: auto;
        }
        
        @media (min-width: 768px) {
            .photo-limit-badge {
                padding: 4px 12px;
                font-size: 0.75rem;
            }
        }
        
        /* Mobile optimizations */
        @media (max-width: 767px) {
            body {
                padding: 12px 8px;
            }
        }
        
        /* Lightbox Styles */
        .lightbox {
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
        
        .lightbox.active {
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
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
        
        /* Make gallery thumbnails clickable */
        #photos-gallery img,
        #camera-thumbnails img {
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        #photos-gallery img:hover,
        #camera-thumbnails img:hover {
            transform: scale(1.05);
        }
        
        /* Camera zoom isolation - prevent zoom from affecting UI elements */
        #camera-feed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform-origin: center center;
            /* Isolate zoom transformations */
            will-change: transform;
            backface-visibility: hidden;
        }
        
        /* Keep UI elements fixed in position with higher z-index */
        .camera-ui-layer {
            position: absolute;
            z-index: 100;
            pointer-events: none;
            /* Prevent UI from being affected by video transformations */
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        
        .camera-ui-layer * {
            pointer-events: auto;
        }
        
        /* Fix camera controls layout - always center capture button using flexbox */
        .camera-controls-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            position: relative;
            min-height: 100px;
            padding: 0 1rem;
        }
        
        /* Position side buttons absolutely to not affect center alignment */
        .camera-control-left {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .camera-control-center {
            /* Center is default with flexbox justify-content: center */
            z-index: 10;
        }
        
        .camera-control-right {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }
        
        /* Ensure buttons are always visible and properly sized */
        .camera-control-btn {
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .camera-control-btn:active {
            transform: scale(0.95);
        }
        
        /* Mobile optimizations for camera controls */
        @media (max-width: 640px) {
            .camera-control-left {
                left: 1rem;
            }
            
            .camera-control-right {
                right: 1rem;
            }
            
            .camera-controls-wrapper {
                padding: 0 0.5rem;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen py-8 px-4">

    <div class="w-full max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-6 md:mb-8">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 mb-1 md:mb-2">Report an Incident</h1>
            <p class="text-sm md:text-base text-gray-600">Help us keep our community safe by reporting incidents</p>
        </div>

        <!-- Rate Limit Information -->
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold">Report Limits</p>
                    <p class="text-sm mt-1">You can submit up to <strong>10 incident reports per hour</strong> and have a maximum of <strong>10 pending reports</strong> at any time.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                <p class="font-bold">Please fix the following errors:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($publicMode) && $publicMode ? route('public.incident.store') : route('incident_reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($publicMode) && $publicMode)
            <div class="section-card">
                <div class="space-y-4">
                    <div>
                        <label for="reporter_name" class="block text-sm font-medium text-gray-700 mb-2">Your Name *</label>
                        <input type="text" id="reporter_name" name="reporter_name" required value="{{ old('reporter_name') }}"
                               class="w-full rounded-lg border-gray-300 px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" autocomplete="name">
                        @error('reporter_name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">Contact Number *</label>
                        <input type="tel" id="contact_number" name="contact_number" required inputmode="numeric" maxlength="20" value="{{ old('contact_number') }}"
                               class="w-full rounded-lg border-gray-300 px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" autocomplete="tel">
                        @error('contact_number')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <!-- Incident Details Section -->
            <div class="section-card">
                <h3 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Incident Details
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="incident_type" class="block text-sm font-medium text-gray-700 mb-2">Incident Type *</label>
                        <select name="incident_type" id="incident_type" required
                            class="w-full rounded-lg border-gray-300 px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="">-- Select Incident Type --</option>
                            @foreach(\App\Models\IncidentReport::TYPES as $key => $label)
                                <option value="{{ $key }}">{{ format_label($label) }}</option>
                            @endforeach
                        </select>
                        <div id="incident_type_other_wrap" class="mt-3 hidden">
                            <label for="incident_type_other" class="block text-sm font-medium text-gray-700 mb-2">Please specify</label>
                            <input type="text" name="incident_type_other" id="incident_type_other" maxlength="100"
                                   value="{{ old('incident_type_other') }}"
                                   class="w-full rounded-lg border-gray-300 px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="Describe the incident type">
                            @error('incident_type_other')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const sel = document.getElementById('incident_type');
                            const wrap = document.getElementById('incident_type_other_wrap');
                            const otherInput = document.getElementById('incident_type_other');
                            if (!sel || !wrap) return;
                            function syncOther() {
                                if (sel.value === 'other') {
                                    wrap.classList.remove('hidden');
                                } else {
                                    wrap.classList.add('hidden');
                                    if (otherInput) otherInput.value = '';
                                }
                            }
                            const oldVal = "{{ old('incident_type') }}";
                            if (oldVal) { sel.value = oldVal; }
                            sel.addEventListener('change', syncOther);
                            syncOther();
                        });
                    </script>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" id="description" rows="4" required
                            class="w-full rounded-lg border-gray-300 px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none transition"
                            placeholder="Please provide detailed information about the incident..."></textarea>
                    </div>
                </div>
            </div>

            <input type="hidden" name="photo_data" id="photo_data">
            <input type="hidden" name="photos_data" id="photos_data">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <!-- Location Section -->
            <div class="section-card">
                <h3 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Location
                    <button type="button" id="use-current-location" data-action="get-location"
                        class="ml-auto text-xs md:text-sm text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 md:px-4 md:py-2 rounded-lg flex items-center gap-1 md:gap-2 transition"
                        title="Use current location">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline">Use Current Location</span>
                        <span class="sm:hidden">Get Location</span>
                    </button>
                </h3>

            <!-- Location Input -->
            <div class="relative">
                <div class="relative">
                    <input type="text" id="location" name="location"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Enter location or click 'Use Current'" autocomplete="off" 
                        data-coordinates="" readonly
                        title="Location will be detected automatically">
                    <div id="location-error" class="error-message"></div>

                    <!-- Coordinates Tooltip -->
                    <div id="coordinates-tooltip"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-400 hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="location-loading" class="absolute inset-y-0 right-8 flex items-center pr-3 hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Location Status -->
                <div id="location-status" class="mt-1 text-xs text-gray-500 hidden"></div>

                <!-- Fallback Message -->
                <div id="location-fallback" class="mt-2 p-2 text-xs bg-yellow-50 text-yellow-700 rounded-md hidden">
                    <p>Couldn't find an exact address. Showing coordinates instead.</p>
                    <button type="button" id="retry-location" data-action="retry-location" class="mt-1 text-yellow-700 hover:underline">
                        Try again
                    </button>
                </div>
            </div>

            <!-- Location Preview -->
            <div id="location-preview" class="mt-2 p-3 bg-gray-50 rounded-md border border-gray-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="location-address" class="text-sm text-gray-700"></span>
                </div>
                <div class="mt-2 text-xs text-gray-500" id="location-coordinates"></div>
                
                <!-- Map Preview -->
                <div id="map-container" class="mt-3 rounded-md border border-gray-200 overflow-hidden" style="height: 300px; min-height: 300px; display: none;">
                    <div id="map" style="height: 100%; width: 100%; min-height: 300px;"></div>
                </div>
                
                <!-- Map Instructions -->
                <div class="mt-2 p-2 bg-blue-50 rounded-md border border-blue-200">
                    <p class="text-xs text-blue-700 flex items-start">
                        <svg class="w-4 h-4 mr-1 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><strong>Tip:</strong> Click on the map or drag the marker to adjust your exact location</span>
                    </p>
                </div>
            </div>


        </div>

            <!-- Photo Evidence Section -->
            <div class="section-card">
                <h3 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Photo Evidence
                    <span class="photo-limit-badge">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Max 6 photos
                    </span>
                </h3>

                <div class="space-y-4">
                    <!-- Camera Activation Button (shown when camera is off) -->
                    <div id="camera-activation" class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Take Photos with Camera</label>
                        <button type="button" id="activateCameraBtn" data-action="activate-camera"
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-6 px-6 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg flex items-center justify-center gap-3 text-lg font-semibold">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Open Camera</span>
                        </button>
                        <p class="text-xs text-gray-500 mt-2">Click to activate your device camera</p>
                    </div>

                    <!-- Camera Section (hidden by default) -->
                    <div id="camera-section" class="hidden">
                        <!-- Fullscreen Camera Container -->
                        <div class="fixed inset-0 z-50 bg-black overflow-hidden">
                            <!-- Camera Feed Container (isolated for zoom) -->
                            <div class="absolute inset-0" style="z-index: 1;">
                                <video id="camera-feed" autoplay playsinline class="w-full h-full object-cover"></video>
                            </div>
                            
                            <!-- Camera Overlay (separate layer) -->
                            <div class="camera-ui-layer" style="inset: 0; pointer-events: none;">
                                <!-- Grid Lines for Composition -->
                                <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 opacity-20" style="z-index: 50;">
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                    <div class="border border-white/30"></div>
                                </div>
                            </div>
                            
                            <!-- Flash Effect (separate layer) -->
                            <div id="camera-flash" class="camera-ui-layer bg-white opacity-0 pointer-events-none transition-opacity duration-150" style="inset: 0; z-index: 200;"></div>
                            
                            <!-- Top Controls Bar (fixed UI layer) -->
                            <div class="camera-ui-layer" style="top: 0; left: 0; right: 0; z-index: 150;">
                                <div class="bg-gradient-to-b from-black/80 via-black/40 to-transparent p-4 sm:p-5 flex items-center justify-between">
                                    <!-- Close Button -->
                                    <button type="button" id="closeCameraBtn" data-action="close-camera"
                                        class="camera-control-btn bg-white/20 hover:bg-red-500/80 text-white p-3 sm:p-3.5 rounded-full transition-all backdrop-blur-md shadow-lg">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Photo Counter -->
                                    <div class="bg-black/70 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-sm sm:text-base font-bold flex items-center gap-2 backdrop-blur-md shadow-lg border border-white/20">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span id="photo-counter">0/6</span>
                                    </div>
                                    
                                    <!-- Torch Button -->
                                    <button type="button" id="torchButton" data-action="toggle-torch"
                                        class="camera-control-btn bg-white/20 hover:bg-yellow-500/80 text-white p-3 sm:p-3.5 rounded-full transition-all backdrop-blur-md shadow-lg hidden">
                                        <svg id="torchIcon" class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Bottom Controls Bar (fixed UI layer) -->
                            <div class="camera-ui-layer" style="bottom: 0; left: 0; right: 0; z-index: 150;">
                                <div class="bg-gradient-to-t from-black/90 via-black/50 to-transparent p-4 sm:p-6">
                                    <!-- Thumbnails Preview -->
                                    <div id="camera-thumbnails" class="flex gap-2 sm:gap-3 overflow-x-auto pb-4 sm:pb-5 px-1 justify-center scrollbar-hide">
                                        <!-- Thumbnails will appear here -->
                                    </div>
                                    
                                    <!-- Camera Controls with Flexbox Layout (Always Centers Capture Button) -->
                                    <div class="camera-controls-wrapper">
                                        <!-- Left: Flip Camera Button (Absolutely Positioned) -->
                                        <div class="camera-control-left">
                                            <button type="button" id="flipButton" data-action="flip-camera"
                                                class="camera-control-btn bg-white/20 hover:bg-blue-500/80 text-white p-3.5 sm:p-4 rounded-full transition-all backdrop-blur-md shadow-lg">
                                                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Center: Capture Button (Always Centered via Flexbox) -->
                                        <div class="camera-control-center">
                                            <button type="button" id="photoButton" data-action="take-photo"
                                                class="camera-control-btn w-20 h-20 sm:w-24 sm:h-24 bg-white rounded-full hover:bg-gray-100 transition-all shadow-2xl border-4 sm:border-[5px] border-gray-300 flex items-center justify-center">
                                                <div class="w-16 h-16 sm:w-[76px] sm:h-[76px] bg-gradient-to-br from-red-500 to-red-600 rounded-full pointer-events-none"></div>
                                            </button>
                                        </div>
                                        
                                        <!-- Right: Done Button (Absolutely Positioned, hidden when no photos) -->
                                        <div class="camera-control-right">
                                            <button type="button" id="doneButton" data-action="done-photos"
                                                class="camera-control-btn bg-green-500 hover:bg-green-600 text-white p-3.5 sm:p-4 rounded-full transition-all backdrop-blur-md shadow-lg hidden">
                                                <svg class="w-7 h-7 sm:w-8 sm:h-8 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <canvas id="snapshot" class="hidden"></canvas>
                    </div>

                    <!-- Photos Gallery (Main) -->
                    <div id="photos-gallery" class="grid grid-cols-3 gap-3 hidden">
                        <!-- Photos will be added here -->
                    </div>

                    <p id="photo-status" class="text-sm text-green-600 font-medium hidden flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span id="photo-count">0</span> photo(s) captured
                    </p>

                    <!-- Upload Section -->
                    <div class="border-t pt-4">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-3">Or Upload Photos from Device</label>
                        <input type="file" name="photos[]" id="photo" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" multiple
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:font-medium hover:file:bg-green-100 transition cursor-pointer"
                            data-action="upload-photos" />
                    </div>
                </div>
            </div>

        @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            </div>
        </div>
    </div>
@endif

            <!-- Submit Section -->
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4 pt-4 md:pt-6">
                <a href="{{ (isset($publicMode) && $publicMode) ? route('public.landing', [], false) : route('dashboard') }}"
                    class="w-full sm:w-1/2 text-center bg-gray-200 text-gray-700 py-3 md:py-4 px-4 md:px-6 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center justify-center gap-2 text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
                <button type="submit" id="submit-button"
                    class="w-full sm:w-1/2 bg-green-600 text-white py-3 md:py-4 px-4 md:px-6 rounded-lg font-semibold hover:bg-green-700 active:bg-green-800 transition flex items-center justify-center gap-2 shadow-lg text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Submit Report
                </button>
            </div>
        </form>
    </div>

    <!-- Lightbox Modal -->
    <div id="photoLightbox" class="lightbox">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <span class="lightbox-prev" onclick="changeLightboxPhoto(-1)">&#10094;</span>
        <span class="lightbox-next" onclick="changeLightboxPhoto(1)">&#10095;</span>
        <img class="lightbox-content" id="lightboxImage" alt="Full size photo">
        <div class="lightbox-counter">
            <span id="lightboxCounter">1 / 1</span>
        </div>
    </div>

    <!-- Include our JavaScript file -->
    @vite(['resources/js/incident-report.js'])
</body>

</html>