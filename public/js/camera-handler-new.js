// Camera handler for ID uploads
class CameraHandler {
    constructor(side) {
        console.log(`Initializing CameraHandler for ${side}...`);
        this.side = side;
        
        // Initialize elements
        this.initializeElements();
        
        this.stream = null;
        this.currentFacingMode = 'environment'; // Default to back camera
        
        // Initialize event listeners
        this.setupEventListeners();
        console.log(`[${side}] CameraHandler initialized`);
    }
    
    initializeElements() {
        // Define all element IDs and their properties
        const elements = {
            cameraContainer: { id: `${this.side}_camera_container`, element: null },
            camera: { id: `${this.side}_camera`, element: null },
            snapshot: { id: `${this.side}_snapshot`, element: null },
            idPreview: { id: `${this.side}_id_preview`, element: null },
            cameraPlaceholder: { id: `${this.side}_camera_placeholder`, element: null },
            uploadBtn: { id: `${this.side}_upload_btn`, element: null },
            cameraBtn: { id: `${this.side}_camera_btn`, element: null },
            captureBtn: { id: `${this.side}_capture_btn`, element: null },
            retakeBtn: { id: `${this.side}_retake_btn`, element: null },
            switchCameraBtn: { id: `${this.side}_switch_camera`, element: null },
            fileInput: { id: `${this.side}_valid_id`, element: null },
            idPhotoData: { id: `${this.side}_id_photo_data`, element: null }
        };
        
        // Initialize elements and log their status
        for (const [key, data] of Object.entries(elements)) {
            const element = document.getElementById(data.id);
            this[key] = element;
            console.log(`[${this.side}] Element ${key} (${data.id}):`, element ? 'Found' : 'Not found');
        }
    }
    
    setupEventListeners() {
        // Camera button
        if (this.cameraBtn) {
            this.cameraBtn.addEventListener('click', () => this.startCamera());
        } else {
            console.error(`[${this.side}] Camera button not found`);
        }
        
        // Capture button
        if (this.captureBtn) {
            this.captureBtn.addEventListener('click', () => this.capturePhoto());
        }
        
        // Upload button
        if (this.uploadBtn && this.fileInput) {
            this.uploadBtn.addEventListener('click', () => this.fileInput.click());
        }
        
        // Retake button
        if (this.retakeBtn) {
            this.retakeBtn.addEventListener('click', () => this.retakePhoto());
        }
        
        // Switch camera button
        if (this.switchCameraBtn) {
            this.switchCameraBtn.addEventListener('click', () => this.switchCamera());
        }
        
        // File input
        if (this.fileInput) {
            this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }
        
        // Drag and drop
        if (this.cameraContainer) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.cameraContainer.addEventListener(eventName, this.preventDefaults, false);
            });
            
            ['dragenter', 'dragover'].forEach(eventName => {
                this.cameraContainer.addEventListener(eventName, () => this.highlight(), false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                this.cameraContainer.addEventListener(eventName, () => this.unhighlight(), false);
            });
            
            this.cameraContainer.addEventListener('drop', (e) => this.handleDrop(e), false);
        }
    }
    
    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    highlight() {
        if (this.cameraContainer) {
            this.cameraContainer.classList.add('border-indigo-500', 'bg-indigo-50');
        }
    }
    
    unhighlight() {
        if (this.cameraContainer) {
            this.cameraContainer.classList.remove('border-indigo-500', 'bg-indigo-50');
        }
    }
    
    async startCamera() {
        console.log(`[${this.side}] Starting camera...`);
        try {
            // Stop any existing stream
            await this.stopCamera();
            
            // Start the camera stream
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    facingMode: this.currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });
            
            if (this.camera) {
                this.camera.srcObject = this.stream;
                this.camera.classList.remove('hidden');
                
                if (this.cameraPlaceholder) {
                    this.cameraPlaceholder.classList.add('hidden');
                }
                
                if (this.idPreview) {
                    this.idPreview.classList.add('hidden');
                }
                
                // Check for multiple cameras
                try {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    console.log(`[${this.side}] Found ${videoDevices.length} video devices`);
                    
                    // Show switch button if multiple cameras are available
                    if (this.switchCameraBtn) {
                        this.switchCameraBtn.classList.toggle('hidden', videoDevices.length <= 1);
                    }
                } catch (e) {
                    console.warn(`[${this.side}] Could not enumerate devices:`, e);
                }
                
                this.updateUI('cameraActive');
                
                // Play the video
                await this.camera.play();
            }
            
        } catch (err) {
            console.error(`[${this.side}] Error accessing camera:`, err);
            alert(`Could not access the camera: ${err.message || 'Please check your permissions.'}`);
            if (this.switchCameraBtn) {
                this.switchCameraBtn.classList.add('hidden');
            }
            this.updateUI('error');
        }
    }
    
    async stopCamera() {
        if (this.stream) {
            console.log(`[${this.side}] Stopping camera stream`);
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
    }
    
    capturePhoto() {
        console.log(`[${this.side}] Capturing photo...`);
        
        if (!this.camera || !this.snapshot) {
            console.error(`[${this.side}] Camera or snapshot element not found`);
            return;
        }
        
        try {
            // Get the actual video dimensions
            const video = this.camera;
            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;
            
            if (videoWidth === 0 || videoHeight === 0) {
                console.error(`[${this.side}] Invalid video dimensions:`, { width: videoWidth, height: videoHeight });
                alert('Failed to capture photo: Invalid video dimensions. Please try again.');
                return;
            }
            
            console.log(`[${this.side}] Video dimensions:`, { width: videoWidth, height: videoHeight });
            
            // Set canvas dimensions to match video
            this.snapshot.width = videoWidth;
            this.snapshot.height = videoHeight;
            
            // Draw video frame to canvas
            const context = this.snapshot.getContext('2d');
            context.drawImage(video, 0, 0, videoWidth, videoHeight);
            
            // Get image data as base64
            const imageData = this.snapshot.toDataURL('image/jpeg', 0.8);
            console.log(`[${this.side}] Captured photo (${imageData.length} chars)`);
            
            if (!imageData || !imageData.startsWith('data:image/')) {
                console.error(`[${this.side}] Invalid image data captured`);
                alert('Failed to capture photo: Invalid image data');
                return;
            }
            
            // Stop the camera
            this.stopCamera();
            
            // Update preview
            this.updatePreview(imageData);
            
        } catch (err) {
            console.error(`[${this.side}] Error capturing photo:`, err);
            alert('Failed to capture photo: ' + (err.message || 'Please try again'));
        }
    }
    
    async retakePhoto() {
        console.log(`[${this.side}] Retaking photo...`);
        await this.startCamera();
    }
    
    async switchCamera() {
        console.log(`[${this.side}] Switching camera...`);
        this.currentFacingMode = this.currentFacingMode === 'environment' ? 'user' : 'environment';
        await this.startCamera();
    }
    
    handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        if (!file.type.match('image.*')) {
            alert('Please select an image file (JPEG, PNG)');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (event) => {
            try {
                // Stop any active camera stream
                this.stopCamera();
                
                // Update preview
                this.updatePreview(event.target.result);
                
            } catch (err) {
                console.error(`[${this.side}] Error processing file:`, err);
                alert('Error processing the selected file');
            }
        };
        
        reader.onerror = (error) => {
            console.error(`[${this.side}] FileReader error:`, error);
            alert('Error reading the selected file');
        };
        
        reader.readAsDataURL(file);
    }
    
    handleDrop(e) {
        const dt = e.dataTransfer;
        const file = dt.files[0];
        
        if (file && file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    // Stop any active camera stream
                    this.stopCamera();
                    
                    // Update preview
                    this.updatePreview(event.target.result);
                    
                } catch (err) {
                    console.error(`[${this.side}] Error processing dropped file:`, err);
                    alert('Error processing the dropped file');
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
    updatePreview(imageData) {
        console.log(`[${this.side}] Updating preview with image data`);
        
        // Ensure we have valid image data
        if (!imageData || typeof imageData !== 'string') {
            console.error(`[${this.side}] Invalid image data`);
            return;
        }
        
        // Ensure the image data has the proper data URI prefix
        const fullImageData = imageData.startsWith('data:image/') ? imageData : `data:image/jpeg;base64,${imageData}`;
        
        // Update the preview image
        if (this.idPreview) {
            this.idPreview.src = fullImageData;
            this.idPreview.classList.remove('hidden');
        }
        
        // Hide camera and show preview
        if (this.camera) this.camera.classList.add('hidden');
        if (this.cameraPlaceholder) this.cameraPlaceholder.classList.add('hidden');
        
        // Set the hidden input values
        const inputName = this.side === 'front' ? 'front_id_photo_data' : 'back_id_photo_data';
        
        // Update or create the hidden input
        let input = document.getElementById(inputName);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName;
            input.id = inputName;
            document.querySelector('form').appendChild(input);
        }
        
        // Set the value
        input.value = fullImageData;
        console.log(`[${this.side}] Set ${inputName} value (${input.value.length} chars)`);
        
        // Store the value in the camera handler for recovery if needed
        this.idPhotoData = { value: fullImageData };
        
        // Update UI
        this.updateUI('previewActive');
        
        // Debug: Verify the input was set
        console.log(`[${this.side}] Input value set:`, !!document.getElementById(inputName)?.value);
    }
    
    updateUI(state) {
        console.log(`[${this.side}] Updating UI state:`, state);
        
        if (!this.cameraBtn || !this.captureBtn || !this.uploadBtn || !this.retakeBtn) {
            console.error(`[${this.side}] Missing required elements for UI update`);
            return;
        }
        
        switch (state) {
            case 'cameraActive':
                this.cameraBtn.classList.add('hidden');
                this.captureBtn.classList.remove('hidden');
                this.uploadBtn.classList.add('hidden');
                this.retakeBtn.classList.add('hidden');
                if (this.switchCameraBtn) this.switchCameraBtn.classList.remove('hidden');
                break;
                
            case 'previewActive':
                this.cameraBtn.classList.remove('hidden');
                this.captureBtn.classList.add('hidden');
                this.uploadBtn.classList.remove('hidden');
                this.retakeBtn.classList.remove('hidden');
                if (this.switchCameraBtn) this.switchCameraBtn.classList.add('hidden');
                break;
                
            case 'error':
                this.cameraBtn.classList.remove('hidden');
                this.captureBtn.classList.add('hidden');
                this.uploadBtn.classList.remove('hidden');
                this.retakeBtn.classList.add('hidden');
                if (this.switchCameraBtn) this.switchCameraBtn.classList.add('hidden');
                break;
        }
    }
}

// Create hidden input if it doesn't exist
CameraHandler.prototype.createHiddenInput = function(imageData) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `${this.side}_id_photo_data`;
    input.id = `${this.side}_id_photo_data`;
    input.value = imageData;
    document.querySelector('form').appendChild(input);
    this.idPhotoData = input;
    console.log(`[${this.side}] Created hidden input element`);
};

// Function to initialize camera handlers
function initializeCameraHandlers() {
    console.log('=== INITIALIZING CAMERA HANDLERS ===');
    
    // Check if the form exists
    const form = document.querySelector('form');
    if (!form) {
        console.error('Form element not found!');
        return;
    }
    
    // Create hidden inputs if they don't exist
    function ensureHiddenInput(id) {
        let input = document.getElementById(id);
        if (!input) {
            console.log(`Creating missing hidden input: ${id}`);
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = id; // Important: match backend expectation
            input.id = id;
            form.appendChild(input);
        }
        return input;
    }
    
    // Ensure both hidden inputs exist
    const frontInput = ensureHiddenInput('front_id_photo_data');
    const backInput = ensureHiddenInput('back_id_photo_data');
    
    console.log('Front ID input element:', frontInput);
    console.log('Back ID input element:', backInput);
    
    // Log form enctype to ensure it supports file uploads
    console.log('Form enctype:', form.enctype);
    if (form.enctype !== 'multipart/form-data') {
        console.warn('Form enctype is not set to multipart/form-data. This may cause issues with file uploads.');
        form.enctype = 'multipart/form-data'; // Ensure it's set correctly
    }
    
    // Log form attributes for debugging
    console.log('Form attributes:', {
        id: form.id,
        action: form.action,
        method: form.method,
        enctype: form.enctype
    });
    
    try {
        // Initialize both front and back camera handlers
        console.log('Initializing front camera handler...');
        window.frontCamera = new CameraHandler('front');
        
        console.log('Initializing back camera handler...');
        window.backCamera = new CameraHandler('back');
        
        console.log('Camera handlers initialized successfully');
        
        // Add a small delay to ensure all elements are ready
        setTimeout(() => {
            console.log('Checking camera handler initialization status:');
            console.log('Front camera handler:', window.frontCamera ? 'Initialized' : 'Failed');
            console.log('Back camera handler:', window.backCamera ? 'Initialized' : 'Failed');
        }, 500);
        
    } catch (error) {
        console.error('Error initializing camera handlers:', error);
    }
}

// Initialize camera handlers when DOM is fully loaded
function onDOMContentLoaded() {
    console.log('DOM fully loaded, initializing camera handlers...');
    
    // Ensure the form has the correct enctype
    const form = document.querySelector('form');
    if (form) {
        form.enctype = 'multipart/form-data';
        
        // Add form submission handler
        form.addEventListener('submit', function(e) {
            console.log('Form submission started...');
            
            const frontPhoto = document.querySelector('input[name="front_id_photo_data"]');
            const backPhoto = document.querySelector('input[name="back_id_photo_data"]');
            
            if (!frontPhoto?.value) {
                e.preventDefault();
                alert('Please take or upload a photo of the front of your ID');
                return false;
            }
            
            if (!backPhoto?.value) {
                e.preventDefault();
                alert('Please take or upload a photo of the back of your ID');
                return false;
            }
            
            console.log('Form submission proceeding...');
            return true;
        });
    }
    
    // Initialize camera handlers after setting up form
    initializeCameraHandlers();
    
    // Add a small delay to log initialization status
    setTimeout(() => {
        console.log('=== INITIALIZATION COMPLETE ===');
        console.log('Front camera handler:', window.frontCamera ? 'Initialized' : 'Failed');
        console.log('Back camera handler:', window.backCamera ? 'Initialized' : 'Failed');
    }, 500);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onDOMContentLoaded);
} else {
    // DOM is already ready
    onDOMContentLoaded();
}

// Add debug function to window for manual testing
window.debugCameraHandlers = function() {
    console.log('=== DEBUGGING CAMERA HANDLERS ===');
    console.log('Window.frontCamera:', window.frontCamera);
    console.log('Window.backCamera:', window.backCamera);
    
    const frontInput = document.getElementById('front_id_photo_data');
    const backInput = document.getElementById('back_id_photo_data');
    
    console.log('Front ID input value length:', frontInput ? frontInput.value.length : 'Not found');
    console.log('Back ID input value length:', backInput ? backInput.value.length : 'Not found');
    
    if (window.frontCamera) {
        console.log('Front Camera Handler Elements:', {
            camera: window.frontCamera.camera ? 'Found' : 'Not found',
            snapshot: window.frontCamera.snapshot ? 'Found' : 'Not found',
            idPreview: window.frontCamera.idPreview ? 'Found' : 'Not found',
            idPhotoData: window.frontCamera.idPhotoData ? 'Found' : 'Not found',
            photoDataLength: window.frontCamera.idPhotoData ? window.frontCamera.idPhotoData.value.length : 0
        });
    }
    
    if (window.backCamera) {
        console.log('Back Camera Handler Elements:', {
            camera: window.backCamera.camera ? 'Found' : 'Not found',
            snapshot: window.backCamera.snapshot ? 'Found' : 'Not found',
            idPreview: window.backCamera.idPreview ? 'Found' : 'Not found',
            idPhotoData: window.backCamera.idPhotoData ? 'Found' : 'Not found',
            photoDataLength: window.backCamera.idPhotoData ? window.backCamera.idPhotoData.value.length : 0
        });
    }
};

// Clean up camera streams when leaving the page
window.addEventListener('beforeunload', () => {
    if (window.frontCamera) window.frontCamera.stopCamera();
    if (window.backCamera) window.backCamera.stopCamera();
});
