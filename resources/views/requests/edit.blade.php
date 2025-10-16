@extends('layouts.app')

@push('styles')
<style>
    .camera-container {
        position: relative;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .camera-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        text-align: center;
        color: #6b7280;
    }
    .camera {
        display: none;
        width: 100%;
    }
    .snapshot {
        display: none;
        width: 100%;
    }
    .id-preview {
        max-width: 100%;
        display: none;
    }
    .btn-camera, .btn-upload, .btn-capture, .btn-retake, .btn-switch-camera {
        margin: 0.25rem;
    }
    .camera-controls {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        width: 90%;
        max-width: 32rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .close {
        color: #6b7280;
        float: right;
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 1;
        cursor: pointer;
    }
    .close:hover {
        color: #374151;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Request Details</h1>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="requestForm" method="POST" action="{{ route('requests.update', $request->id) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Hidden inputs for photo data -->
                <input type="hidden" name="front_id_photo_data" id="front_id_photo_data">
                <input type="hidden" name="back_id_photo_data" id="back_id_photo_data">

                <!-- Request Details Card -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Request Information</h2>
                    <p class="text-sm text-gray-500 mb-4">
                        Note: Your personal information will be automatically pulled from your profile.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Form Type -->
                        <div>
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-600">*</span></label>
                            <select name="form_type" id="form_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                <option value="barangay_clearance" {{ old('form_type', $request->form_type) == 'barangay_clearance' ? 'selected' : '' }}>Barangay Clearance</option>

                                <option value="business_clearance" {{ old('form_type', $request->form_type) == 'business_clearance' ? 'selected' : '' }}>Business Clearance</option>
                                <option value="certificate_of_residency" {{ old('form_type', $request->form_type) == 'certificate_of_residency' ? 'selected' : '' }}>Certificate of Residency</option>
                                <option value="certificate_of_indigency" {{ old('form_type', $request->form_type) == 'certificate_of_indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                                <option value="other" {{ old('form_type', $request->form_type) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        
                        <!-- Purok (Display only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Purok</label>
                            <div class="p-2 bg-gray-50 rounded-md border border-gray-200">
                                {{ $request->purok->name ?? 'N/A' }}
                            </div>
                        </div>
                        
                        <!-- Purpose -->
                        <div class="md:col-span-2">
                            <div class="flex justify-between items-baseline">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-600">*</span></label>
                                <span id="purpose-counter" class="text-xs text-gray-500">{{ strlen(old('purpose', $request->purpose)) }}/50</span>
                            </div>
                            <input type="text" name="purpose" id="purpose" 
                                   value="{{ old('purpose', $request->purpose) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                   placeholder="e.g., Employment, Business, etc." required maxlength="50"
                                   oninput="document.getElementById('purpose-counter').textContent = this.value.length + '/50';">
                        </div>
                        
                        <!-- Remarks -->
                        <div class="md:col-span-2">
                            <div class="flex justify-between items-baseline">
                                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                                <span id="remarks-counter" class="text-xs text-gray-500">{{ strlen(old('remarks', $request->remarks)) }}/100</span>
                            </div>
                            <textarea name="remarks" id="remarks" rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                      placeholder="Any additional information or special requests"
                                      maxlength="100"
                                      oninput="document.getElementById('remarks-counter').textContent = this.value.length + '/100';">{{ old('remarks', $request->remarks) }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- User Information Notice -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 sm:p-4 rounded text-sm sm:text-base">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Your personal information will be automatically pulled from your profile. 
                                <a href="{{ route('profile.edit') }}" class="font-medium text-blue-700 underline hover:text-blue-600">Update your profile</a> 
                                if you need to make any changes to your personal details.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- ID Display Section -->
                <div class="bg-gray-50 p-4 sm:p-6 rounded-lg shadow-sm mt-4 sm:mt-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Valid ID Photos</h2>
                    <p class="text-sm text-gray-500 mb-4 sm:mb-6">
                        Your submitted ID photos
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Front ID -->
                        <div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700">Front of ID</label>
                            </div>
                            @if($request->valid_id_front_path)
                                <div class="border border-gray-200 rounded-md p-1 sm:p-2">
                                    <img src="{{ asset($request->valid_id_front_path) }}" alt="Front ID" class="w-full h-auto rounded object-contain max-h-48 sm:max-h-64">
                                </div>
                            @else
                                <div class="bg-gray-100 p-4 text-center text-gray-500 rounded-md">
                                    No front ID photo available
                                </div>
                            @endif
                        </div>
                        
                        <!-- Back ID -->
                        <div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700">Back of ID</label>
                            </div>
                            @if($request->valid_id_back_path)
                                <div class="border border-gray-200 rounded-md p-1 sm:p-2">
                                    <img src="{{ asset($request->valid_id_back_path) }}" alt="Back ID" class="w-full h-auto rounded object-contain max-h-48 sm:max-h-64">
                                </div>
                            @else
                                <div class="bg-gray-100 p-4 text-center text-gray-500 rounded-md">
                                    No back ID photo available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-6 sm:mt-8">
                    <a href="{{ route('requests.show', $request) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Format contact number input
    document.getElementById('contact_number')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.substring(0, 11);
        e.target.value = value;
    });
    
    // Format postal code input
    document.getElementById('postal_code')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.substring(0, 4);
        e.target.value = value;
    });
</script>
@endpush
@endsection
