@extends('layouts.app')

@section('title', 'My Requests')

@section('content')
    <div class="max-w-7xl mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">Your Requests</h1>

        @if(session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        <a href="{{ route('requests.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Create New Request</a>

        <ul class="space-y-2">
            @forelse($requests as $request)
                <li class="p-4 bg-white shadow rounded">
                    {{ format_label($request->form_type) }} - {{ $request->purpose }} - {{ format_label($request->status ?? 'Pending') }}
                    <a href="{{ route('requests.show', $request->id) }}" class="inline-flex items-center text-blue-600 ml-2" title="View Details">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <a href="{{ route('requests.edit', $request->id) }}" class="text-blue-600 ml-2">Edit</a>
                </li>
            @empty
                <li>No requests yet.</li>
            @endforelse
        </ul>
    </div>
@endsection
