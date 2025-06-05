@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Request Details #{{ $request->id }}</h1>
        <a href="{{ route('requests.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Requests
        </a>
    </div>

    <!-- Status Indicator -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-700">Request Status</h2>
            <span class="px-3 py-1 rounded-full text-sm font-medium 
                @if($request->status === 'completed') bg-green-100 text-green-800
                @elseif($request->status === 'rejected') b<!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('requests.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Submit Request
                    </button>
                </div>g-red-100 text-red-800
                @elseif($request->status === 'barangay_approved') bg-blue-100 text-blue-800
                @elseif($request->status === 'purok_approved') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ format_label($request->status) }}
            </span>
        </div>

        <!-- Progress Steps -->
        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                        @if($request->status === 'rejected') Request Rejected
                        @elseif(in_array($request->status, ['completed', 'barangay_approved', 'purok_approved'])) Request Completed
                        @else In Progress @endif
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold inline-block text-blue-600">
                        @if($request->status === 'pending') 25%
                        @elseif($request->status === 'purok_approved') 50%
                        @elseif($request->status === 'barangay_approved') 75%
                        @else 100% @endif
                    </span>
                </div>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                <div style="width:@if($request->status === 'pending') 25%
                    @elseif($request->status === 'purok_approved') 50%
                    @elseif($request->status === 'barangay_approved') 75%
                    @else 100% @endif" 
                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center 
                    @if($request->status === 'rejected') bg-red-500
                    @elseif($request->status === 'completed') bg-green-500
                    @else bg-blue-500 @endif">
                </div>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center 
                        @if(in_array($request->status, ['purok_approved', 'barangay_approved', 'completed', 'rejected'])) 
                            bg-blue-600 text-white @else bg-gray-200 @endif">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="mt-1">Submitted</div>
                    <div class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</div>
                </div>
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center 
                        @if(in_array($request->status, ['purok_approved', 'barangay_approved', 'completed', 'rejected'])) 
                            bg-blue-600 text-white @else bg-gray-200 @endif">
                        @if($request->purok_approved_at)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <span class="text-xs">2</span>
                        @endif
                    </div>
                    <div class="mt-1">Purok Approved</div>
                    @if($request->purok_approved_at)
                        <div class="text-xs text-gray-500">{{ $request->purok_approved_at->format('M d, Y') }}</div>
                        @if($request->purokApprover)
                            <div class="text-xs text-gray-500">by {{ $request->purokApprover->name }}</div>
                        @endif
                    @endif
                </div>
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center 
                        @if(in_array($request->status, ['barangay_approved', 'completed'])) 
                            bg-blue-600 text-white 
                        @elseif($request->status === 'rejected') bg-red-500 text-white
                        @else bg-gray-200 @endif">
                        @if($request->barangay_approved_at)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <span class="text-xs">3</span>
                        @endif
                    </div>
                    <div class="mt-1">Barangay Approved</div>
                    @if($request->barangay_approved_at)
                        <div class="text-xs text-gray-500">{{ $request->barangay_approved_at->format('M d, Y') }}</div>
                        @if($request->barangayApprover)
                            <div class="text-xs text-gray-500">by {{ $request->barangayApprover->name }}</div>
                        @endif
                    @endif
                </div>
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center 
                        @if($request->status === 'completed') 
                            bg-green-500 text-white 
                        @elseif($request->status === 'rejected') bg-red-500 text-white
                        @else bg-gray-200 @endif">
                        @if($request->status === 'completed')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <span class="text-xs">4</span>
                        @endif
                    </div>
                    <div class="mt-1">Completed</div>
                    @if($request->document_generated_at)
                        <div class="text-xs text-gray-500">{{ $request->document_generated_at->format('M d, Y') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Request Information</h2>
        
        <!-- ID Photos Section -->
        @if($request->valid_id_front_path || $request->valid_id_back_path)
        <div class="mb-6">
            <h3 class="text-md font-medium text-gray-700 mb-3">Submitted ID Photos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($request->valid_id_front_path)
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Front of ID</p>
                    <a href="{{ asset($request->valid_id_front_path) }}" target="_blank" class="block">
                        <img src="{{ asset($request->valid_id_front_path) }}" alt="Front of ID" class="w-full h-48 object-contain border rounded-md">
                    </a>
                </div>
                @endif
                @if($request->valid_id_back_path)
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Back of ID</p>
                    <a href="{{ asset($request->valid_id_back_path) }}" target="_blank" class="block">
                        <img src="{{ asset($request->valid_id_back_path) }}" alt="Back of ID" class="w-full h-48 object-contain border rounded-md">
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Form Type</p>
                <p class="font-medium">{{ $request->form_type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Purok</p>
                <p class="font-medium">{{ $request->purok->name ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Purpose</p>
                <p class="font-medium">{{ $request->purpose }}</p>
            </div>
            @if($request->remarks)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Remarks</p>
                    <p class="font-medium">{{ $request->remarks }}</p>
                </div>
            @endif
            @if($request->purok_notes)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Purok Notes</p>
                    <p class="font-medium">{{ $request->purok_notes }}</p>
                </div>
            @endif
            @if($request->barangay_notes && $request->status !== 'rejected')
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Barangay Notes</p>
                    <p class="font-medium">{{ $request->barangay_notes }}</p>
                </div>
            @endif
            @if($request->status === 'rejected')
                <div class="md:col-span-2 bg-red-50 p-4 rounded-md">
                    <p class="text-sm font-medium text-red-800">Rejection Reason</p>
                    <p class="mt-1 text-red-700">{{ $request->barangay_notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-wrap gap-3">
            @if($request->status === 'pending' && (auth()->user()->role === 'purok_leader' || auth()->user()->role === 'admin'))
                <button onclick="openApproveModal()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Approve Purok Clearance
                </button>
                <button onclick="openRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Reject Request
                </button>
            @elseif($request->status === 'purok_approved' && (auth()->user()->role === 'barangay_official' || auth()->user()->role === 'admin'))
                <button onclick="openApproveModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Approve Barangay Clearance
                </button>
                <button onclick="openRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Reject Request
                </button>
            @elseif($request->status === 'barangay_approved' && (auth()->user()->role === 'barangay_official' || auth()->user()->role === 'admin'))
                <form action="{{ route('requests.complete', $request) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Mark as Completed
                    </button>
                </form>
            @endif

            @if($request->status === 'pending' && auth()->user()->id === $request->user_id)
                <a href="{{ route('requests.edit', $request) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                    Edit Request
                </a>
            @endif

            @if($request->status === 'completed' && $request->document_path)
                <a href="{{ asset('storage/' . $request->document_path) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Download Document
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        {{ $request->status === 'pending' ? 'Approve Purok Clearance' : 'Approve Barangay Clearance' }}
                    </h3>
                    <div class="mt-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="{{ $request->status === 'pending' ? 'purok_notes' : 'barangay_notes' }}" id="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Approval
                    </button>
                    <button type="button" onclick="closeModal('approveModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST" action="{{ route('requests.reject', $request) }}">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Reject Request</h3>
                    <div class="mt-2">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Reason for Rejection <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Rejection
                    </button>
                    <button type="button" onclick="closeModal('rejectModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Feedback Section -->
@if($request->status === 'completed')
    @php
        // Check if feedback already exists for this request
        $hasFeedback = \App\Models\Feedback::where('request_id', $request->id)
            ->where('user_id', auth()->id())
            ->exists();
    @endphp
    
    <x-feedback-form 
        type="request" 
        :itemId="$request->id"
        :hasFeedback="$hasFeedback"
    />
@endif

@push('scripts')
<script>
    function openApproveModal() {
        const form = document.getElementById('approveForm');
        form.action = '{{ $request->status === "pending" ? route("requests.approve-purok", $request) : route("requests.approve-barangay", $request) }}';
        document.getElementById('approveModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('bg-gray-500')) {
            document.querySelectorAll('.fixed.inset-0').forEach(modal => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }
    }
</script>
@endpush

@endsection
