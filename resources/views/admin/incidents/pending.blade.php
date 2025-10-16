@extends('layouts.app')

@php
    $status = request('status', 'pending');
    $isPendingView = $status === 'pending';
    $title = $isPendingView ? 'Pending Incident Reports' : 'Incident History';
    $emptyMessage = $isPendingView 
        ? 'There are currently no incident reports awaiting your review.'
        : 'No resolved or invalid incident reports found.';
    $emptyIcon = $isPendingView 
        ? 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'
        : 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Dark Card with Header -->
        <div class="bg-gray-800 dark:bg-gray-900 rounded-t-lg shadow-md p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 mb-6">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-white">{{ $title }}</h2>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('barangay.incident_reports.index', ['status' => 'pending']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $isPendingView ? 'bg-green-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        Pending
                        @if(isset($pendingCount) && $pendingCount > 0)
                            <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('barangay.incident_reports.index', ['status' => 'completed']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ !$isPendingView ? 'bg-green-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        History
                    </a>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
                        {{ $reports->total() }} {{ Str::plural('Report', $reports->total()) }}
                    </span>
                </div>
            </div>
            @if (session('success'))
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

            <!-- Search and Filter Section - Integrated in Dark Card -->
            <form method="GET" action="{{ route('barangay.incident_reports.index') }}">
                <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search by ID, reporter name..."
                                   class="w-full rounded-md bg-gray-700 border-gray-600 text-white placeholder-gray-400 focus:border-green-500 focus:ring-green-500">
                            </div>

                        <!-- Incident Type Filter -->
                        <div>
                            <label for="incident_type" class="block text-sm font-medium text-gray-300 mb-2">Incident Type</label>
                            <select id="incident_type" 
                                    name="incident_type" 
                                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white focus:border-green-500 focus:ring-green-500">
                                <option value="">All Types</option>
                                @foreach(\App\Models\IncidentReport::TYPES as $key => $label)
                                    <option value="{{ $key }}" {{ request('incident_type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Purok Filter -->
                        <div>
                            <label for="purok" class="block text-sm font-medium text-gray-300 mb-2">Purok</label>
                            <select id="purok" 
                                    name="purok" 
                                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white focus:border-green-500 focus:ring-green-500">
                                <option value="">All Puroks</option>
                                @foreach(\App\Models\Purok::orderBy('name')->get() as $purok)
                                    <option value="{{ $purok->id }}" {{ request('purok') == $purok->id ? 'selected' : '' }}>
                                        {{ $purok->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter (only for history view) -->
                        @if(!$isPendingView)
                            <div>
                                <label for="incident_status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                                <select id="incident_status" 
                                        name="incident_status" 
                                        class="w-full rounded-md bg-gray-700 border-gray-600 text-white focus:border-green-500 focus:ring-green-500">
                                    <option value="">All Status</option>
                                    <option value="in_progress" {{ request('incident_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ request('incident_status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="rejected" {{ request('incident_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="invalid" {{ request('incident_status') == 'invalid' ? 'selected' : '' }}>Invalid</option>
                                </select>
                            </div>
                        @endif
                    </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('barangay.incident_reports.index', ['status' => request('status', 'pending')]) }}" 
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

        <!-- White Table Section -->
        <div class="bg-white dark:bg-gray-800 rounded-b-lg shadow-md overflow-hidden">

                @if ($reports->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="{{ $emptyIcon }}" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
                            {{ $isPendingView ? 'No pending incident reports' : 'No incident history' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyMessage }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <div class="align-middle inline-block min-w-full overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">Report ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 18%;">Incident Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 20%;">Reported By</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">Purok</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">Date Reported</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 7%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($reports as $report)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('incident_reports.show', $report) }}'">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                                #{{ str_pad($report->id, 2, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                                {{ format_label($report->incident_type) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $report->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $report->purok->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                        'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300', // Backward compatibility
                                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                        'invalid' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                                                    ];
                                                    $statusClass = $statusColors[$report->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $report->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('incident_reports.show', $report) }}" class="text-green-400 hover:text-green-300" title="View Details">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    @endif

            @if($reports->hasPages())
                <div class="px-6 py-4 bg-white border-t border-gray-200">
                    {{ $reports->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
