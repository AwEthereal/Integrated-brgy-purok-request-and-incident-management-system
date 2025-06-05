// Camera handler for ID uploads
class CameraHandler {
    constructor(side) {
        console.log(`Initializing CameraHandler for ${side}...`);
        this.side = side;
        
        // Get all required elements
        const elements = [
            'cameraContainer', 'camera', 'snapshot', 'idPreview', 
            'cameraPlaceholder', 'uploadBtn', 'cameraBtn', 'captureBtn',
            'retakeBtn', 'switchCameraBtn', 'fileInput', 'idPhotoData'
        ];
        
        // Initialize all elements
        elements.forEach(el => {
            const id = `${side}-${el.replace(/([A-Z])/g, '-$1').toLowerCase()}`;
            this[el] = document.getElementById(id);
            console.log(`[${side}] Element ${el} (${id}):`, this[el] ? 'Found' : 'Not found');
        });
        
        this.stream = null;
        this.currentFacingMode = 'environment'; // Default to back camera
        
        // Initialize event listeners
        this.setupEventListeners();
        console.log(`[${side}] CameraHandler initialized`);
    }
    
    log(...args) {
        console.log(`[${this.side}]`, ...args);
    }
    
    setupEventListeners() {
        // Camera button
        this.cameraBtn?.addEventListener('click', () => this.startCamera());
        
        // Capture button
        this.captureBtn?.addEventListener('click', () => this.capturePhoto());
        
        // Retake button
        this.retakeBtn?.addEventListener('click', () => this.retakePhoto());
        
        // Upload button
        this.uploadBtn?.addEventListener('click', () => this.fileInput?.click());
        
        // Switch camera button
        this.switchCameraBtn?.addEventListener('click', () => this.switchCamera());
        
        // File input
        this.fileInput?.addEventListener('change', (e) => this.handleFileSelect(e));
        
        // Drag and drop
        if (this.cameraContainer) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.cameraContainer.addEventListener(eventName, (e) => this.preventDefaults(e), false);
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
        this.cameraContainer?.classList.add('border-indigo-500', 'bg-indigo-50');
    }
    
    unhighlight() {
        this.cameraContainer?.classList.remove('border-indigo-500', 'bg-indigo-50');
    }
    
    async startCamera() {
        this.log('Starting camera...');
        try {
            // Show switch camera button when camera is active
            this.switchCameraBtn?.classList.add('hidden');
            
            // Stop any existing stream
            await this.stopCamera();
            
            // Start the camera stream with basic constraints first
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    facingMode: this.currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });
            
            this.log('Camera stream started');
            this.camera.srcObject = this.stream;
            this.camera.classList.remove('hidden');
            this.cameraPlaceholder?.classList.add('hidden');
            this.idPreview?.classList.add('hidden');
            
            // Check for multiple cameras after getting initial access
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                this.log(`Found ${videoDevices.length} video devices`);
                
                // Only show switch button if multiple cameras are available
                if (videoDevices.length > 1) {
                    this.switchCameraBtn?.classList.remove('hidden');
                    this.log('Showing switch camera button');
                }
            } catch (e) {
                console.warn('Could not enumerate devices:', e);
            }
            
            this.updateUI('cameraActive');
            
            // Play the video to start the stream
            await this.camera.play();
            
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert(`Could not access the camera: ${err.message || 'Please check your permissions.'}`);
            this.switchCameraBtn?.classList.add('hidden');
            this.updateUI('error');
        }
    }
    
    async stopCamera() {
        if (this.stream) {
            this.log('Stopping camera stream');
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
    }
    
    capturePhoto() {
        this.log('Capturing photo...');
        try {
            // Make sure video is ready
            if (this.camera.readyState === 0) {
                throw new Error('Video stream not ready');
            }
            
            // Set canvas size to match video
            const context = this.snapshot.getContext('2d');
            const width = this.camera.videoWidth;
            const height = this.camera.videoHeight;
            
            // Set canvas dimensions
            this.snapshot.width = width;
            this.snapshot.height = height;
            
            // Draw the current video frame to the canvas
            context.drawImage(this.camera, 0, 0, width, height);
            
            // Get the image data
            const imageData = this.snapshot.toDataURL('image/jpeg');
            
            // Stop camera stream
            this.stopCamera();
            
            // Update preview
            this.updatePreview(imageData);
            
            this.log('Photo captured successfully');
            
        } catch (err) {
            console.error('Error capturing photo:', err);
            alert('Failed to capture photo: ' + (err.message || 'Please try again'));
        }
    }
    
    async retakePhoto() {
        this.log('Retaking photo...');
        await this.startCamera();
    }
    
    async switchCamera() {
        this.log('Switching camera...');
        try {
            // Toggle between front and back camera
            this.currentFacingMode = this.currentFacingMode === 'environment' ? 'user' : 'environment';
            await this.startCamera();
        } catch (err) {
            console.error('Error switching camera:', err);
            alert('Failed to switch camera. Please try again.');
        }
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
                
                this.log('File loaded successfully');
            } catch (err) {
                console.error('Error processing file:', err);
                alert('Error processing the selected file');
            }
        };
        reader.onerror = (error) => {
            console.error('FileReader error:', error);
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
                    
                    this.log('Dropped file loaded successfully');
                } catch (err) {
                    console.error('Error processing dropped file:', err);
                    alert('Error processing the dropped file');
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
    updatePreview(imageData) {
        console.log(`[${this.side}] Updating preview with image data:`, imageData ? 'Data received' : 'No data');
        
        // Update the preview image
        if (this.idPreview) {
            this.idPreview.src = imageData;
            this.idPreview.classList.remove('hidden');
        } else {
            console.error(`[${this.side}] Preview element not found`);
        }
        
        // Hide camera and show preview
        if (this.camera) this.camera.classList.add('hidden');
        if (this.cameraPlaceholder) this.cameraPlaceholder.classList.add('hidden');
        
        // Update hidden field with image data
        if (this.idPhotoData) {
            this.idPhotoData.value = imageData;
            console.log(`[${this.side}] Hidden input updated:`, this.idPhotoData.value ? 'Has value' : 'Empty');
        } else {
            console.error(`[${this.side}] Hidden input element not found`);
        }
        
        // Log the current value of the hidden input for debugging
        console.log(`[${this.side}] Current hidden input value:`, this.idPhotoData?.value ? 'Has value' : 'Empty');
        
        // Update UI
        this.updateUI('previewActive');
    }
    
    updateUI(state) {
        switch (state) {
            case 'cameraActive':
                this.cameraBtn?.classList.add('hidden');
                this.captureBtn?.classList.remove('hidden');
                this.uploadBtn?.classList.add('hidden');
                this.retakeBtn?.classList.add('hidden');
                break;
                
            case 'previewActive':
                this.cameraBtn?.classList.remove('hidden');
                this.captureBtn?.classList.add('hidden');
                this.uploadBtn?.classList.remove('hidden');
                this.retakeBtn?.classList.remove('hidden');
                this.switchCameraBtn?.classList.add('hidden');
                break;
                
            case 'error':
                this.cameraBtn?.classList.remove('hidden');
                this.captureBtn?.classList.add('hidden');
                this.uploadBtn?.classList.remove('hidden');
                this.retakeBtn?.classList.add('hidden');
                this.switchCameraBtn?.classList.add('hidden');
                break;
        }
    }
    
    // Log the current value of the hidden input for debugging
    console.log(`[${this.side}] Current hidden input value:`, this.idPhotoData ? (this.idPhotoData.value ? 'Has value' : 'Empty') : 'No element found');
    
    // Update UI
    this.updateUI('previewActive');
}

updateUI(state) {
    if (!this.cameraBtn || !this.captureBtn || !this.uploadBtn || !this.retakeBtn) {
        console.error(`[${this.side}] Missing required elements for UI update`);
        return;
    }
    
    switch (state) {
        case 'cameraActive':
            this.cameraBtn.classList.add('hidden');
            if (this.captureBtn) this.captureBtn.classList.remove('hidden');
            if (this.uploadBtn) this.uploadBtn.classList.add('hidden');
            if (this.retakeBtn) this.retakeBtn.classList.add('hidden');
            break;
            
        case 'previewActive':
            this.cameraBtn.classList.remove('hidden');
            if (this.captureBtn) this.captureBtn.classList.add('hidden');
            if (this.uploadBtn) this.uploadBtn.classList.remove('hidden');
            if (this.retakeBtn) this.retakeBtn.classList.remove('hidden');
            if (this.switchCameraBtn) this.switchCameraBtn.classList.add('hidden');
            break;
            
        case 'error':
            this.cameraBtn.classList.remove('hidden');
            if (this.captureBtn) this.captureBtn.classList.add('hidden');
            if (this.uploadBtn) this.uploadBtn.classList.remove('hidden');
            if (this.retakeBtn) this.retakeBtn.classList.add('hidden');
            if (this.switchCameraBtn) this.switchCameraBtn.classList.add('hidden');
            break;
    }
}
}

// Initialize camera handlers when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded, initializing camera handlers...');
    
    try {
        // Initialize both front and back camera handlers
        window.frontCamera = new CameraHandler('front');
        window.backCamera = new CameraHandler('back');
        
        console.log('Camera handlers initialized successfully');
        console.log('Front camera handler:', window.frontCamera);
        console.log('Back camera handler:', window.backCamera);
    } catch (error) {
        console.error('Error initializing camera handlers:', error);
    }
});

// Clean up camera streams when leaving the page
window.addEventListener('beforeunload', () => {
    window.frontCamera.stopCamera();
    window.backCamera.stopCamera();
});
