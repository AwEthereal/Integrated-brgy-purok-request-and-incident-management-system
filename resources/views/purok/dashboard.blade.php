@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6 text-green-700 flex items-center">
        <svg class="w-7 h-7 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2m-6 0h6" />
        </svg>
        Purok Dashboard
    </h1>
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-6 4v2a2 2 0 002 2h2a2 2 0 002-2v-2" />
            </svg>
            Requests & Incidents
        </h2>
        <div class="text-gray-500">
            <!-- Place purok-style dashboard content here -->
            This is the purok-style dashboard. Resident statistics and activity will appear here.
        </div>
    </div>
</div>
@endsection
