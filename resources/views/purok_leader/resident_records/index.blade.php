@extends('layouts.app')

@section('title', 'Resident Records')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-4">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold">Resident Records (RBI Form B)</h1>
        <p class="text-purple-100 mt-1">Manage individual records for your purok</p>
    </div>
    
</div>

<div class="max-w-6xl mx-auto px-4">
    <div class="mb-3">
        <a href="{{ route('purok_leader.resident_records.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700 shadow">
            + Add Record
        </a>
    </div>
    <form method="GET" class="mb-4">
        <div class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search by name, PhilSys no., address, contact" class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button class="px-4 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700">Search</button>
        </div>
    </form>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sex</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Birth Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Address</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Contact</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($records as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $r->last_name }}, {{ $r->first_name }} {{ $r->middle_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->sex }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ optional($r->birth_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->residence_address }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->contact_number }}</td>
                        <td class="px-4 py-2 text-sm text-center">
                            <a href="{{ route('purok_leader.resident_records.show', ['record' => $r->id]) }}" class="text-blue-600 hover:underline">View</a>
                            @can('update', $r)
                                <span class="text-gray-300 mx-1">|</span>
                                <a href="{{ route('purok_leader.resident_records.edit', ['record' => $r->id]) }}" class="text-purple-600 hover:underline">Edit</a>
                            @endcan
                            @can('delete', $r)
                                <span class="text-gray-300 mx-1">|</span>
                                <form action="{{ route('purok_leader.resident_records.destroy', ['record' => $r->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $records->links() }}</div>
</div>
@endsection
