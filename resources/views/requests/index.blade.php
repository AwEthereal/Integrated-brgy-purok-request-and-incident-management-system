@extends('layouts.app')

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
                    <a href="{{ route('requests.show', $request->id) }}" class="text-blue-600 ml-2">View</a>
                    <a href="{{ route('requests.edit', $request->id) }}" class="text-blue-600 ml-2">Edit</a>
                </li>
            @empty
                <li>No requests yet.</li>
            @endforelse
        </ul>
    </div>
@endsection
