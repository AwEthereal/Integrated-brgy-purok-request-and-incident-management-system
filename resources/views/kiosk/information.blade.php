@extends('layouts.kiosk')

@section('content')
<div class="max-w-5xl mx-auto">
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
        <h2 class="text-5xl font-bold text-blue-900 mb-4">Barangay Information</h2>
        <p class="text-2xl text-gray-700">Learn more about Barangay Kalawag Dos</p>
    </div>

    <!-- Information Cards -->
    <div class="space-y-8">
        <!-- About Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-start">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">About Us</h3>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        Barangay Kalawag Dos is a progressive community committed to providing excellent services to its residents. 
                        We strive to maintain peace, order, and development in our barangay through efficient governance and community participation.
                    </p>
                </div>
            </div>
        </div>

        <!-- Vision & Mission -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Vision -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center mb-4">
                    <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <h3 class="text-3xl font-bold">Vision</h3>
                </div>
                <p class="text-xl leading-relaxed">
                    A peaceful, progressive, and sustainable barangay where every resident enjoys quality life, 
                    participates actively in governance, and contributes to community development.
                </p>
            </div>

            <!-- Mission -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center mb-4">
                    <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    <h3 class="text-3xl font-bold">Mission</h3>
                </div>
                <p class="text-xl leading-relaxed">
                    To deliver responsive, transparent, and efficient public services through innovative programs, 
                    strong partnerships, and active community engagement.
                </p>
            </div>
        </div>

        <!-- Location & Contact -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h3 class="text-3xl font-bold text-gray-800 mb-6">Location & Office Hours</h3>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <div class="flex items-start mb-4">
                        <svg class="w-8 h-8 text-blue-600 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Address</h4>
                            <p class="text-lg text-gray-700">Barangay Kalawag Dos<br>Sangguniang Pambarangay ng Kalawag Ⅱ, General Siongco Street, Kalawag II, Isulan, Sultan Kudarat, Soccsksargen, 9805, Philippines</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-start">
                        <svg class="w-8 h-8 text-blue-600 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Office Hours</h4>
                            <p class="text-lg text-gray-700">
                                Monday - Friday<br>
                                8:00 AM - 5:00 PM<br>
                                <span class="text-red-600 font-semibold">Closed on weekends and holidays</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
