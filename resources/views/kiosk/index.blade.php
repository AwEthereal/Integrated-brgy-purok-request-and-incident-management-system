@extends('layouts.kiosk')

@section('content')
<div class="w-full max-w-6xl mx-auto">
    <!-- Welcome Section -->
    <div class="text-center mb-6 sm:mb-8 md:mb-12">
        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-blue-900 mb-2 sm:mb-4 px-2">Welcome to Barangay Kalawag Dos</h2>
        <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-gray-700 px-2">Touch any button below to get started</p>
    </div>

    <!-- Main Menu Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
        <!-- Barangay Information -->
        <a href="{{ route('kiosk.information') }}" 
           class="kiosk-button bg-white hover:bg-blue-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col items-center justify-center text-center group min-h-[120px] sm:min-h-[140px]">
            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-blue-100 rounded-full flex items-center justify-center mb-2 sm:mb-3 md:mb-4 group-hover:bg-blue-200 transition flex-shrink-0">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Barangay Info</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Learn about our barangay</p>
        </a>

        <!-- Services -->
        <a href="{{ route('kiosk.services') }}" 
           class="kiosk-button bg-white hover:bg-blue-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col items-center justify-center text-center group min-h-[120px] sm:min-h-[140px]">
            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-green-100 rounded-full flex items-center justify-center mb-2 sm:mb-3 md:mb-4 group-hover:bg-green-200 transition flex-shrink-0">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Services</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">View available services</p>
        </a>

        <!-- Requirements -->
        <a href="{{ route('kiosk.requirements') }}" 
           class="kiosk-button bg-white hover:bg-blue-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col items-center justify-center text-center group min-h-[120px] sm:min-h-[140px]">
            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-purple-100 rounded-full flex items-center justify-center mb-2 sm:mb-3 md:mb-4 group-hover:bg-purple-200 transition flex-shrink-0">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Requirements</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Document requirements</p>
        </a>

        <!-- Officials -->
        <a href="{{ route('kiosk.officials') }}" 
           class="kiosk-button bg-white hover:bg-blue-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col items-center justify-center text-center group min-h-[120px] sm:min-h-[140px]">
            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-2 sm:mb-3 md:mb-4 group-hover:bg-yellow-200 transition flex-shrink-0">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Officials</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Meet our officials</p>
        </a>

        <!-- Announcements -->
        <a href="{{ route('kiosk.announcements') }}" 
           class="kiosk-button bg-white hover:bg-blue-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col items-center justify-center text-center group min-h-[120px] sm:min-h-[140px] relative">
            @if(isset($hasNewAnnouncements) && $hasNewAnnouncements)
                <!-- Red Dot Indicator for New Announcements -->
                <span class="absolute top-2 right-2 sm:top-4 sm:right-4 flex h-4 w-4 sm:h-5 sm:w-5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 sm:h-5 sm:w-5 bg-red-500"></span>
                </span>
            @endif
            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-red-100 rounded-full flex items-center justify-center mb-2 sm:mb-3 md:mb-4 group-hover:bg-red-200 transition flex-shrink-0">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Announcements</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Latest news & updates</p>
        </a>

        <!-- Contact -->
        <a href="{{ route('kiosk.contact') }}" 
           class="kiosk-button bg-white hover:bg-blue-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col items-center justify-center text-center group min-h-[120px] sm:min-h-[140px]">
            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-indigo-100 rounded-full flex items-center justify-center mb-2 sm:mb-3 md:mb-4 group-hover:bg-indigo-200 transition flex-shrink-0">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Contact Us</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Get in touch</p>
        </a>
    </div>

    <!-- QR Code Section -->
    <div class="mt-6 sm:mt-8 md:mt-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-0">
            <div class="text-white flex-1 text-center md:text-left">
                <h3 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2">Access Online Services</h3>
                <p class="text-sm sm:text-base md:text-lg lg:text-xl text-blue-100 mb-4">Scan the QR code to submit requests and reports online</p>
                <a href="{{ route('kiosk.qr-code') }}" 
                   class="inline-flex items-center px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white text-blue-600 rounded-lg sm:rounded-xl font-bold text-sm sm:text-base md:text-lg lg:text-xl hover:bg-blue-50 transition">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    View QR Code
                </a>
            </div>
            <div class="md:ml-8">
                <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-white rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Home Button -->
    <div class="mt-4 sm:mt-6 md:mt-8 text-center pb-4">
        <a href="{{ route('kiosk.index') }}" 
           class="inline-flex items-center px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg sm:rounded-xl font-bold text-sm sm:text-base md:text-lg lg:text-xl transition">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Return to Home
        </a>
    </div>
</div>
@endsection
