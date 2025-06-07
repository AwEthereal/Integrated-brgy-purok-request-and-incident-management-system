@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Resident Profile: {{ $resident->name }}</h1>
            <p class="text-gray-600">{{ $purokName }}</p>
        </div>
        <a href="{{ route('purok_leader.residents') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Residents
        </a>
    </div>

    <!-- Resident Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Full Name</p>
                <p class="font-medium">{{ $resident->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ $resident->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Contact Number</p>
                <p class="font-medium">{{ $resident->contact_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Address</p>
                <p class="font-medium">{{ $resident->address ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date Registered</p>
                <p class="font-medium">{{ $resident->created_at->format('F j, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Resident's Requests -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <details>
            <summary class="flex justify-between items-center mb-4 cursor-pointer">
                <h2 class="text-lg font-semibold text-gray-700 hover:text-blue-600">Request History</h2>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    {{ $resident->requests->count() }} {{ Str::plural('Request', $resident->requests->count()) }}
                </span>
            </summary>
            @if($resident->requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Requested</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($resident->requests as $request)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#{{ $request->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ str_replace('_', ' ', ucfirst($request->form_type)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $request->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'purok_approved' => 'bg-blue-100 text-blue-800',
                                            'barangay_approved' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800'
                                        ];
                                        $color = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                        {{ str_replace('_', ' ', ucfirst($request->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('requests.show', $request) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No requests found</h3>
                <p class="mt-1 text-sm text-gray-500">This resident hasn't made any requests yet.</p>
            </div>
        @endif
    </div>
</details>  
</div>
@endsection
