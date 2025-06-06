@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold">Purok President Dashboard</h1>
                <p class="text-gray-600">Managing: <span class="font-medium">{{ $purokName }}</span></p>
            </div>
            <div class="flex items-center space-x-2">
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
            <div class="bg-white rounded-lg shadow p-6">
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
            </div>

            <!-- Pending Requests Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Pending</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_requests'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved Requests Card -->
            <div class="bg-white rounded-lg shadow p-6">
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
            </div>

            <!-- Rejected Requests Card -->
            <div class="bg-white rounded-lg shadow p-6">
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
            </div>

            <!-- Total Residents Card -->
            <div class="bg-white rounded-lg shadow p-6">
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
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif


        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Purok Clearance Requests</h2>
            <details>
                <summary>Click to View Requests</summary>
                <div class="overflow-x-auto bg-white shadow rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Request ID</th>
                                <th class="px-4 py-2">Resident Name</th>
                                <th class="px-4 py-2">Address</th>
                                <th class="px-4 py-2">Purpose</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $request)
                                <tr>
                                    <td class="px-4 py-2"># {{ $request->id }}</td>
                                    <td class="px-4 py-2">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $request->user->address ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $request->purpose }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800'
                                            ];
                                            $color = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">No clearance requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>
    </div>



@endsection