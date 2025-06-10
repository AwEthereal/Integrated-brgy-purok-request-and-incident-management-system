@extends('layouts.app')

@push('scripts')
    @vite(['resources/js/purok-notifications.js'])
@endpush

@section('content')
    <!-- Add purok ID for real-time notifications -->
    <meta name="purok-id" content="{{ auth()->user()->purok_id }}">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold purok-leader-dashboard">Purok President Dashboard</h1>
                <p class="text-gray-600">Managing: <span class="font-medium">{{ $purokName }}</span></p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Pending Requests Badge -->
                <div class="relative">
                    <a href="{{ route('purok_leader.dashboard', ['status' => 'pending']) }}" class="flex items-center space-x-1 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium hover:bg-red-200 transition-colors">
                        <span>Pending Requests</span>
                        <span class="pending-requests-badge flex items-center justify-center h-5 w-5 bg-red-600 text-white text-xs rounded-full">
                            {{ \App\Models\Request::where('purok_id', auth()->user()->purok_id)->where('status', 'pending')->count() }}
                        </span>
                    </a>
                </div>

                @php
                    $roleLabels = [
                        'purok_leader' => 'Purok President',
                        'purok_president' => 'Purok President',
                        'admin' => 'Admin',
                        'barangay_kagawad' => 'Barangay Official',
                        'barangay_captain' => 'Barangay Captain',
                        'secretary' => 'Secretary',
                        'sk_chairman' => 'SK Chairman',
                    ];

                    $userRole = auth()->user()->role ?? 'unknown';
                    $displayRole = $roleLabels[$userRole] ?? ucfirst(str_replace('_', ' ', $userRole));
                @endphp

                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    Purok ID: {{ auth()->user()->purok_id }}
                </span>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    Role: {{ $displayRole }}
                </span>
            </div>

        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Requests Card -->
            <a href="{{ route('purok_leader.dashboard') }}"
                class="block bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow duration-200 {{ !isset($activeFilter) ? 'ring-2 ring-blue-500' : '' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Total Requests</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_requests'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Pending Requests Card -->
            <a href="{{ route('purok_leader.dashboard', ['filter' => 'status', 'value' => 'pending']) }}"
                class="pending-requests-card block bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow duration-200 {{ isset($activeFilter) && $activeFilter['type'] == 'status' && $activeFilter['value'] == 'pending' ? 'ring-2 ring-yellow-500' : '' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Pending Requests</h3>
                        <p class="text-2xl font-semibold text-gray-900">
                            <span class="pending-requests-badge">{{ $pendingCount }}</span>
                        </p>
                    </div>
                </div>
            </a>

            <!-- Approved Requests Card -->
            <a href="{{ route('purok_leader.dashboard', ['filter' => 'status', 'value' => 'approved']) }}"
                class="block bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow duration-200 {{ isset($activeFilter) && $activeFilter['type'] == 'status' && $activeFilter['value'] == 'approved' ? 'ring-2 ring-green-500' : '' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Approved</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved_requests'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Rejected Requests Card -->
            <a href="{{ route('purok_leader.dashboard', ['filter' => 'status', 'value' => 'rejected']) }}"
                class="block bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow duration-200 {{ isset($activeFilter) && $activeFilter['type'] == 'status' && $activeFilter['value'] == 'rejected' ? 'ring-2 ring-red-500' : '' }}">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Rejected</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['rejected_requests'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Total Residents Card -->
            <a href="{{ route('purok_leader.residents') }}"
                class="block bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Total Residents</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['residents_count'] }}</p>
                    </div>
                </div>
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif


        <!-- Active Filter Badge -->
        @if(isset($activeFilter))
            <div class="mb-4 flex items-center">
                <span class="text-sm text-gray-600 mr-2">Filtered by:</span>
                @if($activeFilter['type'] == 'status')
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                                @if($activeFilter['value'] == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($activeFilter['value'] == 'approved') bg-green-100 text-green-800
                                                @elseif($activeFilter['value'] == 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                        {{ str_replace('_', ' ', ucfirst($activeFilter['value'])) }} Requests
                    </span>
                @elseif($activeFilter['type'] == 'resident')
                    @php
                        $resident = App\Models\User::find($activeFilter['value']);
                    @endphp
                    <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-sm font-medium">
                        {{ $resident ? $resident->name : 'Resident' }}'s Requests
                    </span>
                @endif
                <a href="{{ route('purok_leader.dashboard') }}" class="ml-2 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        @endif

        <div class="mb-8 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    @if(isset($activeFilter))
                        @if($activeFilter['type'] == 'status')
                            {{ str_replace('_', ' ', ucfirst($activeFilter['value'])) }} Requests
                        @elseif($activeFilter['type'] == 'resident')
                            {{ $resident ? $resident->name . "'s" : 'Resident' }} Requests
                        @endif
                    @else
                        Purok Clearance Requests
                    @endif
                    <span class="text-sm text-gray-500 font-normal ml-2">({{ $requests->total() }} total)</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Request ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Requested</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resident Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Address</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purpose</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr>
                                <td class="px-4 py-2 text-center"># {{ $request->id }}</td>
                                <td class="px-4 py-2 text-center">{{ $request->created_at->format('F j, Y') }}</td>
                                <td class="px-4 py-2 text-center">{{ $request->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-center">{{ $request->user->address ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-center">{{ $request->purpose }}</td>
                                <td class="px-4 py-2 text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'purok_approved' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $color = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                        $statusLabel = [
                                            'pending' => 'Pending',
                                            'purok_approved' => 'Purok Approved',
                                            'completed' => 'Completed',
                                            'rejected' => 'Rejected',
                                            'cancelled' => 'Cancelled'
                                        ][$request->status] ?? ucfirst($request->status);
                                    @endphp
                                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $color }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('requests.show', $request) }}"
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        View Details
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">No clearance requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="px-6 py-3 bg-white border-t border-gray-200">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="px-6 py-4 border-t border-gray-200 text-sm text-gray-500">
                        Showing {{ $requests->count() }} requests
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .pagination li {
            margin: 0 0.25rem;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            border: 1px solid #d1d5db; /* Tailwind gray-300 */
            border-radius: 0.25rem;
            color: #374151; /* Tailwind gray-700 */
            font-size: 0.875rem; /* text-sm */
            text-decoration: none;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .pagination a:hover {
            background-color: #f3f4f6; /* gray-100 */
            border-color: #9ca3af;     /* gray-400 */
        }

        .pagination .active span {
            background-color: #3b82f6; /* blue-500 */
            border-color: #3b82f6;
            color: white;
        }

        .pagination .disabled span {
            background-color: #f3f4f6; /* gray-100 */
            border-color: #d1d5db;     /* gray-300 */
            color: #9ca3af;            /* gray-400 */
        }
    </style>
@endpush