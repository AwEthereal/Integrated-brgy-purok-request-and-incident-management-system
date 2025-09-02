@extends('layouts.app')

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
    .document-preview {
        @apply border border-gray-200 rounded-md p-2 mt-2;
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h1 class="text-lg leading-6 font-medium text-gray-900">Request Details</h1>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Review the request details before taking action</p>
        </div>

        <!-- Request Information -->
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <!-- Resident Information -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Resident Information</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="font-medium">{{ $request->user->name ?? 'N/A' }}</div>
                        <div class="text-gray-500">{{ $request->user->email ?? 'N/A' }}</div>
                        <div class="text-gray-500">{{ $request->user->contact_number ?? 'N/A' }}</div>
                    </dd>
                </div>

                <!-- Request Details -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Request Details</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 space-y-2">
                        <div>
                            <span class="font-medium">Document Type:</span>
                            <span class="ml-2">{{ ucwords(str_replace('_', ' ', $request->form_type)) }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Purok:</span>
                            <span class="ml-2">{{ $request->purok->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Purpose:</span>
                            <span class="ml-2">{{ $request->purpose }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Requested On:</span>
                            <span class="ml-2">{{ $request->created_at->format('F j, Y \a\t g:i A') }}</span>
                        </div>
                    </dd>
                </div>

                <!-- Purok Leader Notes -->
                @if($request->purok_notes)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-yellow-50">
                    <dt class="text-sm font-medium text-yellow-700">Purok Leader Notes</dt>
                    <dd class="mt-1 text-sm text-yellow-700 sm:mt-0 sm:col-span-2">
                        {{ $request->purok_notes }}
                    </dd>
                </div>
                @endif

                <!-- Attachments -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Attachments</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($request->valid_id_front_path || $request->valid_id_back_path)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if($request->valid_id_front_path)
                                <div class="border border-gray-200 rounded-md p-2">
                                    <div class="font-medium text-sm text-center text-gray-700 mb-1">Front of ID</div>
                                    <div class="h-40 overflow-hidden flex items-center justify-center bg-gray-50 rounded">
                                        <img src="{{ asset($request->valid_id_front_path) }}" 
                                             alt="Front of ID" 
                                             class="max-h-full max-w-full object-contain cursor-pointer hover:opacity-90 transition-opacity"
                                             onclick="previewImage(this.src, 'Front of ID')">
                                    </div>
                                </div>
                                @endif
                                @if($request->valid_id_back_path)
                                <div class="border border-gray-200 rounded-md p-2">
                                    <div class="font-medium text-sm text-center text-gray-700 mb-1">Back of ID</div>
                                    <div class="h-40 overflow-hidden flex items-center justify-center bg-gray-50 rounded">
                                        <img src="{{ asset($request->valid_id_back_path) }}" 
                                             alt="Back of ID" 
                                             class="max-h-full max-w-full object-contain cursor-pointer hover:opacity-90 transition-opacity"
                                             onclick="previewImage(this.src, 'Back of ID')">
                                    </div>
                                </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No attachments available</p>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Action Buttons -->
        <div class="px-4 py-4 bg-gray-50 text-right sm:px-6 space-x-3">
            <a href="{{ route('barangay.approvals.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back to List
            </a>
            
            @if($request->status === 'purok_approved')
                <button type="button" onclick="showRejectModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Reject
                </button>
                
                <form action="{{ route('barangay.approvals.approve', $request->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Approve
                    </button>
                </form>
            @else
                <!-- Show status badge for non-pending requests -->
                <span class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                    @if($request->status === 'rejected')
                        <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        Rejected
                    @elseif($request->status === 'barangay_approved')
                        <svg class="-ml-1 mr-2 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Approved
                    @endif
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideRejectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Reject Request
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Please provide a reason for rejecting this request. This will help the resident understand why their request was not approved.
                        </p>
                    </div>
                    <div class="mt-4">
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Enter reason for rejection..." required></textarea>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="button" onclick="submitRejectForm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                    Submit Rejection
                </button>
                <button type="button" onclick="hideRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function submitRejectForm() {
        const reason = document.getElementById('rejection_reason').value.trim();
        if (!reason) {
            alert('Please provide a reason for rejection');
            return;
        }
        
        const form = document.getElementById('rejectForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'rejection_reason';
        input.value = reason;
        form.appendChild(input);
        
        form.submit();
    }

    function previewImage(src, title) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="relative max-w-4xl w-full">
                <div class="bg-white p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-medium">${title}</h3>
                        <button onclick="this.closest('div[role=dialog]').remove()" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="max-h-[80vh] overflow-auto">
                        <img src="${src}" alt="${title}" class="max-w-full h-auto">
                    </div>
                </div>
            </div>
        `;
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        document.body.appendChild(modal);
        
        // Close modal when clicking on the overlay
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function closeOnEscape(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', closeOnEscape);
            }
        });
    }
</script>
@endpush
@endsection
