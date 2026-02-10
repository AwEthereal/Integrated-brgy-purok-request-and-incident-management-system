@extends('layouts.app')

@section('title', 'Request Details')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Request Details #{{ $request->id }}</h1>
            @php
                $isPurokLeader = auth()->user()->role === 'purok_leader';
                $isBarangayOfficial = in_array(auth()->user()->role, ['barangay_kagawad', 'barangay_captain']);
                $backRoute = 'requests.index'; // Default for regular users

                if ($isPurokLeader) {
                    $backRoute = 'purok_leader.dashboard';
                } elseif ($isBarangayOfficial) {
                    $backRoute = 'dashboard'; // Barangay officials use regular dashboard
                } elseif (auth()->user()->role === 'admin') {
                    $backRoute = 'dashboard'; // Admin also uses regular dashboard
                }
            @endphp
            <a href="{{ route($backRoute) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to {{ $isPurokLeader ? 'Purok Dashboard' : 'Requests' }}
            </a>
        </div>

        <!-- Status Indicator -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Request Status</h2>
                @php
                    // Helper variables
                    $purokNotes = strtolower($request->purok_notes ?? '');
                    $barangayNotes = strtolower($request->barangay_notes ?? '');
                    $normalizedStatus = strtolower(trim($request->status));
                    $isPurokRejected = str_contains($purokNotes, 'rejected by purok');
                    $isBarangayRejected = str_contains($barangayNotes, 'rejected by barangay');

                    // Default status
                    $statusConfig = [
                        'text' => 'Pending',
                        'class' => 'bg-gray-100 text-gray-800',
                        'icon' => 'fa-clock'
                    ];

                    // Status mapping
                    $statusMap = [
                        'completed' => [
                            'text' => 'Completed',
                            'class' => 'bg-green-100 text-green-800',
                            'icon' => 'fa-check-circle'
                        ],
                        'barangay_approved' => [
                            'text' => 'Barangay Approved',
                            'class' => 'bg-blue-100 text-blue-800',
                            'icon' => 'fa-check-double'
                        ],
                        'purok_approved' => [
                            'text' => 'Purok Approved',
                            'class' => 'bg-yellow-100 text-yellow-800',
                            'icon' => 'fa-check'
                        ],
                        'rejected' => [
                            'text' => 'Rejected',
                            'class' => 'bg-red-100 text-red-800',
                            'icon' => 'fa-times-circle'
                        ]
                    ];

                    // Check for rejection in notes first (highest priority)
                    if ($isPurokRejected) {
                        $statusConfig = [
                            'text' => 'Rejected by Purok Leader',
                            'class' => 'bg-red-100 text-red-800',
                            'icon' => 'fa-times-circle'
                        ];
                    } elseif ($isBarangayRejected) {
                        $statusConfig = [
                            'text' => 'Rejected by Barangay Official',
                            'class' => 'bg-red-100 text-red-800',
                            'icon' => 'fa-times-circle'
                        ];
                    }
                    // Check status from the status map
                    elseif (isset($statusMap[$normalizedStatus])) {
                        $statusConfig = $statusMap[$normalizedStatus];
                    }

                    // Set variables for the view
                    $statusText = $statusConfig['text'];
                    $statusClass = $statusConfig['class'];
                    $statusIcon = $statusConfig['icon'];
                @endphp

                <div class="flex items-center">
                    <span class="px-3 py-1.5 rounded-md text-sm font-medium {{ $statusClass }} shadow-sm">
                        <i class="fas {{ $statusIcon }} mr-1"></i>
                        {{ $statusText }}
                    </span>
                </div>

            </div>

            <!-- Progress Steps -->
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div class="flex flex-col">
                        <span
                            class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                            @if($request->status === 'rejected')
                                Request Rejected
                            @elseif($request->status === 'completed')
                                Request Completed
                            @else
                                In Progress
                            @endif
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-blue-600">
                            @if($request->status === 'pending') 50% @else 100% @endif
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                    <div style="width:@if($request->status === 'pending') 50% @else 100% @endif" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center 
                                                                @if($request->status === 'rejected') bg-red-500
                                                                @else bg-blue-500 @endif">
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">

                    {{-- Step 1: Submitted --}}
                    <div class="text-center">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center 
                                @if(in_array($request->status, ['purok_approved', 'barangay_approved', 'completed', 'rejected'])) 
                                    bg-blue-600 text-white 
                                @else 
                                    bg-gray-200 
                                @endif">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-1">Submitted</div>
                        <div class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</div>
                    </div>

                    {{-- Step 2: Purok --}}
                    <div class="text-center">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center 
                                @if(in_array($request->status, ['purok_approved', 'barangay_approved', 'completed'])) 
                                    bg-blue-600 text-white 
                                @elseif($isPurokRejected || $request->status === 'rejected') 
                                    bg-red-500 text-white 
                                @else 
                                    bg-gray-200 
                                @endif">
                            @if($request->purok_approved_at)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <span class="text-xs">2</span>
                            @endif
                        </div>
                        <div class="mt-1">
                            {{ $isPurokRejected ? 'Purok Rejected' : 'Purok Approved' }}
                        </div>
                        @if($request->purok_approved_at)
                            <div class="text-xs text-gray-500">{{ $request->purok_approved_at->format('M d, Y') }}</div>
                            @if($request->purokApprover)
                                <div class="text-xs text-gray-500">by {{ $request->purokApprover->name }}</div>
                            @endif
                        @endif
                    </div>

                    {{-- Barangay step hidden per requirement --}}

                    {{-- Step 3: Barangay is now the final step --}}
                    @if($isPurokRejected || $isBarangayRejected)
                        <div class="text-center">
                            <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center bg-red-500 text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="mt-1">Rejected</div>
                            @if($request->purok_rejected_at)
                                <div class="text-xs text-gray-500">{{ $request->purok_rejected_at->format('M d, Y') }}</div>
                            @elseif($request->barangay_rejected_at)
                                <div class="text-xs text-gray-500">{{ $request->barangay_rejected_at->format('M d, Y') }}</div>
                            @endif
                        </div>
                    @endif

                </div>


                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Request Information</h2>
                    
                    <!-- Processing Fee Notice -->
                    @if($request->status === 'pending')
                    <div class="mb-4 p-4 bg-blue-50 rounded-md">
                        <p class="text-sm text-blue-700">
                            <span class="font-medium">Note to Resident:</span> To receive your purok clearance,
                            please prepare 20 pesos for the processing fee.
                        </p>
                    </div>
                    @endif

                    <!-- ID Photos Section -->
                    @if($request->valid_id_front_path || $request->valid_id_back_path)
                        <div class="mb-6">
                            <h3 class="text-md font-medium text-gray-700 mb-3">Submitted ID Photos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($request->valid_id_front_path)
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-2">Front of ID</p>
                                        <div class="cursor-pointer" onclick="openIdLightbox('{{ asset($request->valid_id_front_path) }}', 'Front of ID')">
                                            <img src="{{ asset($request->valid_id_front_path) }}" alt="Front of ID"
                                                class="w-full h-48 object-contain border rounded-md hover:opacity-90 transition-opacity">
                                        </div>
                                    </div>
                                @endif
                                @if($request->valid_id_back_path)
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-2">Back of ID</p>
                                        <div class="cursor-pointer" onclick="openIdLightbox('{{ asset($request->valid_id_back_path) }}', 'Back of ID')">
                                            <img src="{{ asset($request->valid_id_back_path) }}" alt="Back of ID"
                                                class="w-full h-48 object-contain border rounded-md hover:opacity-90 transition-opacity">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Resident</p>
                            <div class="flex items-center space-x-2">
                                <p class="font-medium">{{ $request->user->name ?? 'N/A' }}</p>
                                @if($request->user)
                                    @if(auth()->user()->role === 'purok_leader')
                                        <a href="{{ route('purok_leader.residents.show', $request->user->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline flex items-center text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            View Profile
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Form Type</p>
                            <p class="font-medium">{{ format_label($request->form_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Purok</p>
                            <p class="font-medium">{{ $request->purok->name ?? 'N/A' }}</p>
                        </div>
                        @if($request->remarks)
                        <div>
                            <p class="text-sm text-gray-500">Additional Notes</p>
                            <p class="font-medium text-blue-600 break-words">{{ $request->remarks }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Purpose</p>
                            <p class="font-medium">{{ $request->purpose }}</p>
                        </div>
                        {{-- Rejected by Purok Leader --}}
                        @if($request->status === 'rejected' && $request->purok_notes)
                            <div class="md:col-span-2 bg-white p-4 rounded-md border border-gray-200">
                                <p class="text-lg font-semibold text-gray-700 mb-2">Message from Purok Leader</p>
                                <p class="text-sm font-medium text-red-600 mb-2">
                                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Rejection Notice
                                </p>
                                <p class="text-gray-700 mb-3">
                                    @php
                                        $notes = str_replace('Rejected by Purok Leader: ', '', $request->purok_notes);
                                        $notes = str_replace('Rejected by Purok: ', '', $notes);
                                    @endphp
                                    <span class="font-medium">Reason for Dismissal:</span> {{ $notes }}
                                </p>
                                <p class="text-xs text-gray-500 border-t pt-2 mt-2">
                                    Rejected on:
                                    {{ $request->rejected_at ? $request->rejected_at->format('M d, Y h:i A') : $request->updated_at->format('M d, Y h:i A') }}
                                    @if($request->rejectedBy)
                                        by {{ $request->rejectedBy->name }}
                                    @elseif($request->purokApprover)
                                        by {{ $request->purokApprover->name }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        {{-- Approved by Purok Leader --}}
                        @if($request->status === 'purok_approved' || $request->status === 'barangay_approved' || $request->status === 'completed')
                            <div class="md:col-span-2 bg-white p-4 rounded-md border border-gray-200">
                                <p class="text-lg font-semibold text-gray-700 mb-2">Message from Purok Leader</p>
                                <p class="text-sm font-medium text-green-600 mb-2">
                                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Approval Notice
                                </p>
                                @if($request->purok_notes)
                                    <p class="text-gray-700 mb-3">
                                        <span class="font-medium">Approval Note:</span> {{ $request->purok_notes }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 border-t pt-2 mt-2">
                                    Approved on:
                                    {{ $request->purok_approved_at ? $request->purok_approved_at->format('M d, Y h:i A') : $request->updated_at->format('M d, Y h:i A') }}
                                    @if($request->purokApprover)
                                        by {{ $request->purokApprover->name }}
                                    @else
                                        by Purok Leader
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if($request->barangay_notes && !($request->status === 'rejected' && $request->purok_notes))
                            <p
                                class="text-sm font-medium {{ $request->status === 'rejected' ? 'text-red-800' : 'text-gray-500' }}">
                                {{ $request->status === 'rejected' ? 'Rejection Reason' : 'Barangay Notes' }}
                            </p>
                            <p class="mt-1 {{ $request->status === 'rejected' ? 'text-red-700' : 'text-gray-700' }}">
                                {{ $request->barangay_notes }}
                            </p>
                            @if($request->rejected_at && !$request->purok_notes)
                                <p class="text-xs text-gray-500 mt-1">
                                    Rejected on: {{ $request->rejected_at->format('M d, Y h:i A') }}
                                    @if($request->rejectedBy)
                                        by {{ $request->rejectedBy->name }}
                                    @endif
                                </p>
                            @endif
                        @endif

                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-wrap gap-3">
                        @php
                            $role = auth()->user()->role;
                            $isPurokLeader = ($role === 'purok_leader');
                            $isAdmin = $role === 'admin';
                            $isSecretary = $role === 'secretary';
                            $isBarangayDecisionMaker = in_array($role, ['barangay_captain', 'barangay_kagawad']);
                            $isBarangayOfficial = $isBarangayDecisionMaker || $isSecretary || $role === 'sk_chairman';
                            $isPurokLeaderForThisRequest = $isPurokLeader &&
                                ($request->purok_id == auth()->user()->purok_id || $isAdmin);
                        @endphp

                        @if($request->status === 'pending' && ($isPurokLeaderForThisRequest || $isBarangayOfficial || $isAdmin))
                            @if($isPurokLeaderForThisRequest || $isBarangayOfficial || $isAdmin)
                                <button onclick="openApproveModal({{ $request->id }})"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Approve Purok Clearance
                                </button>
                                <button onclick="openRejectModal({{ $request->id }})"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Reject Request
                                </button>
                            @endif
                        @elseif($request->status === 'purok_approved' && ($isBarangayDecisionMaker || $isAdmin))
                            @if($isBarangayDecisionMaker || $isAdmin)
                                <button onclick="openApproveModal({{ $request->id }})"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Approve Request
                                </button>
                                <button onclick="openRejectModal({{ $request->id }})"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Reject Request
                                </button>
                            @endif
                        @elseif($request->status === 'barangay_approved' && ($isBarangayOfficial || $isAdmin))
                            <form action="{{ route('requests.complete', $request) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if(($request->status === 'pending' || $request->status === 'rejected') && auth()->user()->id === $request->user_id)
                            @if($request->status === 'pending')
                                <a href="{{ route('requests.edit', $request) }}"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                    Edit Request
                                </a>
                            @endif
                            <form action="{{ route('requests.destroy', $request) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                                    Delete Request
                                </button>
                            </form>
                        @endif

                        @if($request->status === 'completed' && $request->document_path)
                            <a href="{{ asset('storage/' . $request->document_path) }}" target="_blank"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Download Document
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <div id="approveModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form id="approveForm" method="POST" action="">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    {{ $request->status === 'pending' ? 'Approve Purok Clearance' : 'Approve Barangay Clearance' }}
                                </h3>

                                <div class="mt-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">
                                        Message to Resident (Optional)
                                        <span class="text-gray-500 text-xs">
                                            - Any special instructions or notes
                                        </span>
                                    </label>
                                    <textarea name="{{ $request->status === 'pending' ? 'purok_notes' : 'barangay_notes' }}"
                                        id="notes" rows="3"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Example: Please pick up your clearance between 8:00 AM - 5:00 PM on weekdays..."></textarea>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Confirm Approval
                                </button>
                                <button type="button" onclick="closeModal('approveModal')"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Purok Leader Private Notes (Only visible to purok leaders) -->
            @if(in_array(auth()->user()->role, ['purok_leader', 'admin']))
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold text-gray-700">Private Notes</h2>
                        <button type="button" id="editPrivateNotesBtn" class="text-sm text-blue-600 hover:text-blue-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div id="privateNotesView" class="bg-gray-50 p-4 rounded-md border-l-4 border-gray-300">
                        <p id="privateNotesContent" class="text-sm text-gray-700 whitespace-pre-line">
                            @if($request->purok_private_notes)
                                {{ $request->purok_private_notes }}
                            @else
                                <span class="text-gray-400">No private notes added yet. Click the edit button to add notes.</span>
                            @endif
                        </p>
                    </div>
                    <div id="privateNotesEdit" class="hidden">
                        <form id="privateNotesForm" onsubmit="return false;">
                            @csrf
                            @method('PUT')
                            <textarea name="purok_private_notes" id="purok_private_notes" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $request->purok_private_notes }}</textarea>
                            <div class="mt-2 flex justify-end space-x-2">
                                <button type="button" id="cancelEditBtn"
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <button type="button" id="saveNotesBtn"
                                    class="px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <span id="saveButtonText">Save Notes</span>
                                    <span id="saveButtonSpinner" class="hidden">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Saving...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Reject Modal -->
            <div id="rejectModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form id="rejectForm" method="POST" action="{{ route('requests.reject', $request) }}">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Reject Request
                                </h3>
                                <div class="mt-2">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Reason for
                                        Rejection <span class="text-red-500">*</span></label>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required></textarea>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Confirm Rejection
                                </button>
                                <button type="button" onclick="closeModal('rejectModal')"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Feedback Section - Only shown to residents for approved/completed requests -->
            @if(in_array($request->status, ['barangay_approved', 'completed']) && 
                auth()->user()->role === 'resident' && 
                $request->user_id === auth()->id())
                @php
                    // Check if feedback already exists for this request
                    $hasFeedback = \App\Models\Feedback::where('request_id', $request->id)
                        ->where('user_id', auth()->id())
                        ->exists();
                @endphp

                <x-feedback-form type="request" :itemId="$request->id" :hasFeedback="$hasFeedback" />
            @endif
            
            <!-- ID Photo Lightbox Modal -->
            <div id="idLightbox" class="fixed inset-0 bg-black bg-opacity-95 z-50 hidden flex items-center justify-center p-4">
                <div class="relative w-full h-full flex items-center justify-center">
                    <!-- Close Button -->
                    <button onclick="closeIdLightbox()" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 hover:bg-opacity-70 rounded-full p-3 transition-all z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- Photo Container -->
                    <div class="relative max-w-7xl max-h-full">
                        <div class="bg-white rounded-t-lg px-6 py-3">
                            <h3 id="idLightboxTitle" class="text-lg font-semibold text-gray-800"></h3>
                        </div>
                        <div class="bg-white p-4">
                            <img id="idLightboxImage" src="" alt="ID Photo" class="max-w-full max-h-[80vh] object-contain mx-auto">
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
                <script>
                    // Private Notes Functions
                    function editPrivateNotes() {
                        const view = document.getElementById('privateNotesView');
                        const edit = document.getElementById('privateNotesEdit');
                        if (view && edit) {
                            view.classList.add('hidden');
                            edit.classList.remove('hidden');
                            // Focus the textarea when editing
                            document.getElementById('purok_private_notes').focus();
                        }
                    }

                    function cancelEditPrivateNotes() {
                        const view = document.getElementById('privateNotesView');
                        const edit = document.getElementById('privateNotesEdit');
                        if (view && edit) {
                            view.classList.remove('hidden');
                            edit.classList.add('hidden');
                        }
                    }

                    // Initialize when DOM is fully loaded
                    document.addEventListener('DOMContentLoaded', function() {
                        // Edit button
                        const editBtn = document.getElementById('editPrivateNotesBtn');
                        if (editBtn) {
                            editBtn.addEventListener('click', editPrivateNotes);
                        }
                        
                        // Cancel button
                        const cancelBtn = document.getElementById('cancelEditBtn');
                        if (cancelBtn) {
                            cancelBtn.addEventListener('click', cancelEditPrivateNotes);
                        }

                        // Save button
                        const saveBtn = document.getElementById('saveNotesBtn');
                        if (saveBtn) {
                            saveBtn.addEventListener('click', function() {
                                const form = document.getElementById('privateNotesForm');
                                const notesValue = document.getElementById('purok_private_notes').value;
                                const submitButton = document.getElementById('saveNotesBtn');
                                const saveButtonText = document.getElementById('saveButtonText');
                                const saveButtonSpinner = document.getElementById('saveButtonSpinner');
                                const notesContent = document.getElementById('privateNotesContent');
                                const notesEdit = document.getElementById('privateNotesEdit');
                                const notesView = document.getElementById('privateNotesView');

                                // Show loading state
                                saveButtonText.classList.add('hidden');
                                saveButtonSpinner.classList.remove('hidden');
                                submitButton.disabled = true;

                                // Send the request
                                fetch('{{ route('requests.update-private-notes', $request->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `purok_private_notes=${encodeURIComponent(notesValue)}&_method=PUT`
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        // Update the displayed notes
                                        const displayText = data.notes && data.notes.trim() !== '' ?
                                            data.notes : 'No private notes added yet. Click the edit button to add notes.';

                                        notesContent.innerHTML = displayText.replace(/\n/g, '<br>');

                                        // Hide the edit form and show the view
                                        notesEdit.classList.add('hidden');
                                        notesView.classList.remove('hidden');

                                        // Show success message
                                        const alertDiv = document.createElement('div');
                                        alertDiv.className = 'mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded';
                                        alertDiv.textContent = 'Notes saved successfully.';
                                        
                                        // Insert alert before the form
                                        const container = form.closest('.bg-white.rounded-lg.shadow-md');
                                        if (container) {
                                            container.insertBefore(alertDiv, container.firstChild);
                                            
                                            // Remove the alert after 3 seconds
                                            setTimeout(() => {
                                                alertDiv.remove();
                                            }, 3000);
                                        }
                                    } else {
                                        throw new Error(data.message || 'Failed to save notes');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    
                                    // Show error message
                                    const alertDiv = document.createElement('div');
                                    alertDiv.className = 'mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';
                                    alertDiv.textContent = 'Failed to save notes: ' + (error.message || 'Unknown error occurred');
                                    
                                    // Insert alert before the form
                                    const container = form.closest('.bg-white.rounded-lg.shadow-md');
                                    if (container) {
                                        container.insertBefore(alertDiv, container.firstChild);
                                        
                                        // Remove the alert after 5 seconds
                                        setTimeout(() => {
                                            alertDiv.remove();
                                        }, 5000);
                                    }
                                })
                                .finally(() => {
                                    // Reset button state
                                    if (saveButtonText) saveButtonText.classList.remove('hidden');
                                    if (saveButtonSpinner) saveButtonSpinner.classList.add('hidden');
                                    if (submitButton) submitButton.disabled = false;
                                });
                            });
                        }
                    });

                    // Purok Approval/Rejection
                    function openModal(modalId) {
                        document.getElementById(modalId).classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }

                    function closeModal(modalId) {
                        document.getElementById(modalId).classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }

                    // Close modal when clicking outside of it
                    window.onclick = function(event) {
                        if (event.target.classList.contains('fixed')) {
                            event.target.classList.add('hidden');
                            document.body.style.overflow = 'auto';
                        }
                    }

                    function openApproveModal(requestId) {
                        const form = document.getElementById('approveForm');
                        const status = '{{ $request->status }}';
                        const route = status === 'pending'
                            ? `/requests/${requestId}/approve-purok`
                            : `/requests/${requestId}/approve-barangay`;
                        form.action = route;
                        document.getElementById('approveModal').classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    }

                    function openRejectModal(requestId) {
                        const form = document.getElementById('rejectForm');
                        form.action = `/requests/${requestId}/reject`;
                        document.getElementById('rejectModal').classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    }

                    function closeModal(modalId) {
                        document.getElementById(modalId).classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }

                    // Close modal when clicking outside
                    window.onclick = function (event) {
                        if (event.target.classList.contains('bg-gray-500')) {
                            document.querySelectorAll('.fixed.inset-0').forEach(modal => {
                                modal.classList.add('hidden');
                                document.body.classList.remove('overflow-hidden');
                            });
                        }
                    }
                    
                    // ID Photo Lightbox Functions
                    function openIdLightbox(imageSrc, title) {
                        const lightbox = document.getElementById('idLightbox');
                        const lightboxImage = document.getElementById('idLightboxImage');
                        const lightboxTitle = document.getElementById('idLightboxTitle');
                        
                        lightboxImage.src = imageSrc;
                        lightboxTitle.textContent = title;
                        lightbox.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                    
                    function closeIdLightbox() {
                        const lightbox = document.getElementById('idLightbox');
                        lightbox.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                    
                    // Close lightbox on Escape key
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            const lightbox = document.getElementById('idLightbox');
                            if (lightbox && !lightbox.classList.contains('hidden')) {
                                closeIdLightbox();
                            }
                        }
                    });
                    
                    // Close lightbox when clicking outside the image
                    document.getElementById('idLightbox')?.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeIdLightbox();
                        }
                    });
                </script>
            @endpush
@endsection