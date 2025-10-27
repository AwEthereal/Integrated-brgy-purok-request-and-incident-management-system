@extends('layouts.app')

@section('title', 'Purok Change Requests')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-8 px-4 sm:px-6 lg:px-8 rounded-lg shadow-lg mb-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Purok Join Requests
                    </h1>
                    <p class="text-purple-100 mt-2">Manage join requests for <span class="font-semibold">{{ $purokName }}</span></p>
                </div>
                <div class="grid grid-cols-3 gap-4 w-full md:w-auto">
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $pendingCount }}</p>
                        <p class="text-xs">Pending</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $approvedCount }}</p>
                        <p class="text-xs">Approved</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold">{{ $rejectedCount }}</p>
                        <p class="text-xs">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('purok_leader.dashboard') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-5">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Join Requests
            </h2>
        </div>
        
        <!-- Status Tabs -->
        <div class="border-b border-gray-200 bg-gray-50">
            <nav class="flex -mb-px">
                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => 1]) }}" 
                   class="px-6 py-4 border-b-2 font-semibold text-sm transition-colors {{ $currentStatus === 'pending' ? 'border-yellow-500 text-yellow-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending
                        @if($pendingCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved', 'page' => 1]) }}" 
                   class="px-6 py-4 border-b-2 font-semibold text-sm transition-colors {{ $currentStatus === 'approved' ? 'border-green-500 text-green-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approved
                        @if($approvedCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                {{ $approvedCount }}
                            </span>
                        @endif
                    </span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected', 'page' => 1]) }}" 
                   class="px-6 py-4 border-b-2 font-semibold text-sm transition-colors {{ $currentStatus === 'rejected' ? 'border-red-500 text-red-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Rejected
                        @if($rejectedCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                {{ $rejectedCount }}
                            </span>
                        @endif
                    </span>
                </a>
            </nav>
        </div>

        @if($changeRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Purok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Purok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested On</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @if(in_array($currentStatus, ['approved', 'rejected']))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed On</th>
                                @if($currentStatus === 'rejected')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                @endif
                            @endif
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($changeRequests as $request)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                            <span class="text-white font-semibold text-sm">{{ substr($request->user->first_name ?? $request->user->name, 0, 1) }}{{ substr($request->user->last_name ?? '', 0, 1) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $request->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center mt-1">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $request->user->email }}
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                {{ $request->user->contact_number ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ $request->user->address ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $request->currentPurok->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ $request->requestedPurok->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->requested_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                @if(in_array($currentStatus, ['approved', 'rejected']))
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->processed_at ? $request->processed_at->format('M j, Y g:i A') : 'N/A' }}
                                        @if($request->processedBy)
                                            <div class="text-xs text-gray-400">
                                                by {{ $request->processedBy->name }}
                                            </div>
                                        @endif
                                    </td>
                                    @if($currentStatus === 'rejected')
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $request->rejection_reason ?? 'No reason provided' }}
                                        </td>
                                    @endif
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if($request->status === 'pending')
                                        <div class="flex justify-center space-x-2">
                                            <form action="{{ route('purok_leader.approve-purok-change', $request) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-2 text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors shadow-sm" 
                                                    onclick="return confirm('Approve this resident to join your purok?')" title="Approve">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="document.getElementById('rejectModal{{ $request->id }}').classList.remove('hidden')" 
                                                class="p-2 text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm" title="Reject">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Processed
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Rejection Modal -->
                            <div id="rejectModal{{ $request->id }}" class="fixed z-10 inset-0 overflow-y-auto hidden">
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                    </div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                        <div>
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                                Reject Join Request
                                            </h3>
                                            <p class="text-sm text-gray-500 mb-4">
                                                You're about to reject <span class="font-medium">{{ $request->user->name }}'s</span> request to join your purok. 
                                                Please provide a reason for rejection.
                                            </p>
                                            <form action="{{ route('purok_leader.reject-purok-change', $request) }}" method="POST">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">
                                                        Reason for Rejection <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea id="rejection_reason" name="rejection_reason" rows="3" 
                                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2"
                                                        placeholder="Please provide a reason for rejecting this request..."
                                                        required></textarea>
                                                    <p class="mt-1 text-xs text-gray-500">This message will be shared with the resident.</p>
                                                </div>
                                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Confirm Rejection
                                                    </button>
                                                    <button type="button" onclick="document.getElementById('rejectModal{{ $request->id }}').classList.add('hidden')" 
                                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50">
                {{ $changeRequests->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-500">
                        @if($currentStatus === 'pending')
                            No pending requests
                        @elseif($currentStatus === 'approved')
                            No approved requests
                        @else
                            No rejected requests
                        @endif
                    </h3>
                    <p class="mt-1 text-sm text-gray-400">
                        @if($currentStatus === 'pending')
                            There are currently no pending purok change requests
                        @elseif($currentStatus === 'approved')
                            No purok change requests have been approved yet
                        @else
                            No purok change requests have been rejected
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('click', function(event) {
            const modals = document.querySelectorAll('[id^=rejectModal]');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    });
</script>
@endpush
@endsection
