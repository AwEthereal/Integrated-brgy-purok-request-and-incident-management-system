<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Report</title>
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
    </style>
</head>

<body class="bg-[#bbf7d0] min-h-screen flex items-center justify-center py-6 px-4">

    <form action="{{ route('incident_reports.store') }}" method="POST" enctype="multipart/form-data"
        class="w-full max-w-xl bg-white p-6 md:p-8 rounded-2xl shadow-lg space-y-6">
        @csrf

        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 border-b pb-2">Submit Incident Report</h2>

        <div>
            <label for="incident_type" class="block text-sm font-medium text-gray-700 mb-1">Incident Type *</label>
            <select name="incident_type" id="incident_type" required
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Select Incident Type --</option>
                @foreach(\App\Models\IncidentReport::TYPES as $key => $label)
                    <option value="{{ $key }}">{{ format_label($label) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea name="description" id="description" rows="4" required
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
        </div>

        <input type="hidden" name="photo_data" id="photo_data">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <button type="button" id="use-current-location" data-action="get-location"
                    class="text-xs text-blue-600 hover:text-blue-800 flex items-center space-x-1"
                    title="Use current location">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Use Current Location</span>
                </button>
            </div>

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
                <div id="map-container" class="mt-3 rounded-md border border-gray-200 overflow-hidden" style="height: 200px; display: none;">
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
            </div>


        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Take a Photo</label>

            <div class="relative w-full aspect-video rounded-md border border-gray-300 overflow-hidden">
                <video id="camera-feed" autoplay playsinline class="w-full h-full object-cover"></video>
            </div>

            <canvas id="snapshot" class="hidden"></canvas>

            <img id="photo-preview" class="hidden w-full mt-2 rounded-md border border-gray-300" alt="Photo preview" />

            <div class="flex gap-2 mt-2">
                <button type="button" id="flipButton" data-action="flip-camera"
                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">Flip Camera</button>
                <button type="button" id="photoButton" data-action="take-photo"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Take Photo</button>
            </div>

            <p id="photo-status" class="text-sm text-green-600 mt-2 hidden">Photo captured!</p>
        </div>

        <div>
            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Or Upload a Photo</label>
            <input type="file" name="photo" id="photo" accept="image/*"
                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200"
                data-action="disable-camera" />

            <button type="button" id="reEnableBtn" data-action="enable-camera"
                class="hidden mt-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Use Camera
                Again</button>
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

<div class="flex flex-col sm:flex-row gap-4 mt-6">
    <a href="{{ route('dashboard') }}"
        class="w-full sm:w-1/2 text-center bg-gray-300 text-gray-800 py-3 px-6 rounded-lg font-semibold hover:bg-gray-400 transition">
        ← Back
    </a>
    <button type="submit" id="submit-button"
        class="w-full sm:w-1/2 bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition">
        Submit Report
    </button>
</div>

    </form>

    <!-- Include our JavaScript file -->
    @vite(['resources/js/incident-report.js'])
</body>

</html>