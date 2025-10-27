// Incident Report Form Handling
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const video = document.getElementById('camera-feed');
    const canvas = document.getElementById('snapshot');
    const photoInput = document.getElementById('photo_data');
    const photosInput = document.getElementById('photos_data');
    const photosGallery = document.getElementById('photos-gallery');
    const photoCount = document.getElementById('photo-count');
    const photoStatus = document.getElementById('photo-status');
    const takePhotoBtn = document.getElementById('photoButton');
    const flipCameraBtn = document.getElementById('flipButton');
    const fileInput = document.getElementById('photo');
    const reEnableBtn = document.getElementById('reEnableBtn');
    
    // New camera interface elements
    const cameraActivation = document.getElementById('camera-activation');
    const cameraSection = document.getElementById('camera-section');
    const photoCounter = document.getElementById('photo-counter');
    const cameraThumbnails = document.getElementById('camera-thumbnails');
    const cameraFlash = document.getElementById('camera-flash');
    const torchButton = document.getElementById('torchButton');
    const torchIcon = document.getElementById('torchIcon');
    const doneButton = document.getElementById('doneButton');
    const locationPreview = document.getElementById('location-preview');
    const locationAddress = document.getElementById('location-address');
    const locationCoordinates = document.getElementById('location-coordinates');
    const locationLoading = document.getElementById('location-loading');
    const mapContainer = document.getElementById('map-container');
    const mapElement = document.getElementById('map');
    const retryLocationBtn = document.getElementById('retry-location');

    // Map state
    let map = null;
    let marker = null;

    // Utility: Always show the map container and fix its size
    function forceShowMapContainer() {
        if (mapContainer) {
            mapContainer.style.display = 'block';
            mapContainer.style.height = '200px';
            mapContainer.classList.remove('hidden');
        }
        if (mapElement) {
            mapElement.style.height = '100%';
            mapElement.style.width = '100%';
        }
    }

    // Camera state
    let cameraStream = null;
    let useFrontCamera = false;
    let locationWatchId = null;
    let torchEnabled = false;
    let currentTrack = null;
    
    // Photos array - stores captured photos
    let capturedPhotos = [];
    
    // Location input references
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const locationInput = document.getElementById('location');
    const locationStatus = document.getElementById('location-status');

    // Initialize
    init();

    function init() {
        // Don't auto-start camera anymore
        // startCamera();
        
        // Set up event listeners
        setupEventListeners();
        
        // Try to get location automatically
        getCurrentLocation();
    }

    function setupEventListeners() {
        // Direct event listeners for critical buttons (backup)
        if (takePhotoBtn) {
            console.log('📸 Adding direct click listener to photo button');
            takePhotoBtn.addEventListener('click', function(e) {
                console.log('📸 PHOTO BUTTON CLICKED! (direct listener)');
                e.preventDefault();
                e.stopPropagation();
                takePhoto();
            });
        }
        
        if (flipCameraBtn) {
            flipCameraBtn.addEventListener('click', function(e) {
                console.log('🔄 Flip button clicked');
                e.preventDefault();
                e.stopPropagation();
                flipCamera();
            });
        }
        
        // Use event delegation for all interactive elements
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;

            const action = target.getAttribute('data-action');
            console.log('🎯 Action triggered:', action);
            
            switch(action) {
                case 'activate-camera':
                    e.preventDefault();
                    activateCamera();
                    break;
                case 'close-camera':
                    e.preventDefault();
                    closeCamera();
                    break;
                case 'take-photo':
                    console.log('📸 PHOTO BUTTON CLICKED! (delegation)');
                    e.preventDefault();
                    takePhoto();
                    break;
                case 'flip-camera':
                    e.preventDefault();
                    flipCamera();
                    break;
                case 'enable-camera':
                    e.preventDefault();
                    enableCameraAgain();
                    break;
                case 'toggle-torch':
                    e.preventDefault();
                    toggleTorch();
                    break;
                case 'done-photos':
                    console.log('✅ Done button clicked');
                    e.preventDefault();
                    closeCamera();
                    break;
            }
        });
        
        // File input handling with data attribute
        document.addEventListener('change', function(e) {
            if (e.target.matches('input[data-action="upload-photos"]')) {
                if (e.target.files && e.target.files.length > 0) {
                    // Check total photos limit
                    const remainingSlots = 6 - capturedPhotos.length;
                    if (e.target.files.length > remainingSlots) {
                        alert(`You can only add ${remainingSlots} more photo(s). Maximum 6 photos allowed.`);
                        e.target.value = ''; // Clear the file input
                        return;
                    }
                    
                    disableCamera();
                    
                    // Process multiple uploaded files
                    Array.from(e.target.files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            addPhotoToGallery(event.target.result, index);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }
        });
        
        // Location controls using data attributes
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;

            const action = target.getAttribute('data-action');
            
            if (action === 'get-location' || action === 'retry-location') {
                e.preventDefault();
                getCurrentLocation();
            }
        });
        
        // Form submission handler
        const form = document.querySelector('form[action*="incident_reports"]');
        if (form) {
            form.addEventListener('submit', async function(e) {
                console.log('Form submission started');
                e.preventDefault(); // Prevent normal form submission
                
                // Get form elements
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const incidentType = document.getElementById('incident_type');
                const description = document.getElementById('description');
                const photoData = document.getElementById('photo_data');
                const submitBtn = form.querySelector('button[type="submit"]');
                
                // Basic validation
                const errors = [];
                
                // Check location
                if (!latInput?.value || !lngInput?.value) {
                    errors.push('Please set your location before submitting');
                }
                
                // Check incident type
                if (!incidentType?.value) {
                    errors.push('Please select an incident type');
                }
                
                // Check description
                if (!description?.value.trim()) {
                    errors.push('Please enter a description');
                }
                
                // Check photo
                if (!photoData?.value) {
                    errors.push('Please take a photo or upload an image');
                }
                
                // Show all validation errors
                if (errors.length > 0) {
                    console.log('Validation errors:', errors);
                    alert(errors.join('\n'));
                    return false;
                }
                
                console.log('All validations passed, preparing to submit...');
                
                // If valid, submit the form
                try {
                    console.log('Disabling submit button');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Submitting...';
                    }
                    
                    // Create form data
                    const formData = new FormData(form);
                    
                    // Log form data for debugging
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}: ${value}`);
                    }
                    
                    console.log('Submitting form to:', form.action);
                    
                    // Submit the form using fetch for better error handling
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    });
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        console.error('Server error:', errorData);
                        throw new Error(errorData.message || 'Server error occurred');
                    }
                    
                    // If we get here, the form was submitted successfully
                    console.log('Form submitted successfully');
                    
                    // Let the form submit naturally
                    form.submit();
                    
                } catch (error) {
                    console.error('Form submission error:', error);
                    alert(`Error: ${error.message || 'An error occurred while submitting the form. Please try again.'}`);
                    
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit Report';
                    }
                }
            });
        }
    }

    // Camera Functions
    async function activateCamera() {
        console.log('Activating camera...');
        
        // Hide activation button, show camera section
        if (cameraActivation) cameraActivation.classList.add('hidden');
        if (cameraSection) cameraSection.classList.remove('hidden');
        
        // Start the camera
        await startCamera();
    }
    
    function closeCamera() {
        console.log('Closing camera...');
        
        // Stop camera stream
        stopCamera();
        
        // Show activation button, hide camera section
        if (cameraActivation) cameraActivation.classList.remove('hidden');
        if (cameraSection) cameraSection.classList.add('hidden');
    }
    
    async function startCamera() {
        console.log('Starting camera...');
        
        try {
            // Check if getUserMedia is available
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Camera API not supported in this browser or requires HTTPS');
            }
            
            // Stop any existing stream
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
            
            const constraints = {
                video: {
                    facingMode: useFrontCamera ? 'user' : 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };
            
            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = cameraStream;
            
            // Get video track for torch control
            const videoTracks = cameraStream.getVideoTracks();
            if (videoTracks.length > 0) {
                currentTrack = videoTracks[0];
                
                // Check if torch is supported
                const capabilities = currentTrack.getCapabilities();
                if (capabilities.torch && torchButton) {
                    torchButton.classList.remove('hidden');
                    console.log('Torch is supported on this device');
                } else if (torchButton) {
                    torchButton.classList.add('hidden');
                    console.log('Torch is not supported on this device');
                }
            }

            // Mirror the video preview for front camera
            if (useFrontCamera) {
                video.style.transform = 'scaleX(-1)';
                video.style.objectFit = 'cover';
                console.log('[DEBUG] Mirroring video: scaleX(-1)');
            } else {
                video.style.transform = 'scaleX(1)';
                video.style.objectFit = 'cover';
                console.log('[DEBUG] Not mirroring video: scaleX(1)');
            }
            
            // Enable camera controls
            if (takePhotoBtn) takePhotoBtn.disabled = false;
            if (flipCameraBtn) flipCameraBtn.disabled = false;
            
            // Show video element
            video.classList.remove('hidden');
            
            console.log('Camera started successfully');
            
        } catch (error) {
            console.error('Camera error:', error);
            alert('Could not access camera. Please check permissions and try again.');
            // Close camera UI if failed
            closeCamera();
        }
    }

    function stopCamera() {
        // Turn off torch before stopping
        if (torchEnabled && currentTrack) {
            currentTrack.applyConstraints({
                advanced: [{ torch: false }]
            }).catch(err => console.log('Error turning off torch:', err));
            torchEnabled = false;
        }
        
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        if (video) {
            video.srcObject = null;
        }
        currentTrack = null;
    }
    
    async function toggleTorch() {
        if (!currentTrack) {
            console.log('No camera track available');
            return;
        }
        
        try {
            const capabilities = currentTrack.getCapabilities();
            if (!capabilities.torch) {
                console.log('Torch not supported');
                return;
            }
            
            torchEnabled = !torchEnabled;
            
            await currentTrack.applyConstraints({
                advanced: [{ torch: torchEnabled }]
            });
            
            // Update torch button appearance
            if (torchButton) {
                if (torchEnabled) {
                    torchButton.classList.add('bg-yellow-500/50');
                    torchButton.classList.remove('bg-white/20');
                } else {
                    torchButton.classList.remove('bg-yellow-500/50');
                    torchButton.classList.add('bg-white/20');
                }
            }
            
            console.log('Torch toggled:', torchEnabled);
        } catch (error) {
            console.error('Error toggling torch:', error);
        }
    }

    async function flipCamera() {
        console.log('Flipping camera...');
        
        // Reset torch when flipping
        torchEnabled = false;
        
        useFrontCamera = !useFrontCamera;
        await startCamera();
    }

    function takePhoto() {
        console.log('Taking photo...');
        
        // Check photo limit
        if (capturedPhotos.length >= 6) {
            alert('Maximum 6 photos allowed. Please delete a photo to add more.');
            return;
        }
        
        try {
            if (!video.videoWidth || !video.videoHeight) {
                throw new Error('Video not ready');
            }
            
            // Flash effect
            if (cameraFlash) {
                cameraFlash.style.opacity = '0.8';
                setTimeout(() => {
                    cameraFlash.style.opacity = '0';
                }, 150);
            }
            
            // Set canvas size to match video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const ctx = canvas.getContext('2d');
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw video frame to canvas
            if (useFrontCamera) {
                // Mirror for front camera
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();
            } else {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            }
            
            // Convert to base64
            const photoDataUrl = canvas.toDataURL('image/jpeg', 0.8);
            
            // Add to photos array
            addPhotoToGallery(photoDataUrl, capturedPhotos.length);
            
            // Add thumbnail to camera view
            addCameraThumbnail(photoDataUrl, capturedPhotos.length - 1);
            
            // Update photo counter
            updatePhotoCounter();
            
            // Update hidden input with first photo (for backward compatibility)
            if (capturedPhotos.length === 1) {
                photoInput.value = photoDataUrl;
            }
            
            console.log('Photo captured successfully. Total photos:', capturedPhotos.length);
            
        } catch (error) {
            console.error('Error taking photo:', error);
            if (photoStatus) {
                photoStatus.textContent = 'Error: ' + error.message;
                photoStatus.classList.remove('hidden');
            }
        }
    }
    
    function addPhotoToGallery(photoDataUrl, index) {
        // Add to array
        capturedPhotos.push(photoDataUrl);
        
        // Update photos_data hidden input
        if (photosInput) {
            photosInput.value = JSON.stringify(capturedPhotos);
        }
        
        // Create thumbnail
        const photoWrapper = document.createElement('div');
        photoWrapper.className = 'relative group';
        photoWrapper.dataset.index = capturedPhotos.length - 1;
        
        const img = document.createElement('img');
        img.src = photoDataUrl;
        img.className = 'w-full h-24 object-cover rounded-md border border-gray-300';
        img.alt = `Photo ${capturedPhotos.length}`;
        img.style.cursor = 'pointer';
        img.onclick = function(e) {
            e.stopPropagation();
            openLightbox(capturedPhotos.length - 1);
        };
        
        // Delete button
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity';
        deleteBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        deleteBtn.onclick = function() {
            removePhoto(capturedPhotos.length - 1);
        };
        
        photoWrapper.appendChild(img);
        photoWrapper.appendChild(deleteBtn);
        
        // Show gallery
        if (photosGallery) {
            photosGallery.classList.remove('hidden');
            photosGallery.appendChild(photoWrapper);
        }
        
        // Update counter
        updatePhotoCount();
    }
    
    function removePhoto(index) {
        // Remove from array
        capturedPhotos.splice(index, 1);
        
        // Update hidden input
        if (photosInput) {
            photosInput.value = JSON.stringify(capturedPhotos);
        }
        
        // Rebuild gallery
        if (photosGallery) {
            photosGallery.innerHTML = '';
            capturedPhotos.forEach((photo, idx) => {
                const photoWrapper = document.createElement('div');
                photoWrapper.className = 'relative group';
                
                const img = document.createElement('img');
                img.src = photo;
                img.className = 'w-full h-24 object-cover rounded-md border border-gray-300';
                img.alt = `Photo ${idx + 1}`;
                img.style.cursor = 'pointer';
                img.onclick = function(e) {
                    e.stopPropagation();
                    openLightbox(idx);
                };
                
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity';
                deleteBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                deleteBtn.onclick = function() {
                    removePhoto(idx);
                };
                
                photoWrapper.appendChild(img);
                photoWrapper.appendChild(deleteBtn);
                photosGallery.appendChild(photoWrapper);
            });
            
            if (capturedPhotos.length === 0) {
                photosGallery.classList.add('hidden');
            }
        }
        
        // Rebuild camera thumbnails
        if (cameraThumbnails) {
            cameraThumbnails.innerHTML = '';
            capturedPhotos.forEach((photo, idx) => {
                addCameraThumbnail(photo, idx);
            });
        }
        
        // Update counters
        updatePhotoCount();
        updatePhotoCounter();
    }
    
    function updatePhotoCount() {
        if (photoCount) {
            photoCount.textContent = capturedPhotos.length;
        }
        if (photoStatus) {
            if (capturedPhotos.length > 0) {
                photoStatus.classList.remove('hidden');
            } else {
                photoStatus.classList.add('hidden');
            }
        }
    }
    
    function updatePhotoCounter() {
        if (photoCounter) {
            photoCounter.textContent = `${capturedPhotos.length}/6`;
        }
        
        // Show/hide done button based on photo count
        if (doneButton) {
            if (capturedPhotos.length > 0) {
                doneButton.classList.remove('hidden');
            } else {
                doneButton.classList.add('hidden');
            }
        }
    }
    
    function addCameraThumbnail(photoDataUrl, index) {
        if (!cameraThumbnails) return;
        
        const thumbWrapper = document.createElement('div');
        thumbWrapper.className = 'relative flex-shrink-0 animate-in';
        thumbWrapper.dataset.index = index;
        
        const thumb = document.createElement('img');
        thumb.src = photoDataUrl;
        thumb.className = 'w-16 h-16 object-cover rounded-lg border-2 border-green-500 shadow-lg';
        thumb.alt = `Photo ${index + 1}`;
        thumb.style.cursor = 'pointer';
        thumb.onclick = function(e) {
            e.stopPropagation();
            openLightbox(index);
        };
        
        // Success checkmark overlay
        const checkmark = document.createElement('div');
        checkmark.className = 'absolute inset-0 bg-green-500/80 rounded-lg flex items-center justify-center';
        checkmark.innerHTML = `
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
        
        thumbWrapper.appendChild(thumb);
        thumbWrapper.appendChild(checkmark);
        cameraThumbnails.appendChild(thumbWrapper);
        
        // Remove checkmark after animation
        setTimeout(() => {
            checkmark.style.opacity = '0';
            checkmark.style.transition = 'opacity 0.3s';
            setTimeout(() => checkmark.remove(), 300);
        }, 800);
        
        // Scroll to show latest thumbnail
        cameraThumbnails.scrollLeft = cameraThumbnails.scrollWidth;
    }

    function disableCamera() {
        stopCamera();
        
        if (takePhotoBtn) takePhotoBtn.disabled = true;
        if (flipCameraBtn) flipCameraBtn.disabled = true;
        if (reEnableBtn) reEnableBtn.classList.remove('hidden');
        if (photoStatus) photoStatus.classList.add('hidden');
    }

    function enableCameraAgain() {
        if (fileInput) fileInput.value = '';
        if (reEnableBtn) reEnableBtn.classList.add('hidden');
        startCamera();
    }

    // Location Functions
    function getCurrentLocation() {
        console.log('Getting current location...');
        
        if (!navigator.geolocation) {
            showLocationError('Geolocation is not supported by this browser');
            return;
        }
        
        // Show loading
        showLocationStatus('Getting your location...', false);
        
        const options = {
            enableHighAccuracy: true,
            timeout: 10000, // 10 seconds
            maximumAge: 0 // Force fresh location
        };
        
        navigator.geolocation.getCurrentPosition(
            handleLocationSuccess,
            handleLocationError,
            options
        );
    }

    function handleLocationSuccess(position) {
        console.log('[DEBUG] handleLocationSuccess triggered:', position);
        try {
            const lat = position.coords.latitude.toFixed(6);
            const lng = position.coords.longitude.toFixed(6);
            console.log(`[DEBUG] Got coordinates: ${lat}, ${lng}`);
            // Update hidden inputs
            if (latitudeInput) latitudeInput.value = lat;
            if (longitudeInput) longitudeInput.value = lng;
            showLocationStatus('Getting address details...', false);
            if (typeof L === 'undefined') {
                showLocationError('Map service not available. Please refresh the page and try again.');
                return;
            }
            // Always update the map and preview directly
            console.log('[DEBUG] Calling updateLocationPreview from handleLocationSuccess');
            updateLocationPreview(lat, lng, '');
            console.log('[DEBUG] Calling getAddressFromCoordinates from handleLocationSuccess');
            getAddressFromCoordinates(lat, lng);
        } catch (error) {
            console.error('Error in handleLocationSuccess:', error);
            showLocationError('Error processing your location. Please try again.');
            hideLocationLoading();
        }
    }

    function handleLocationError(error) {
        console.error('Location error:', error);
        
        let errorMessage = 'Unable to get location';
        switch (error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Location access denied. Please enable location permissions.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location information unavailable.';
                break;
            case error.TIMEOUT:
                errorMessage = 'Location request timed out.';
                break;
        }
        
        showLocationError(errorMessage);
        hideLocationLoading();
    }

    // Map variables
    // Initialize the map only once
    function initMap(lat, lng) {
        console.log('[DEBUG] initMap called with:', lat, lng);
        if (map) return; // Already initialized
        try {
            // Ensure map container is visible and sized before initializing map
            if (mapContainer) {
                mapContainer.style.display = 'block';
                // Use 300px minimum height for better mobile visibility
                mapContainer.style.height = '300px';
                mapContainer.style.minHeight = '300px';
                mapContainer.classList.remove('hidden');
            }
            if (mapElement) {
                mapElement.style.height = '100%';
                mapElement.style.width = '100%';
                mapElement.style.minHeight = '300px';
            }
            // Now initialize the map
            map = L.map('map', {
                center: [parseFloat(lat), parseFloat(lng)],
                zoom: 15,
                zoomControl: false
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            L.control.zoom({ position: 'topright' }).addTo(map);
            setTimeout(() => { map.invalidateSize(); }, 150);
            console.log('Map initialized');
        } catch (error) {
            console.error('Error initializing map:', error);
            showLocationError('Failed to load map. Please try again.');
        }
    }
    
    // Update the map view and marker
    function updateMap(lat, lng) {
        console.log(`[DEBUG] updateMap: lat=${lat}, lng=${lng}`);
        if (!map) {
            console.log('[DEBUG] Map not initialized, calling initMap');
            initMap(lat, lng);
        }
        if (!map) {
            console.log('[DEBUG] Map still not initialized!');
            return; // Still failed
        }
        const latLng = [parseFloat(lat), parseFloat(lng)];
        console.log('[DEBUG] updateMap using latLng:', latLng);
        if (marker) {
            marker.setLatLng(latLng);
            console.log('[DEBUG] Marker position updated');
        } else {
            // Create draggable marker
            marker = L.marker(latLng, {
                draggable: true,
                title: 'Drag me to adjust location'
            }).addTo(map);
            console.log('[DEBUG] Draggable marker created');
            
            // Add drag event listener
            marker.on('dragend', function(event) {
                const position = marker.getLatLng();
                const newLat = position.lat.toFixed(6);
                const newLng = position.lng.toFixed(6);
                console.log('[DEBUG] Marker dragged to:', newLat, newLng);
                
                // Update inputs
                if (latitudeInput) latitudeInput.value = newLat;
                if (longitudeInput) longitudeInput.value = newLng;
                
                // Get new address
                showLocationStatus('Updating location...', false);
                getAddressFromCoordinates(newLat, newLng);
            });
        }
        
        // Add click event to map to place marker
        map.off('click'); // Remove previous listener
        map.on('click', function(e) {
            const clickedLat = e.latlng.lat.toFixed(6);
            const clickedLng = e.latlng.lng.toFixed(6);
            console.log('[DEBUG] Map clicked at:', clickedLat, clickedLng);
            
            // Move marker to clicked position
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, {
                    draggable: true,
                    title: 'Drag me to adjust location'
                }).addTo(map);
                
                // Add drag event listener
                marker.on('dragend', function(event) {
                    const position = marker.getLatLng();
                    const newLat = position.lat.toFixed(6);
                    const newLng = position.lng.toFixed(6);
                    console.log('[DEBUG] Marker dragged to:', newLat, newLng);
                    
                    // Update inputs
                    if (latitudeInput) latitudeInput.value = newLat;
                    if (longitudeInput) longitudeInput.value = newLng;
                    
                    // Get new address
                    showLocationStatus('Updating location...', false);
                    getAddressFromCoordinates(newLat, newLng);
                });
            }
            
            // Update inputs
            if (latitudeInput) latitudeInput.value = clickedLat;
            if (longitudeInput) longitudeInput.value = clickedLng;
            
            // Get address for clicked location
            showLocationStatus('Getting location details...', false);
            getAddressFromCoordinates(clickedLat, clickedLng);
        });
        
        map.setView(latLng, 15);
        // Always show the map container
        forceShowMapContainer();
        setTimeout(() => { map.invalidateSize(); }, 150);
    }
    

    
    function getAddressFromCoordinates(lat, lng) {
        console.log('[DEBUG] getAddressFromCoordinates called:', { lat, lng });
        showLocationStatus('Getting address details...', false);
        fetch(`/reverse-geocode?lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                let address = '';
                if (data.display_name) {
                    address = data.display_name;
                } else {
                    address = `Location at ${lat}, ${lng}`;
                }
                console.log('[DEBUG] getAddressFromCoordinates got address:', address);
                updateLocationPreview(lat, lng, address);
                setTimeout(() => {
                    hideLocationLoading();
                }, 100);
            })
            .catch(error => {
                console.error('Error getting address:', error);
                updateLocationPreview(lat, lng, `Location at ${lat}, ${lng}`);
                showLocationError('Error getting address details. Using coordinates only.');
                hideLocationLoading();
            });
    }
    
    // Show location preview and update map
    function updateLocationPreview(lat, lng, address = '') {
        console.log(`[DEBUG] updateLocationPreview called: lat=${lat}, lng=${lng}, address=${address}`);
        if (locationPreview) {
            locationPreview.classList.remove('hidden');
            locationPreview.style.display = 'block';
        }
        // Always show the map container when preview updates
        forceShowMapContainer();
        if (locationAddress) {
            locationAddress.textContent = address || 'Location not available';
        }
        if (locationCoordinates) {
            locationCoordinates.textContent = `Coordinates: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`;
        }
        if (latitudeInput) latitudeInput.value = lat;
        if (longitudeInput) longitudeInput.value = lng;
        if (locationInput) {
            locationInput.value = address || `${lat}, ${lng}`;
            locationInput.dataset.coordinates = `${lat},${lng}`;
        }
        console.log('[DEBUG] Calling updateMap from updateLocationPreview');
        updateMap(lat, lng);
    }
    
    function showLocationStatus(message, isError = false) {
        if (locationStatus) {
            locationStatus.textContent = message;
            locationStatus.className = isError ? 'error-message' : 'text-gray-600 text-sm mt-1';
            locationStatus.classList.remove('hidden');
        }
        
        if (locationLoading) {
            locationLoading.classList.add('hidden');
        }
    }
    
    function hideLocationLoading() {
        console.log('Hiding location loading...');
        
        // Hide the loading spinner
        const loadingElement = document.getElementById('location-loading');
        if (loadingElement) {
            loadingElement.classList.add('hidden');
            console.log('Loading spinner hidden');
        }
        
        // Clear the status message
        if (locationStatus) {
            locationStatus.classList.add('hidden');
            console.log('Status message hidden');
        }
        
        // Also ensure any error messages are cleared
        const errorElement = document.getElementById('location-error');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.add('hidden');
        }
    }
    
    function showLocationError(message) {
        showLocationStatus(message, true);
    }

    // Form validation function
    function validateForm(e) {
        console.log('Validating form...');
        let isValid = true;
        
        // Check if location is set
        if ((!latitudeInput || !longitudeInput || !latitudeInput.value || !longitudeInput.value) && 
            (!locationInput || !locationInput.value)) {
            console.log('Location validation failed');
            showLocationError('Please set your location before submitting');
            isValid = false;
        } else {
            console.log('Location is valid');
        }
        
        // Check incident type
        const incidentType = document.getElementById('incident_type');
        if (incidentType && !incidentType.value) {
            console.log('Incident type validation failed');
            alert('Please select an incident type');
            isValid = false;
        } else {
            console.log('Incident type is valid');
        }
        
        // Check description
        const description = document.getElementById('description');
        if (description && !description.value.trim()) {
            console.log('Description validation failed');
            alert('Please enter a description');
            description.focus();
            isValid = false;
        } else {
            console.log('Description is valid');
        }
        
        // Check photo
        if (!photoInput || !photoInput.value) {
            console.log('Photo validation failed');
            alert('Please take a photo or upload an image');
            if (takePhotoBtn) takePhotoBtn.scrollIntoView({ behavior: 'smooth' });
            isValid = false;
        } else {
            console.log('Photo is valid');
        }
        
        console.log('Form validation result:', isValid);
        return isValid;
    }
    
    // Lightbox functionality
    let currentLightboxIndex = 0;
    
    window.openLightbox = function(index) {
        const lightbox = document.getElementById('photoLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');
        
        if (!lightbox || !lightboxImage) return;
        
        currentLightboxIndex = index;
        
        if (capturedPhotos.length > 0 && capturedPhotos[index]) {
            lightboxImage.src = capturedPhotos[index];
            lightboxCounter.textContent = `${index + 1} / ${capturedPhotos.length}`;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
    };
    
    window.closeLightbox = function() {
        const lightbox = document.getElementById('photoLightbox');
        if (lightbox) {
            lightbox.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }
    };
    
    window.changeLightboxPhoto = function(direction) {
        if (capturedPhotos.length === 0) return;
        
        currentLightboxIndex += direction;
        
        // Wrap around
        if (currentLightboxIndex < 0) {
            currentLightboxIndex = capturedPhotos.length - 1;
        } else if (currentLightboxIndex >= capturedPhotos.length) {
            currentLightboxIndex = 0;
        }
        
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');
        
        if (lightboxImage && capturedPhotos[currentLightboxIndex]) {
            lightboxImage.src = capturedPhotos[currentLightboxIndex];
        }
        
        if (lightboxCounter) {
            lightboxCounter.textContent = `${currentLightboxIndex + 1} / ${capturedPhotos.length}`;
        }
    };
    
    // Close lightbox on Escape key
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('photoLightbox');
        if (lightbox && lightbox.classList.contains('active')) {
            if (e.key === 'Escape') {
                closeLightbox();
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
            closeLightbox();
        }
    });
    
    // Touch/swipe support for lightbox
    let lightboxTouchStartX = 0;
    let lightboxTouchEndX = 0;
    
    const lightboxElement = document.getElementById('photoLightbox');
    if (lightboxElement) {
        lightboxElement.addEventListener('touchstart', function(e) {
            lightboxTouchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        lightboxElement.addEventListener('touchend', function(e) {
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
});
