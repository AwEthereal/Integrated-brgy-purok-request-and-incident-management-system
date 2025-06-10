@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Reject Resident Application</h2>
            
            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    You are about to reject the application of <span class="font-semibold">{{ $resident->full_name }}</span>.
                    Please provide a reason for rejection.
                </p>
                
                <form action="{{ route('purok_leader.residents.reject', $resident) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-gray-700 text-sm font-medium mb-2">
                            Reason for Rejection
                        </label>
                        <textarea 
                            name="rejection_reason" 
                            id="rejection_reason" 
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required
                            placeholder="Please provide a clear reason for rejecting this application..."></textarea>
                        @error('rejection_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-4 mt-6">
                        <a 
                            href="{{ route('purok_leader.residents') }}" 
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
