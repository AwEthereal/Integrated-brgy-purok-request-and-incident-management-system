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
        <h2 class="text-5xl font-bold text-blue-900 mb-4">Contact Us</h2>
        <p class="text-2xl text-gray-700">Get in touch with us</p>
    </div>

    <!-- Contact Information -->
    <div class="grid md:grid-cols-2 gap-8 mb-10">
        <!-- Address -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-start">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Office Address</h3>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        Barangay Kalawag Dos<br>
                        Sangguniang Pambarangay ng Kalawag Ⅱ, General Siongco Street, Kalawag II, Isulan, Sultan Kudarat
                    </p>
                </div>
            </div>
        </div>

        <!-- Phone -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-start">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Phone Number</h3>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        (02) 1234-5678<br>
                        0917-123-4567
                    </p>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-start">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Email Address</h3>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        barangay.kalawagdos@pasig.gov.ph<br>
                        info@kalawagdos.gov.ph
                    </p>
                </div>
            </div>
        </div>

        <!-- Office Hours -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-start">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Office Hours</h3>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        Monday - Friday<br>
                        8:00 AM - 5:00 PM<br>
                        <span class="text-red-600 font-semibold">Closed on weekends and holidays</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media / Website -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white text-center">
        <h3 class="text-3xl font-bold mb-4">Visit Our Website</h3>
        <p class="text-xl mb-6">Access online services and submit requests</p>
        <div class="text-3xl font-mono bg-white text-blue-600 rounded-xl py-4 px-6 inline-block">
            {{ config('app.url') }}
        </div>
    </div>
</div>
@endsection
