@extends('layouts.kiosk')

@section('content')
<div class="max-w-4xl mx-auto">
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
        <h2 class="text-5xl font-bold text-blue-900 mb-4">Access Online Services</h2>
        <p class="text-2xl text-gray-700">Scan the QR code with your smartphone</p>
    </div>

    <!-- QR Code Display -->
    <div class="bg-white rounded-2xl shadow-2xl p-12">
        <div class="text-center">
            <!-- QR Code -->
            <div class="inline-block bg-white p-8 rounded-2xl shadow-lg mb-8">
                <div class="flex items-center justify-center">
                    {!! $qrCodeSvg !!}
                </div>
            </div>

            <!-- Instructions -->
            <div class="space-y-8">
                <div class="bg-blue-50 rounded-xl p-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">How to Use</h3>
                    <div class="grid md:grid-cols-3 gap-4 text-left">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 font-bold text-xl">
                                1
                            </div>
                            <div>
                                <p class="text-lg text-gray-700">Open your phone's camera or QR scanner app</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 font-bold text-xl">
                                2
                            </div>
                            <div>
                                <p class="text-lg text-gray-700">Point your camera at the QR code above</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 font-bold text-xl">
                                3
                            </div>
                            <div>
                                <p class="text-lg text-gray-700">Tap the notification to open the website</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Website URL -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
                    <p class="text-lg mb-2">Or visit us directly at:</p>
                    <p class="text-3xl font-bold font-mono">{{ $websiteUrl }}</p>
                </div>

                <!-- Available Services -->
                <div class="bg-green-50 rounded-xl p-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Available Online Services</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-left">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-lg text-gray-700">Submit Purok Clearance Requests</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-lg text-gray-700">Report Incidents</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-lg text-gray-700">Send Feedback</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-lg text-gray-700">View Announcements</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // You can add QR code generation library here
    // Example: using qrcode.js or similar library
    // const qr = new QRCode(document.getElementById("qrcode"), {
    //     text: "{{ $websiteUrl }}",
    //     width: 320,
    //     height: 320
    // });
</script>
@endpush
@endsection
