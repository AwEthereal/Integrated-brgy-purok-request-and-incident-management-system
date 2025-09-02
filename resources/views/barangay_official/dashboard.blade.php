@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6 text-green-700 flex items-center">
        <svg class="w-7 h-7 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2m-6 0h6" />
        </svg>
        Barangay Official Dashboard
    </h1>

    @include('barangay_official.partials.purok_filter', ['puroks' => $puroks, 'selectedPurok' => $selectedPurok])

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button onclick="switchTab('pending')" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $currentTab === 'pending' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pending Requests
                        @if(count($pendingRequests) > 0)
                            <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ count($pendingRequests) }}
                            </span>
                        @endif
                    </button>
                    <button onclick="switchTab('history')" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $currentTab === 'history' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Request History
                        @if($completedRequests->total() > 0)
                            <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $completedRequests->total() }}
                            </span>
                        @endif
                    </button>
                </nav>
            </div>

            <!-- Pending Requests Tab -->
            <div id="pending" class="p-6 {{ $currentTab === 'pending' ? '' : 'hidden' }}">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Pending Clearance Requests
                </h2>
                <ul>
                    @forelse($pendingRequests as $request)
                        <li class="border-b py-2 flex justify-between items-center">
                            <span>
                                <span class="font-medium">#{{ $request->id }}</span> -
                                {{ $request->user->name }}
                                <span class="ml-2 px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">
                                    Pending Approval
                                </span>
                            </span>
                            <a href="{{ route('barangay.approvals.show', $request->id) }}" class="text-green-700 hover:underline">View</a>
                        </li>
                    @empty
                        <li class="text-gray-500 py-2">No pending requests found for this purok.</li>
                    @endforelse
                </ul>
            </div>

            <!-- History Tab -->
            <div id="history" class="p-6 {{ $currentTab === 'history' ? '' : 'hidden' }}">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Request History
                    </h2>
                    
                    <!-- Status Filter -->
                    <div class="w-full sm:w-48">
                        <label for="status-filter" class="sr-only">Filter by status</label>
                        <select 
                            id="status-filter" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                            onchange="updateUrlParameter('status', this.value)">
                            <option value="" {{ !request()->has('status') ? 'selected' : '' }}>All Processed Requests</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Approved & Completed</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected Requests</option>
                        </select>
                    </div>
                </div>
                
                @if($completedRequests->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <div class="align-middle inline-block min-w-full">
                            <div class="overflow-hidden border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Request #
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Resident
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($completedRequests as $request)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    #{{ $request->id }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $request->user->name }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($request->status === 'barangay_approved')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Approved
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Rejected
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    @if($request->status === 'barangay_approved')
                                                        {{ $request->barangay_approved_at?->format('M d, Y h:i A') ?? 'N/A' }}
                                                    @else
                                                        {{ $request->rejected_at?->format('M d, Y h:i A') ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                    <a 
                                                        href="{{ route('barangay.approvals.show', $request->id) }}" 
                                                        class="inline-flex items-center px-2.5 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                        aria-label="View request #{{ $request->id }} details">
                                                        <svg class="-ml-0.5 mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-3">
                        @foreach($completedRequests as $request)
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                #{{ $request->id }} - {{ $request->user->name }}
                                            </p>
                                            @if($request->status === 'barangay_approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">
                                            @if($request->status === 'barangay_approved')
                                                Approved on {{ $request->barangay_approved_at?->format('M d, Y h:i A') ?? 'N/A' }}
                                            @else
                                                Rejected on {{ $request->rejected_at?->format('M d, Y h:i A') ?? 'N/A' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a 
                                            href="{{ route('barangay.approvals.show', $request->id) }}" 
                                            class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                            aria-label="View request #{{ $request->id }} details">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $completedRequests->appends([
                            'purok' => $selectedPurok, 
                            'tab' => 'history',
                            'status' => request('status')
                        ])->links() }}
                    </div>
                @else
                    <div class="text-center py-8 bg-white rounded-lg border border-gray-200">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No requests found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request('status'))
                                No {{ request('status') }} requests found for this filter.
                            @else
                                No request history found for this purok.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="border-b border-gray-200">
                <h2 class="px-6 py-4 text-lg font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Incident Reports
                </h2>
            </div>
            <div class="p-6">
                @if($incidents->count() > 0)
                    <div class="space-y-3">
                        @foreach($incidents as $incident)
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">
                                                #{{ $incident->id }} - {{ $incident->user->name }}
                                            </p>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $incident->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($incident->status) }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Reported on {{ $incident->created_at->format('M d, Y h:i A') }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a 
                                            href="{{ route('incident_reports.show', $incident->id) }}" 
                                            class="inline-flex items-center px-2.5 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                            aria-label="View incident #{{ $incident->id }} details">
                                            <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-white rounded-lg border border-gray-200">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No incident reports</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            No incident reports have been submitted for this purok.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabName) {
        // If clicking the already active tab, do nothing
        const currentTab = new URLSearchParams(window.location.search).get('tab') || 'pending';
        if (currentTab === tabName) return;
        
        // Build the new URL with the correct tab parameter
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        
        // If switching to pending tab, remove the status filter
        if (tabName === 'pending') {
            url.searchParams.delete('status');
        }
        
        // Navigate to the new URL
        window.location.href = url.toString();
    }
    
    // Function to update URL parameters
    function updateUrlParameter(param, value) {
        // Get current search params
        const searchParams = new URLSearchParams(window.location.search);
        
        // Update or remove the parameter
        if (value === '') {
            searchParams.delete(param);
            // If removing status filter, also remove the page parameter
            if (param === 'status') {
                searchParams.delete('page');
            }
        } else {
            searchParams.set(param, value);
            // When changing status, reset to first page
            if (param === 'status') {
                searchParams.set('page', '1');
            }
        }
        
        // Always set tab to history when filtering status
        if (param === 'status') {
            searchParams.set('tab', 'history');
        }
        
        // Preserve other query parameters
        const currentTab = searchParams.get('tab') || 'pending';
        const purok = searchParams.get('purok') || '';
        
        // Build the new URL with all parameters
        let newUrl = '/dashboard?';
        if (currentTab) newUrl += `tab=${currentTab}&`;
        if (purok) newUrl += `purok=${purok}&`;
        if (param === 'status' && value) newUrl += `status=${value}&`;
        
        // Remove trailing & or ? if no parameters
        newUrl = newUrl.replace(/[&?]$/, '');
        
        // Navigate to the new URL
        window.location.href = newUrl;
    }
    
    // Initialize the correct tab on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'pending';
        const status = urlParams.get('status');
        
        // Update tab buttons active state
        document.querySelectorAll('button[onclick^="switchTab"]').forEach(button => {
            const tabName = button.getAttribute('onclick').match(/'([^']+)'/)[1];
            if (tabName === tab) {
                button.classList.add('border-green-500', 'text-green-600');
                button.classList.remove('border-transparent', 'text-gray-500');
            } else {
                button.classList.remove('border-green-500', 'text-green-600');
                button.classList.add('border-transparent', 'text-gray-500');
            }
        });
        
        // Show the correct tab content
        document.querySelectorAll('[id^="pending"], [id^="history"]').forEach(tabElement => {
            tabElement.classList.add('hidden');
        });
        
        const activeTab = document.getElementById(tab);
        if (activeTab) {
            activeTab.classList.remove('hidden');
        } else {
            // Fallback to pending tab if specified tab doesn't exist
            document.getElementById('pending').classList.remove('hidden');
        }
        
        // Update status filter if it exists
        if (document.getElementById('status-filter') && status) {
            document.getElementById('status-filter').value = status;
        }
    });
</script>
@endpush

@endsection
