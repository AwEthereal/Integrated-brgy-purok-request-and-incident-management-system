@extends('layouts.app')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-4 sm:py-5 px-4 sm:px-6 rounded-lg shadow-lg mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        My Requests
                    </h2>
                    <p class="text-green-100 text-sm mt-1">View and manage your clearance requests</p>
                </div>
                <a href="{{ route('requests.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-white text-green-700 rounded-lg font-semibold text-sm hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Request
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl">
            <div class="p-4 sm:p-6">

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-200">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Filter Section -->
                <div class="mb-6 bg-gray-800 p-4 rounded-lg">
                    <form id="filterForm" method="GET" action="{{ route('requests.my_requests') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div>
                            <label for="form_type" class="block text-xs font-medium text-gray-300 mb-1.5">Filter by Type</label>
                            <select id="form_type" name="form_type" onchange="this.form.submit()" class="block w-full px-3 py-2 text-sm bg-gray-700 border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg cursor-pointer">
                                <option value="">All Types</option>
                                @foreach(\App\Models\Request::FORM_TYPES as $key => $type)
                                    <option value="{{ $key }}" {{ request('form_type') == $key ? 'selected' : '' }}>{{ format_label($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-300 mb-1.5">Filter by Status</label>
                            <select id="status" name="status" onchange="this.form.submit()" class="block w-full px-3 py-2 text-sm bg-gray-700 border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg cursor-pointer">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="barangay_approved" {{ request('status') == 'barangay_approved' ? 'selected' : '' }}>Barangay Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            @if(request()->has('form_type') || request()->has('status'))
                                <a href="{{ route('requests.my_requests') }}" class="w-full sm:w-auto px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors text-sm flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Clear Filters
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <script>
                    // Add visual feedback when filters are being applied
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('filterForm');
                        const selects = form.querySelectorAll('select');
                        
                        selects.forEach(select => {
                            select.addEventListener('change', function() {
                                // Add loading state
                                const button = document.querySelector('button[type="submit"]');
                                if (button) {
                                    button.disabled = true;
                                    button.innerHTML = 'Applying...';
                                }
                                
                                // Submit the form
                                form.submit();
                            });
                        });
                    });
                </script>

                @if($requests->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No requests found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if(request('form_type') || request('status'))
                                Try adjusting your filters or
                            @endif
                            Get started by creating a new request.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                New Request
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Purpose
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach($requests as $request)
                                    @php
                                        // Show dot if resident hasn't viewed the request since last update
                                        $isUnread = !$request->last_viewed_at || 
                                                   ($request->updated_at && $request->last_viewed_at && 
                                                    $request->updated_at->gt($request->last_viewed_at));
                                        $isRelevantStatus = in_array($request->status, ['purok_approved', 'barangay_approved', 'rejected']);
                                        $showDot = $isUnread && $isRelevantStatus;
                                    @endphp
                                    <tr class="hover:bg-gray-700 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            <div class="flex items-center gap-2">
                                                @if($showDot)
                                                    {{-- Yellow dot for items needing attention --}}
                                                    <span class="relative inline-flex flex-shrink-0">
                                                        <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                                    </span>
                                                @endif
                                                <span>REQ-{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-white">
                                            {{ $request->purpose }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            {{ format_label($request->form_type) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'purok_approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'barangay_approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                ][$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClasses }}">
                                                {{ format_label($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            {{ $request->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <a href="{{ route('requests.show', $request) }}" class="p-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors inline-flex" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-3">
                        @foreach($requests as $request)
                            @php
                                // Show dot if resident hasn't viewed the request since last update
                                $isUnread = !$request->last_viewed_at || 
                                           ($request->updated_at && $request->last_viewed_at && 
                                            $request->updated_at->gt($request->last_viewed_at));
                                $isRelevantStatus = in_array($request->status, ['purok_approved', 'barangay_approved', 'rejected']);
                                $showDot = $isUnread && $isRelevantStatus;
                            @endphp
                            <div class="bg-gray-700 rounded-lg p-3 hover:bg-gray-600 transition-colors relative">
                                @if($showDot)
                                    {{-- Yellow dot for items needing attention --}}
                                    <div class="absolute -top-1 -right-1 z-10">
                                        <span class="relative inline-flex">
                                            <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
                                            <span class="relative inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                                        </span>
                                    </div>
                                @endif
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-400 mb-1">REQ-{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-sm font-semibold text-white truncate">{{ $request->purpose }}</p>
                                    </div>
                                    <a href="{{ route('requests.show', $request) }}" class="flex-shrink-0 p-1.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-300">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'purok_approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'barangay_approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        ][$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    @endphp
                                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ format_label($request->status) }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ format_label($request->form_type) }}</span>
                                    <span>•</span>
                                    <span>{{ $request->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
