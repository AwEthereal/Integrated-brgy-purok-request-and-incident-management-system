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
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .border-error {
            border-color: #ef4444;
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

        <form action="{{ route('incident_reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

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
                    </div>

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
                <div id="map-container" class="mt-3 rounded-md border border-gray-200 overflow-hidden" style="height: 250px; display: none;">
                    <div id="map" style="height: 100%; width: 100%;"></div>
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
                    <!-- Camera Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Take Photos with Camera</label>
                        <div class="relative w-full aspect-video rounded-lg border-2 border-gray-300 overflow-hidden bg-gray-100">
                            <video id="camera-feed" autoplay playsinline class="w-full h-full object-cover"></video>
                        </div>

                        <canvas id="snapshot" class="hidden"></canvas>

                        <div class="flex gap-2 md:gap-3 mt-3">
                            <button type="button" id="flipButton" data-action="flip-camera"
                                class="flex-1 bg-yellow-500 text-white px-3 py-2.5 md:px-4 md:py-3 rounded-lg hover:bg-yellow-600 transition font-medium flex items-center justify-center gap-1.5 md:gap-2 text-sm md:text-base">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="hidden sm:inline">Flip Camera</span>
                                <span class="sm:hidden">Flip</span>
                            </button>
                            <button type="button" id="photoButton" data-action="take-photo"
                                class="flex-1 bg-blue-600 text-white px-3 py-2.5 md:px-4 md:py-3 rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-1.5 md:gap-2 text-sm md:text-base">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="hidden sm:inline">Take Photo</span>
                                <span class="sm:hidden">Capture</span>
                            </button>
                        </div>
                    </div>

                    <!-- Photos Gallery -->
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
                        <input type="file" name="photos[]" id="photo" accept="image/*" multiple
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:font-medium hover:file:bg-green-100 transition cursor-pointer"
                            data-action="upload-photos" />

                        <button type="button" id="reEnableBtn" data-action="enable-camera"
                            class="hidden mt-3 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                            Use Camera Again
                        </button>
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
                <a href="{{ route('dashboard') }}"
                    class="w-full sm:w-1/2 text-center bg-gray-200 text-gray-700 py-3 md:py-4 px-4 md:px-6 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center justify-center gap-2 text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="hidden sm:inline">Back to Dashboard</span>
                    <span class="sm:hidden">Back</span>
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

    <!-- Include our JavaScript file -->
    @vite(['resources/js/incident-report.js'])
</body>

</html>