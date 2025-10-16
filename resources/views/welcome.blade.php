<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Kalawag II – AKSYON AGAD!</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Video Background */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .video-background video {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.85) 0%, rgba(29, 78, 216, 0.90) 100%);
            z-index: -1;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        /* Glass morphism effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Hover effects */
        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="min-h-screen font-[Inter] overflow-x-hidden">

    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('videos/EditedAnglesFade.mp4') }}" type="video/mp4">
        </video>
    </div>
    <div class="video-overlay"></div>

    <!-- Header -->
    <header class="relative z-10 bg-white/95 backdrop-blur-sm shadow-lg py-4 px-6 animate-fade-in">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="text-center md:text-left">
                    <h1 class="text-xl md:text-2xl font-bold text-blue-600">Barangay Kalawag II</h1>
                    <p class="text-xs md:text-sm text-gray-600">Integrated Management System</p>
                </div>
            </div>

            @if (Route::has('login'))
                <nav class="flex flex-wrap justify-center md:justify-end gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-medium">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 text-blue-600 hover:text-blue-800 transition-all font-medium">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-medium">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main class="relative z-10 flex-grow">
        <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
            <!-- Hero Content -->
            <div class="text-center space-y-8 mb-16 animate-fade-in-up">
                <div class="space-y-4">
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight drop-shadow-lg">
                        KAYANG-KAYA<br>
                        <span class="text-yellow-300">BASTA'T SAMA-SAMA!</span>
                    </h1>
                    <h2 class="text-2xl md:text-4xl font-bold text-white drop-shadow-lg">
                        SA BARANGAY KALAWAG II,<br>
                        <span class="text-yellow-300">AKSYON AGAD!</span>
                    </h2>
                </div>

                <p class="text-lg md:text-xl text-white/90 max-w-3xl mx-auto drop-shadow-md">
                    Welcome to the official online portal of Barangay Kalawag II.<br>
                    Access services and submit reports quickly, anytime, anywhere.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap justify-center gap-4 pt-4">
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-yellow-400 text-blue-900 rounded-lg hover:bg-yellow-300 transition-all shadow-xl hover:shadow-2xl font-bold text-lg">
                            Register Now
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all shadow-xl backdrop-blur-sm font-bold text-lg border-2 border-white/30">
                            Sign In
                        </a>
                    @endguest
                </div>
            </div>

            <!-- Quick Access Services -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 animate-fade-in-up delay-200">
                <div class="service-card bg-white/95 backdrop-blur-sm rounded-xl p-6 shadow-xl">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Document Requests</h3>
                    <p class="text-gray-600 mb-4">Request barangay clearance, certificates, and other documents online.</p>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center">
                        Learn more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div class="service-card bg-white/95 backdrop-blur-sm rounded-xl p-6 shadow-xl">
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Report Incidents</h3>
                    <p class="text-gray-600 mb-4">Report emergencies, concerns, and incidents in your community.</p>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center">
                        Learn more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div class="service-card bg-white/95 backdrop-blur-sm rounded-xl p-6 shadow-xl">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Track Requests</h3>
                    <p class="text-gray-600 mb-4">Monitor the status of your document requests and reports in real-time.</p>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center">
                        Learn more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-20 text-center animate-fade-in-up delay-300">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 drop-shadow-lg">Why Choose Our System?</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-12">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Efficiency</h3>
                        <p class="text-white/80 text-sm">Streamlined processes for faster service</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Secure</h3>
                        <p class="text-white/80 text-sm">Your data is protected and encrypted</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Transparency</h3>
                        <p class="text-white/80 text-sm">Track and monitor your requests in real-time</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Easy to Use</h3>
                        <p class="text-white/80 text-sm">Simple and intuitive interface</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 bg-white/95 backdrop-blur-sm shadow-inner py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Barangay Kalawag II</h3>
                    <p class="text-gray-600 text-sm">Serving our community with efficiency and transparency.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-600 hover:text-blue-600">Register</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Contact</h3>
                    <p class="text-gray-600 text-sm">For inquiries, please visit the barangay hall or contact your purok leader.</p>
                </div>
            </div>
            <div class="border-t border-gray-200 pt-6">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Barangay Kalawag II. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
