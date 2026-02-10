@extends('layouts.app')

@section('title', 'Clearance Approvals')

@php
    $status = request('status', 'pending');
    $isPendingView = $status !== 'completed';
    $title = $isPendingView ? 'Pending Barangay Clearance Requests' : 'Purok Clearance History';
    $pendingCount = isset($requests) && !$isPendingView ? \App\Models\Request::where('status', 'purok_approved')->count() : 0;
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Dark Card with Header -->
        <div class="bg-gray-800 dark:bg-gray-900 rounded-t-lg shadow-md p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 mb-6">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-white">{{ $title }}</h2>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('barangay.approvals.index') }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $isPendingView ? 'bg-green-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        Pending
                        @php
                            $pendingRequestCount = \App\Models\Request::where('status', 'purok_approved')->count();
                        @endphp
                        @if($pendingRequestCount > 0)
                            <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingRequestCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('barangay.approvals.index', ['status' => 'completed']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ !$isPendingView ? 'bg-green-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        History
                    </a>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
                        {{ isset($requests) ? $requests->count() : 0 }} {{ Str::plural('Request', isset($requests) ? $requests->count() : 0) }}
                    </span>
                </div>
            </div>

            @if(!$isPendingView)
                <div class="flex justify-end gap-2 mb-4">
                    <button type="button" onclick="printAllApprovalsHistory()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                        Print All
                    </button>
                    <button type="button" onclick="printSelectedApprovalsHistory()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                        Print Selected
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900 border-l-4 border-green-500 text-green-200 rounded" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-900 border-l-4 border-red-500 text-red-200 rounded" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search and Filter Section - Integrated in Dark Card -->
            <form method="GET" action="{{ route('barangay.approvals.index') }}">
                <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by ID, resident name..."
                               class="w-full rounded-md bg-gray-700 border-gray-600 text-white placeholder-gray-400 focus:border-green-500 focus:ring-green-500"
                               oninput="filterApprovalsTable()">
                    </div>

                    <!-- Purok Filter -->
                    <div>
                        <label for="purok" class="block text-sm font-medium text-gray-300 mb-2">Purok</label>
                        <select id="purok" 
                                name="purok" 
                                class="w-full rounded-md bg-gray-700 border-gray-600 text-white focus:border-green-500 focus:ring-green-500"
                                onchange="filterApprovalsTable()">
                            <option value="">All Puroks</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok->id }}" {{ request('purok') == $purok->id ? 'selected' : '' }}>
                                    {{ $purok->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter (only for history view) -->
                    @if(!$isPendingView)
                        <div>
                            <label for="request_status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select id="request_status" 
                                    name="request_status" 
                                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white focus:border-green-500 focus:ring-green-500"
                                    onchange="filterApprovalsTable()">
                                <option value="">All Status</option>
                                <option value="purok_approved" {{ request('request_status') == 'purok_approved' ? 'selected' : '' }}>Purok Approved</option>
                                <option value="in_progress" {{ request('request_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('request_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ request('request_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('barangay.approvals.index', ['status' => request('status', 'pending')]) }}" 
                       class="px-4 py-2 border border-gray-600 rounded-md text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Section - Continues Dark Theme -->
        <div class="bg-gray-900 rounded-b-lg shadow-md overflow-hidden">
            @if(request('status') === 'completed')
                @include('barangay_official.partials.history_table', ['requests' => $requests])
            @else
                @include('barangay_official.partials.pending_table', ['requests' => $requests])
            @endif

            @if(request('status') === 'completed' && isset($requests) && $requests->hasPages())
                <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">
                    {{ $requests->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function filterApprovalsTable() {
    const input = document.getElementById('search');
    if (!input) return;
    const filter = (input.value || '').toLowerCase();
    const purokSelect = document.getElementById('purok');
    const selectedPurok = purokSelect ? (purokSelect.value || '') : '';
    const statusSelect = document.getElementById('request_status');
    const selectedStatus = statusSelect ? (statusSelect.value || '') : '';
    const tables = document.querySelectorAll('table');
    if (!tables.length) return;

    // Filter all tbody rows in the included tables
    tables.forEach(function(table){
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(function(row){
            // Skip empty-state row (colspan)
            const isEmptyState = row.querySelector('td[colspan]');
            if (isEmptyState) return;
            const text = (row.textContent || row.innerText || '').toLowerCase();
            const rowPurok = row.getAttribute('data-purok-id') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesSearch = filter === '' || text.indexOf(filter) !== -1;
            const matchesPurok = selectedPurok === '' || rowPurok === selectedPurok;
            const matchesStatus = selectedStatus === '' || rowStatus === selectedStatus;
            row.style.display = (matchesSearch && matchesPurok && matchesStatus) ? '' : 'none';
        });
    });
}

function toggleAllHistory(source) {
    const boxes = document.querySelectorAll('.history-checkbox');
    boxes.forEach(function(cb){ cb.checked = source.checked; });
}

function printAllApprovalsHistory() {
    const url = "{{ route('reports.preview.purok-clearance') }}";
    window.open(url, '_blank');
}

function printSelectedApprovalsHistory() {
    const selected = Array.from(document.querySelectorAll('.history-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select at least one request to preview.');
        return;
    }
    const url = "{{ route('reports.preview.purok-clearance') }}" + '?ids=' + selected.join(',');
    window.open(url, '_blank');
}

// Run once on page load if there's an initial query
document.addEventListener('DOMContentLoaded', function(){
    filterApprovalsTable();
});
</script>
@endsection
