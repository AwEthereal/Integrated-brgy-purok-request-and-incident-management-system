@extends('layouts.app')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-600 to-orange-800 text-white py-4 sm:py-5 px-4 sm:px-6 rounded-lg shadow-lg mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        My Incident Reports
                    </h2>
                    <p class="text-orange-100 text-sm mt-1">Track and manage your incident reports</p>
                </div>
                @if(auth()->user()->is_approved)
                    <a href="{{ route('incident_reports.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-white text-orange-700 rounded-lg font-semibold text-sm hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Report
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl">
            <div class="p-4 sm:p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filter Section -->
                <div class="mb-6 bg-gray-800 p-4 rounded-lg">
                    <form id="filterForm" method="GET" action="{{ route('incident_reports.my_reports') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div>
                            <label for="type" class="block text-xs font-medium text-gray-300 mb-1.5">Filter by Type</label>
                            <select id="type" name="type" onchange="this.form.submit()" class="block w-full px-3 py-2 text-sm bg-gray-700 border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 rounded-lg cursor-pointer">
                                <option value="">All Types</option>
                                @foreach(\App\Models\IncidentReport::TYPES as $value => $label)
                                    <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-300 mb-1.5">Filter by Status</label>
                            <select id="status" name="status" onchange="this.form.submit()" class="block w-full px-3 py-2 text-sm bg-gray-700 border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 rounded-lg cursor-pointer">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            @if(request()->has('type') || request()->has('status'))
                                <a href="{{ route('incident_reports.my_reports') }}" class="w-full sm:w-auto px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors text-sm flex items-center justify-center">
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

                @if($reports->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No incident reports</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You haven't submitted any incident reports yet.</p>
                        @if(auth()->user()->is_approved)
                            <div class="mt-6">
                                <a href="{{ route('incident_reports.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    New Incident Report
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mb-4">
                        <p class="text-sm text-gray-300">
                            Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} results
                            @if(request()->has('type') || request()->has('status'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-900 text-orange-200 ml-2">
                                    @if(request('type'))
                                        {{ \App\Models\IncidentReport::TYPES[request('type')] ?? request('type') }}
                                    @endif
                                    @if(request('type') && request('status'))
                                         • 
                                    @endif
                                    @if(request('status'))
                                        {{ format_label(request('status')) }}
                                    @endif
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach($reports as $report)
                                    {{-- Incidents are informational only - no dots in table after viewing --}}
                                    <tr class="hover:bg-gray-700 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            {{ $report->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-white">
                                            {{ \App\Models\IncidentReport::TYPES[$report->incident_type] ?? format_label($report->incident_type) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            {{ $report->purok ? $report->purok->name : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                    'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                    'Resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    'Invalid Report' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                ];
                                                $displayStatus = $report->getDisplayStatusForResident();
                                                $statusClass = $statusClasses[$displayStatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClass }}">
                                                {{ $displayStatus }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <a href="{{ route('incident_reports.show', $report->id) }}" class="p-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors inline-flex" title="View Details">
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
                        @foreach($reports as $report)
                            {{-- Incidents are informational only - no dots in cards after viewing --}}
                            <div class="bg-gray-700 rounded-lg p-3 hover:bg-gray-600 transition-colors">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-400 mb-1">{{ $report->created_at->format('M d, Y') }}</p>
                                        <p class="text-sm font-semibold text-white">{{ \App\Models\IncidentReport::TYPES[$report->incident_type] ?? format_label($report->incident_type) }}</p>
                                    </div>
                                    <a href="{{ route('incident_reports.show', $report->id) }}" class="flex-shrink-0 p-1.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-300">
                                    @php
                                        $statusClasses = [
                                            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'Resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'Invalid Report' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        ];
                                        $displayStatus = $report->getDisplayStatusForResident();
                                        $statusClass = $statusClasses[$displayStatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    @endphp
                                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ $displayStatus }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $report->purok ? $report->purok->name : 'N/A' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
