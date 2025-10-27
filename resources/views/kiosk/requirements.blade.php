@extends('layouts.kiosk')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('kiosk.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 rounded-xl shadow-lg text-lg font-semibold text-gray-700 transition">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Home
        </a>
    </div>

    <!-- Page Title -->
    <div class="text-center mb-10">
        <h2 class="text-5xl font-bold text-blue-900 mb-4">Document Requirements</h2>
        <p class="text-2xl text-gray-700">What you need to bring</p>
    </div>

    <!-- Requirements Grid -->
    <div class="grid md:grid-cols-2 gap-8">
        @foreach($requirements as $key => $requirement)
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ $requirement['name'] }}</h3>
                
                <!-- Requirements List -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Required Documents:</h4>
                    <ul class="space-y-2">
                        @foreach($requirement['requirements'] as $req)
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg text-gray-700">{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Fee and Processing Time -->
                <div class="border-t border-gray-200 pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-gray-700">Processing Time:</span>
                        <span class="text-lg text-blue-600 font-bold">{{ $requirement['processing_time'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-gray-700">Fee:</span>
                        <span class="text-lg {{ $requirement['fee'] === 'Free' ? 'text-green-600' : 'text-gray-800' }} font-bold">
                            {{ $requirement['fee'] }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Important Notes -->
    <div class="mt-10 bg-yellow-50 border-l-8 border-yellow-400 rounded-2xl shadow-xl p-8">
        <div class="flex items-start">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Important Notes</h3>
                <ul class="space-y-2 text-lg text-gray-700">
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>All documents must be original or certified true copies</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Bring valid government-issued ID for verification</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Processing time may vary depending on document availability</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>You can submit requests online to save time</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
