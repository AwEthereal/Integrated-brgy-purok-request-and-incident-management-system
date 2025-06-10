@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">New Request</h1>
            <a href="{{ route('requests.index') }}" class="text-blue-600 hover:text-blue-800 text-sm sm:text-base flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Requests
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p class="font-bold">Please fix the following errors:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="requestForm" method="POST" action="{{ route('requests.store') }}" class="bg-white shadow-md rounded-lg p-4 sm:p-6" enctype="multipart/form-data">
            @csrf
            
            <!-- Hidden fields for ID photo data -->
            <input type="hidden" name="front_id_photo_data" id="front_id_photo_data">
            <input type="hidden" name="back_id_photo_data" id="back_id_photo_data">

            <!-- Personal Information Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 pb-2 border-b">Personal Information</h2>
                
                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-3">Your Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Full Name</p>
                            <p class="text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->middle_name }} {{ auth()->user()->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Contact Number</p>
                            <p class="text-gray-900">{{ auth()->user()->contact_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date of Birth</p>
                            <p class="text-gray-900">{{ auth()->user()->birth_date ? auth()->user()->birth_date->format('F d, Y') : 'Not set' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Address</p>
                            <p class="text-gray-900">
                                {{ auth()->user()->purok ? auth()->user()->purok->name : 'No purok assigned' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Update your profile information
                        </a>
                    </div>
                </div>

            <!-- Valid ID Upload Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 pb-2 border-b">Valid ID Verification</h2>
                <p class="text-sm text-gray-600 mb-6">Please upload clear photos of both sides of your valid government-issued ID (e.g., Voters ID, Driver's License, UMID, etc.)</p>
                
                <!-- Front ID Photo -->
                <div class="mb-8">
                    <h3 class="text-md font-medium text-gray-700 mb-3">Front of ID <span class="text-red-500">*</span></h3>
                    <div id="front_camera_container" class="relative w-full max-w-md mx-auto aspect-[3/2] rounded-md border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center mb-3 sm:mb-4">
                        <video id="front_camera" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                        <canvas id="front_snapshot" class="hidden"></canvas>
                        <img id="front_id_preview" class="hidden w-full h-full object-contain bg-white" alt="Front ID Preview" />
                        <div id="front_camera_placeholder" class="text-center p-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">No front ID photo selected</p>
                        </div>
                    </div>
                    
                    <!-- Camera Controls -->
                    <div class="flex flex-wrap gap-2 justify-center mb-4 sm:mb-6">
                        <input type="file" name="front_valid_id" id="front_valid_id" accept="image/*" class="hidden" data-side="front" />
                        <button type="button" id="front_upload_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span class="truncate">Upload</span>
                        </button>
                        <button type="button" id="front_camera_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">Take Photo</span>
                        </button>
                        <button type="button" id="front_switch_camera" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="truncate">Switch</span>
                        </button>
                        <button type="button" id="front_capture_btn" class="flex-1 sm:flex-none bg-indigo-600 py-2 px-3 sm:px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            </svg>
                            <span class="truncate">Capture</span>
                        </button>
                        <button type="button" id="front_retake_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Retake
                        </button>
                    </div>
                </div>

                <!-- Back ID Photo -->
                <div class="mb-8">
                    <h3 class="text-md font-medium text-gray-700 mb-3">Back of ID <span class="text-red-500">*</span></h3>
                    <div id="back_camera_container" class="relative w-full max-w-md mx-auto aspect-[3/2] rounded-md border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center mb-3 sm:mb-4">
                        <video id="back_camera" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                        <canvas id="back_snapshot" class="hidden"></canvas>
                        <img id="back_id_preview" class="hidden w-full h-full object-contain bg-white" alt="Back ID Preview" />
                        <div id="back_camera_placeholder" class="text-center p-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">No back ID photo selected</p>
                        </div>
                    </div>
                    
                    <!-- Camera Controls -->
                    <div class="flex flex-wrap gap-2 justify-center mb-4 sm:mb-6">
                        <input type="file" name="back_valid_id" id="back_valid_id" accept="image/*" class="hidden" data-side="back" />
                        <button type="button" id="back_upload_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span class="truncate">Upload</span>
                        </button>
                        <button type="button" id="back_camera_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">Take Photo</span>
                        </button>
                        <button type="button" id="back_switch_camera" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="truncate">Switch</span>
                        </button>
                        <button type="button" id="back_capture_btn" class="flex-1 sm:flex-none bg-indigo-600 py-2 px-3 sm:px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            </svg>
                            <span class="truncate">Capture</span>
                        </button>
                        <button type="button" id="back_retake_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden items-center justify-center">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="truncate">Retake</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Request Details Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 pb-2 border-b">Request Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="form_type" class="block text-sm font-medium text-gray-700">Type of Request <span class="text-red-500">*</span></label>
                        <select name="form_type" id="form_type" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select request type</option>
                            <option value="barangay_clearance" {{ old('form_type') == 'barangay_clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                            <option value="business_clearance" {{ old('form_type') == 'business_clearance' ? 'selected' : '' }}>Business Clearance</option>
                            <option value="certificate_of_residency" {{ old('form_type') == 'certificate_of_residency' ? 'selected' : '' }}>Certificate of Residency</option>
                            <option value="certificate_of_indigency" {{ old('form_type') == 'certificate_of_indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                            <option value="other" {{ old('form_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="purpose" id="purpose" value="{{ old('purpose') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., Employment, Government Transaction, Bank Requirement, etc.">
                    </div>

                    <div class="md:col-span-2">
                        <label for="remarks" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Any additional information or special requests">{{ old('remarks') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-6 sm:mt-8">
                <a href="{{ route('requests.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Submit Request
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <!-- Auto-format phone number -->
        <script>
            document.getElementById('contact_number')?.addEventListener('input', function (e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        </script>
        
        <script>
            // Debug function to check form state
            function debugFormState() {
                console.log('=== FORM STATE DEBUG ===');
                
                // Check form attributes
                const form = document.getElementById('requestForm');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                console.log('Form enctype:', form.enctype);
                
                // Check hidden inputs
                const frontInput = document.querySelector('input[name="front_id_photo_data"]');
                const backInput = document.querySelector('input[name="back_id_photo_data"]');
                
                console.log('Front photo data exists:', !!frontInput);
                console.log('Back photo data exists:', !!backInput);
                
                if (frontInput) {
                    console.log('Front photo data length:', frontInput.value.length);
                    console.log('Front photo data starts with:', frontInput.value.substring(0, 30) + '...');
                }
                
                if (backInput) {
                    console.log('Back photo data length:', backInput.value.length);
                    console.log('Back photo data starts with:', backInput.value.substring(0, 30) + '...');
                }
                
                // Check if form is valid
                if (form.checkValidity()) {
                    console.log('Form is valid and ready to submit');
                } else {
                    console.log('Form is not valid');
                    // Force show validation messages
                    form.reportValidity();
                }
            }
            
            // Run debug when DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM fully loaded, setting up form...');
                
                const form = document.getElementById('requestForm');
                if (!form) {
                    console.error('Form element not found!');
                    return;
                }
                
                // Log form attributes for debugging
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                console.log('Form enctype:', form.enctype);
                
                // Ensure form has the correct enctype
                form.setAttribute('enctype', 'multipart/form-data');
                
                // Prevent form from submitting if photos are missing
                form.addEventListener('submit', function(e) {
                    console.log('Form submission intercepted');
                    
                    // Get the hidden inputs
                    const frontPhoto = document.getElementById('front_id_photo_data');
                    const backPhoto = document.getElementById('back_id_photo_data');
                    
                    // Debug: Log the current state
                    console.log('Front photo data exists:', !!frontPhoto);
                    console.log('Back photo data exists:', !!backPhoto);
                    
                    if (frontPhoto) {
                        console.log('Front photo data length:', frontPhoto.value.length);
                    }
                    
                    if (backPhoto) {
                        console.log('Back photo data length:', backPhoto?.value?.length || 0);
                    }
                    
                    // Check front photo
                    if (!frontPhoto || !frontPhoto.value) {
                        console.error('Front photo is missing');
                        e.preventDefault();
                        alert('Please take or upload a photo of the front of your ID');
                        return false;
                    }
                    
                    // Check back photo
                    if (!backPhoto || !backPhoto.value) {
                        console.error('Back photo is missing');
                        
                        // Try to recover from camera handler
                        if (window.backCamera?.idPhotoData?.value) {
                            console.log('Attempting to recover back photo data from camera handler...');
                            
                            // Update the hidden input
                            if (!backPhoto) {
                                const newInput = document.createElement('input');
                                newInput.type = 'hidden';
                                newInput.id = 'back_id_photo_data';
                                newInput.name = 'back_id_photo_data';
                                form.appendChild(newInput);
                                backPhoto = newInput;
                            }
                            
                            backPhoto.value = window.backCamera.idPhotoData.value;
                            console.log('Updated back photo data from camera handler');
                            
                            // Verify the value was set
                            if (backPhoto.value) {
                                console.log('Back photo data recovered successfully');
                                return true; // Allow form submission to proceed
                            }
                        }
                        
                        e.preventDefault();
                        alert('Please take or upload a photo of the back of your ID');
                        return false;
                    }
                    
                    console.log('=== FORM SUBMISSION PROCEEDING ===');
                    return true;
                });
                
                console.log('Form submission handler attached');
            });
            
            // Expose debug function to window
            window.debugFormState = debugFormState;
        </script>
        
        <!-- Include the camera handler script -->
        <script src="{{ asset('js/camera-handler-new.js') }}"></script>
        
        <script>
            // Debug: Log when the camera handler initializes
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded, initializing camera handlers...');
            });
        </script>
    @endpush
@endsection