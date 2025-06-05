// Global state variables
let watchId = null;
let locationCheckInProgress = false;
let locationTimeout = null;
let currentAttempt = 1;
const maxAttempts = 3;

// Clean up location resources
function cleanupLocation() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }

    if (locationTimeout) {
        clearTimeout(locationTimeout);
        locationTimeout = null;
    }

    const loadingIndicator = document.getElementById('location-loading');
    if (loadingIndicator) {
        loadingIndicator.classList.add('hidden');
    }
}

// Update location status message
function updateLocationStatus(message, isError = false) {
    const locationStatus = document.getElementById('location-status');
    if (locationStatus) {
        locationStatus.textContent = message;
        locationStatus.classList.toggle('text-red-500', isError);
        locationStatus.classList.toggle('text-gray-500', !isError);
        locationStatus.classList.remove('hidden');
    }
}

// Get location from IP as fallback
function getLocationFromIP() {
    const loadingIndicator = document.getElementById('location-loading');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
    }
    updateLocationStatus('Getting approximate location from IP...');

    fetch('/api/ip-location', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.json();
    })
    .then(data => {
        if (data.success && data.latitude && data.longitude) {
            updateLocationUI({
                latitude: parseFloat(data.latitude),
                longitude: parseFloat(data.longitude),
                accuracy: 5000 // Approximate accuracy for IP-based location
            }, data.city || 'Approximate location');
        } else {
            throw new Error('No location data from IP');
        }
    })
    .catch(error => {
        console.error('IP geolocation error:', error);
        updateLocationStatus('Could not determine your location. Please enter it manually.', true);
    });
}

// Update UI with location data
function updateLocationUI(coords, address) {
    const locationInput = document.getElementById('location');
    const locationStatus = document.getElementById('location-status');
    const loadingIndicator = document.getElementById('location-loading');

    if (locationInput) {
        locationInput.value = address || `${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)}`;
    }

    if (locationStatus) {
        const accuracy = coords.accuracy ? ` (Accuracy: ~${Math.round(coords.accuracy)}m)` : '';
        locationStatus.textContent = address ? `Location found${accuracy}` : 'Location found (no address available)';
        locationStatus.classList.remove('text-red-500');
        locationStatus.classList.add('text-green-600');
        locationStatus.classList.remove('hidden');
    }

    if (loadingIndicator) {
        loadingIndicator.classList.add('hidden');
    }

    // Update map if available
    updateMap(coords.latitude, coords.longitude);
}

// Main function to get current location
function getCurrentLocation(attempt = 1) {
    if (locationCheckInProgress) return;
    locationCheckInProgress = true;
    currentAttempt = attempt;

    const loadingIndicator = document.getElementById('location-loading');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
    }

    // Show appropriate message based on attempt
    const messages = [
        'Requesting precise location... (Please allow location access)',
        'Trying standard location service...',
        'Getting approximate location...'
    ];
    updateLocationStatus(messages[Math.min(attempt - 1, messages.length - 1)]);

    // Clean up any existing location attempts
    cleanupLocation();

    // Check if geolocation is available
    if (!navigator.geolocation) {
        locationCheckInProgress = false;
        updateLocationStatus('Geolocation not supported. Using approximate location...');
        getLocationFromIP();
        return;
    }

    // Set a timeout to fall back to IP if geolocation takes too long
    locationTimeout = setTimeout(() => {
        if (locationCheckInProgress) {
            cleanupLocation();
            locationCheckInProgress = false;

            if (attempt < maxAttempts) {
                // Try again with different settings
                setTimeout(() => getCurrentLocation(attempt + 1), 500);
            } else {
                updateLocationStatus('Location request timed out. Using approximate location...');
                getLocationFromIP();
            }
        }
    }, attempt === 1 ? 10000 : 15000);

    // Configure geolocation options
    const options = {
        enableHighAccuracy: attempt === 1, // Only high accuracy on first attempt
        timeout: 10000, // 10 seconds
        maximumAge: 0 // Force fresh location
    };

    // Try to get current position
    watchId = navigator.geolocation.watchPosition(
        // Success callback
        (position) => {
            cleanupLocation();
            locationCheckInProgress = false;
            const coords = position.coords;

            // Only accept positions with reasonable accuracy
            if (coords.accuracy > 500 && attempt < maxAttempts) {
                getCurrentLocation(attempt + 1);
                return;
            }

            if (coords.accuracy > 1000) { // If accuracy is very poor, fall back to IP
                updateLocationStatus('Location accuracy is too low. Using approximate location...');
                getLocationFromIP();
                return;
            }

            // Get address from coordinates
            fetch(`/api/reverse-geocode?lat=${coords.latitude}&lon=${coords.longitude}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.message || 'Failed to get address');
                updateLocationUI(coords, data.display_name || null);
            })
            .catch(error => {
                console.error('Reverse geocoding error:', error);
                updateLocationUI(coords, null);
            });
        },
        // Error callback
        (error) => {
            console.warn('Geolocation error:', error);
            cleanupLocation();
            locationCheckInProgress = false;

            if (attempt < maxAttempts) {
                // Try again with different settings
                setTimeout(() => getCurrentLocation(attempt + 1), 500);
            } else {
                updateLocationStatus('Could not get precise location. Using approximate location...');
                getLocationFromIP();
            }
        },
        options
    );
}

// Initialize the application
document.addEventListener('DOMContentLoaded', function () {
    // Initialize camera if function exists
    if (typeof startCamera === 'function') {
        startCamera();
    }

    // Set up event listener for location button
    const useCurrentLocationBtn = document.getElementById('use-current-location');
    if (useCurrentLocationBtn) {
        useCurrentLocationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            getCurrentLocation();
        });
    }

    // Try to get location automatically
    getCurrentLocation();

    // Add event listener for retry button
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'retry-location') {
            e.preventDefault();
            const locationFallback = document.getElementById('location-fallback');
            if (locationFallback) {
                locationFallback.classList.add('hidden');
            }
            getCurrentLocation();
        }
    });
});
