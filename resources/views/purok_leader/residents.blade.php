@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold">Residents in {{ $purokName }}</h1>
            <p class="text-gray-600">Total Residents: {{ $residents->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requests</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($residents as $resident)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">{{ substr($resident->first_name, 0, 1) }}{{ substr($resident->last_name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('purok_leader.residents.show', $resident->id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $resident->full_name }}
                                        </a>
                                        <div class="text-sm text-gray-500">{{ $resident->address }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $resident->contact_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $resident->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $resident->requests_count }} requests
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($resident->is_approved)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Approved on {{ $resident->approved_at?->format('M d, Y') }}
                                    </div>
                                @elseif($resident->rejected_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                    @if($resident->rejection_reason)
                                        <div class="text-xs text-gray-500 mt-1" title="{{ $resident->rejection_reason }}">
                                            {{ Str::limit($resident->rejection_reason, 30) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending Approval
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(!$resident->is_approved && !$resident->rejected_at)
                                        <form action="{{ route('purok_leader.residents.approve', $resident) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Are you sure you want to approve this resident?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1 text-green-600 hover:text-green-900 rounded-full hover:bg-green-50" title="Approve">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('purok_leader.residents.reject-form', $resident) }}" class="p-1 text-red-600 hover:text-red-900 rounded-full hover:bg-red-50" title="Reject">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </a>
                                    @elseif($resident->rejected_at)
                                        <form action="{{ route('purok_leader.residents.approve', $resident) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Are you sure you want to approve this resident?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1 text-green-600 hover:text-green-900 rounded-full hover:bg-green-50" title="Approve">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Approved</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No residents found in your purok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
