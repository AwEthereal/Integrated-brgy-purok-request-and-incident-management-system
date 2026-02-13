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
        <h2 class="text-5xl font-bold text-blue-900 mb-4">Barangay Officials</h2>
        <p class="text-2xl text-gray-700">Meet our dedicated public servants</p>
    </div>

    <!-- Barangay Officials -->
    <div class="mb-10">
        <h3 class="text-3xl font-bold text-gray-800 mb-6">Barangay Officials</h3>
        @if($officials->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($officials as $official)
                    @php
                        $roleLabels = [
                            'barangay_captain' => 'Barangay Captain',
                            'barangay_kagawad' => 'Barangay Kagawad',
                            'secretary' => 'Secretary',
                            'barangay_clerk' => 'Barangay Clerk',
                            'sk_chairman' => 'SK Chairman',
                        ];
                        $roleLabel = $roleLabels[$official->role ?? ''] ?? format_label($official->role ?? 'barangay_official');
                    @endphp
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-800">{{ $official->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $roleLabel }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                <p class="text-xl text-gray-600">No officials information available at this time.</p>
            </div>
        @endif
    </div>

    <!-- Purok Leaders -->
    <div>
        <h3 class="text-3xl font-bold text-gray-800 mb-6">Purok Leaders</h3>
        @if($purokLeaders->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($purokLeaders as $leader)
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-800">{{ $leader->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $leader->purok->name ?? 'Purok Leader' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                <p class="text-xl text-gray-600">No purok leaders information available at this time.</p>
            </div>
        @endif
    </div>
</div>
@endsection
