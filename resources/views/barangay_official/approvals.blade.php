@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Pending Barangay Clearance Requests</h2>
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4">{{ session('error') }}</div>
        @endif
        @include('barangay_official.partials.purok_filter', ['puroks' => $puroks ?? []])
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">Resident</th>
                    <th class="px-4 py-2">Purok</th>
                    <th class="px-4 py-2">Form Type</th>
                    <th class="px-4 py-2">Purpose</th>
                    <th class="px-4 py-2">Requested At</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td class="px-4 py-2">{{ $request->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $request->purok->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $request->form_type }}</td>
                        <td class="px-4 py-2">{{ $request->purpose }}</td>
                        <td class="px-4 py-2">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('barangay.approvals.show', $request->id) }}" class="text-green-700 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">No pending requests.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
