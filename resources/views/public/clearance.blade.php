<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <title>Public Clearance Request</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="max-w-4xl mx-auto px-4 md:px-6 py-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-6 md:py-8 rounded-lg shadow-lg mb-6 md:mb-8">
        <div class="px-4 sm:px-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">Request a Purok Clearance</h1>
                <p class="text-blue-100 mt-1">Fill out your details below to submit your clearance request</p>
            </div>
            <!-- <a href="{{ route('public.landing') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-white/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>-->
        </div>
    </div>

    <form method="POST" action="{{ route('public.clearance.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
        @csrf
        <input type="text" name="website" value="" class="hidden" autocomplete="off">

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 text-sm">
                <div class="font-semibold mb-2">Please fix the following:</div>
                <ul class="list-disc pl-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Document type removed: Public page is dedicated to Purok Clearance -->
        <div class="bg-white rounded-2xl shadow-sm p-4 md:p-6 border border-gray-100">

        <div>
            <label class="block text-sm font-medium mb-1">Purpose</label>
            <input type="text" name="purpose" value="{{ old('purpose') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500" required autocomplete="off" placeholder="e.g., Job requirement, Scholarship, Travel">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Your Full Name</label>
            <input type="text" name="requester_name" value="{{ old('requester_name') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500" required autocomplete="name" placeholder="Full name">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Purok</label>
            <select name="purok_id" id="purok_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select your purok</option>
                @isset($puroks)
                    @foreach($puroks as $p)
                        <option value="{{ $p->id }}" @selected(old('purok_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                @endisset
            </select>
        </div>

        <!-- Additional details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Gender</label>
                <select name="gender" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select</option>
                    <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                    <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Age</label>
                <input type="number" name="age" min="1" max="120" inputmode="numeric" value="{{ old('age') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium mb-1">Address</label>
                <div class="relative">
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 pr-12 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Street name only" />
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">St.</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Contact Number</label>
            <input type="tel" name="contact_number" inputmode="numeric" pattern="[0-9]*" value="{{ old('contact_number') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500" required autocomplete="tel" placeholder="e.g., 09XXXXXXXXX">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email (optional)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="email" placeholder="Optional">
        </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-4 md:p-6 border border-gray-100">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4">Identity Verification</h3>
            <div class="flex items-center gap-4 mb-3">
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="verification_mode" value="id" class="h-4 w-4" @checked(old('verification_mode','id')==='id')>
                    <span class="text-sm">Upload Valid ID (Front/Back)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="verification_mode" value="face" class="h-4 w-4" @checked(old('verification_mode')==='face')>
                    <span class="text-sm">Take a Live Photo of Your Face</span>
                </label>
            </div>

            <div id="id-upload-block" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Valid ID Front</label>
                    <input type="file" name="valid_id_front" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" capture="environment" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Valid ID Back (optional)</label>
                    <input type="file" name="valid_id_back" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" capture="environment" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-xs text-gray-600">Accepted formats: JPG, PNG, or PDF. Max size 10MB each.</p>
            </div>

            <div id="face-upload-block" class="space-y-3 hidden">
                <div>
                    <label class="block text-sm font-medium mb-1">Live Face Photo</label>
                    <!-- Fallback file input (we will programmatically populate this from the camera capture) -->
                    <input type="file" name="face_photo" id="face_photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" capture="user" class="hidden">

                    <!-- Camera area (modeled after the dashboard request page) -->
                    <div id="face_camera_container" class="relative w-full max-w-md mx-auto aspect-[3/2] rounded-md border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center mb-3 sm:mb-4">
                        <video id="face_camera" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                        <canvas id="face_snapshot" class="hidden"></canvas>
                        <img id="face_preview" class="hidden w-full h-full object-contain bg-white" alt="Face Photo Preview" />
                        <div id="face_camera_placeholder" class="text-center p-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2z" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">No face photo captured</p>
                        </div>
                    </div>

                    <!-- Camera controls -->
                    <div id="face_controls" class="flex flex-wrap gap-2 justify-center mb-2">
                        <button type="button" id="face_upload_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Upload</button>
                        <button type="button" id="face_camera_btn" class="flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Take Photo</button>
                        <button type="button" id="face_switch_camera" class="hidden flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Switch</button>
                        <button type="button" id="face_capture_btn" class="hidden flex-1 sm:flex-none bg-indigo-600 py-2 px-3 sm:px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none">Capture</button>
                        <button type="button" id="face_retake_btn" class="hidden flex-1 sm:flex-none bg-white py-2 px-3 sm:px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Retake</button>
                    </div>
                </div>
                <p class="text-xs text-gray-600">Tip: Use your phone's front camera for a clear, recent face photo. Max size 10MB.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 md:gap-4 pt-2 md:pt-0 sm:flex sm:items-center sm:justify-start">
            <a href="{{ '/' . ltrim(route('public.landing', [], false), '/') }}" class="inline-flex items-center justify-center w-full sm:w-auto bg-white text-gray-700 py-2.5 md:py-3 px-5 md:px-6 rounded-lg font-semibold border border-gray-300 hover:bg-gray-50 active:bg-gray-100 transition shadow-sm text-base min-h-[42px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
            <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white py-2.5 md:py-3 px-5 md:px-8 rounded-lg font-semibold hover:bg-blue-700 active:bg-blue-800 transition shadow-lg text-base min-h-[42px]">Submit Request</button>
        </div>
    </form>
</div>
<!-- Full-screen camera overlay for face photo -->
<div id="face_camera_overlay" class="fixed inset-0 bg-black/95 z-50 hidden">
    <div class="absolute inset-0 flex flex-col">
        <video id="face_camera_fs" autoplay playsinline class="w-full h-full object-contain bg-black"></video>
        <!-- Top bar -->
        <div class="absolute top-0 left-0 right-0 p-3 flex justify-between items-center">
            <button type="button" id="face_close_overlay" class="px-3 py-2 text-white bg-black/40 rounded-md border border-white/20">Close</button>
        </div>
        <!-- Bottom controls -->
        <div class="absolute inset-x-0 bottom-0 p-5">
            <div class="relative h-12">
                <!-- Flip (icon) -->
                <button type="button" id="face_switch_camera_fs" aria-label="Flip camera" class="absolute left-5 bottom-0 p-2 rounded-full bg-black/40 border border-white/20 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path d="M7 7h7a4 4 0 014 4v.5a1 1 0 102 0V11a6 6 0 00-6-6H7.83l1.58-1.59A1 1 0 008 2.59L4.29 6.3a1 1 0 000 1.4L8 11.41A1 1 0 009.41 10L7.83 8.41H14a3 3 0 013 3v.5a1 1 0 102 0V11a5 5 0 00-5-5H7z"/>
                        <path d="M17 17H10a4 4 0 01-4-4v-.5a1 1 0 10-2 0V13a6 6 0 006 6h6.17l-1.58 1.59A1 1 0 0016 22l3.71-3.71a1 1 0 000-1.4L16 13.59A1 1 0 0014.59 15L16.17 16.59H10a3 3 0 01-3-3v-.5a1 1 0 10-2 0V13a5 5 0 00-5 5h7z"/>
                    </svg>
                </button>
                <!-- Capture (shutter icon) centered -->
                <button type="button" id="face_capture_fs" aria-label="Capture" class="absolute left-1/2 -translate-x-1/2 bottom-0 h-12 w-12 rounded-full bg-white border-2 border-white shadow-lg flex items-center justify-center">
                    <span class="block h-6 w-6 rounded-full bg-white border-2 border-gray-300"></span>
                </button>
            </div>
        </div>
    </div>
    <!-- Prevent scrolling while overlay is open on mobile -->
</div>
</body>

<!-- Purok Picker Overlay (single-bar solution) -->
<div id="purok_picker" class="fixed inset-0 z-50 hidden">
    <div id="purok_picker_bg" class="absolute inset-0 bg-black/50"></div>
    <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-xl shadow-xl max-h-[75vh] flex flex-col">
        <div class="p-3 border-b flex items-center gap-2">
            <input type="text" id="purok_picker_search" placeholder="Search purok" class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
            <button type="button" id="purok_picker_close" class="px-3 py-2 text-sm rounded-md border border-gray-300">Close</button>
        </div>
        <div id="purok_picker_list" class="p-1 overflow-auto" style="max-height:65vh"></div>
    </div>
</div>
<script>
    (function(){
        const modeRadios = document.querySelectorAll('input[name="verification_mode"]');
        const idBlock = document.getElementById('id-upload-block');
        const faceBlock = document.getElementById('face-upload-block');
        function updateVisibility(){
            const mode = document.querySelector('input[name="verification_mode"]:checked')?.value || 'id';
            if (mode === 'face') {
                idBlock.classList.add('hidden');
                faceBlock.classList.remove('hidden');
            } else {
                faceBlock.classList.add('hidden');
                idBlock.classList.remove('hidden');
            }
        }
        modeRadios.forEach(r => r.addEventListener('change', updateVisibility));
        updateVisibility();
    })();
</script>
<script>
    (function(){
        const videoInline = document.getElementById('face_camera');
        const videoOverlay = document.getElementById('face_camera_fs');
        const overlay = document.getElementById('face_camera_overlay');
        const canvas = document.getElementById('face_snapshot');
        const preview = document.getElementById('face_preview');
        const placeholder = document.getElementById('face_camera_placeholder');
        const takeBtn = document.getElementById('face_camera_btn');
        const captureBtn = document.getElementById('face_capture_btn'); // inline (fallback)
        const retakeBtn = document.getElementById('face_retake_btn');
        const switchBtn = document.getElementById('face_switch_camera'); // inline (fallback)
        const uploadBtn = document.getElementById('face_upload_btn');
        const fileInput = document.getElementById('face_photo');
        const closeOverlayBtn = document.getElementById('face_close_overlay');
        const switchOverlayBtn = document.getElementById('face_switch_camera_fs');
        const captureOverlayBtn = document.getElementById('face_capture_fs');

        let stream = null;
        let facingMode = 'user';
        let useOverlay = false;
        let video = videoInline;

        function requestUserMedia(constraints) {
            const md = (navigator && navigator.mediaDevices) ? navigator.mediaDevices : null;
            if (md && typeof md.getUserMedia === 'function') {
                return md.getUserMedia(constraints);
            }

            const legacyGetUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
            if (legacyGetUserMedia) {
                return new Promise((resolve, reject) => {
                    legacyGetUserMedia.call(navigator, constraints, resolve, reject);
                });
            }

            return Promise.reject(new Error('getUserMedia is not supported in this browser/context'));
        }

        function updateUI(state){
            if (!takeBtn || !captureBtn || !uploadBtn || !retakeBtn) return;
            switch(state){
                case 'cameraActive':
                    takeBtn.classList.add('hidden');
                    captureBtn.classList.remove('hidden');
                    uploadBtn.classList.add('hidden');
                    retakeBtn.classList.add('hidden');
                    if (switchBtn) switchBtn.classList.remove('hidden');
                    break;
                case 'previewActive':
                    takeBtn.classList.remove('hidden');
                    captureBtn.classList.add('hidden');
                    uploadBtn.classList.remove('hidden');
                    retakeBtn.classList.remove('hidden');
                    if (switchBtn) switchBtn.classList.add('hidden');
                    break;
                default:
                    takeBtn.classList.remove('hidden');
                    captureBtn.classList.add('hidden');
                    uploadBtn.classList.remove('hidden');
                    retakeBtn.classList.add('hidden');
                    if (switchBtn) switchBtn.classList.add('hidden');
            }
        }

        async function startCamera(openOverlay){
            try{
                await stopCamera();
                // Preserve overlay state unless explicitly provided
                if (openOverlay === undefined) {
                    // keep current useOverlay
                } else {
                    useOverlay = !!openOverlay;
                }
                video = useOverlay ? videoOverlay : videoInline;
                if (useOverlay && overlay){
                    overlay.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
                if (window.isSecureContext === false) {
                    throw new Error('Camera access requires HTTPS (secure context).');
                }

                stream = await requestUserMedia({
                    video: { facingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                if (video){
                    video.srcObject = stream;
                    video.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (preview) preview.classList.add('hidden');
                updateUI('cameraActive');
                await video.play();
                try{
                    const md = (navigator && navigator.mediaDevices) ? navigator.mediaDevices : null;
                    if (md && typeof md.enumerateDevices === 'function') {
                        const devices = await md.enumerateDevices();
                        const cams = devices.filter(d => d.kind === 'videoinput');
                        if (useOverlay) {
                            if (switchOverlayBtn) switchOverlayBtn.classList.toggle('hidden', cams.length <= 1);
                        } else {
                            if (switchBtn) switchBtn.classList.toggle('hidden', cams.length <= 1);
                        }
                    }
                }catch(e){}
            }catch(err){
                let msg = (err && err.message) ? err.message : 'Please check permissions';
                if (String(msg).toLowerCase().includes('https') || window.isSecureContext === false) {
                    msg = msg + ' Open this page using https:// (not http://).';
                }
                alert('Could not access the camera: ' + msg);
                updateUI('error');
                if (switchBtn) switchBtn.classList.add('hidden');
                if (switchOverlayBtn) switchOverlayBtn.classList.add('hidden');
            }
        }

        async function stopCamera(){
            if (stream){
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
        }

        function dataURLtoFile(dataUrl, filename){
            const arr = dataUrl.split(','), mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length; const u8arr = new Uint8Array(n);
            while(n--){ u8arr[n] = bstr.charCodeAt(n); }
            return new File([u8arr], filename, { type: mime });
        }

        function assignFileToInput(file){
            if (!fileInput || !file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        }

        function capture(){
            if (!video || !canvas) return;
            const vw = video.videoWidth, vh = video.videoHeight;
            if (!vw || !vh){ alert('Failed to capture photo. Please try again.'); return; }
            canvas.width = vw; canvas.height = vh;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, vw, vh);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            const file = dataURLtoFile(dataUrl, 'face.jpg');
            assignFileToInput(file);
            stopCamera();
            if (preview){ preview.src = dataUrl; preview.classList.remove('hidden'); }
            if (video){ video.classList.add('hidden'); }
            if (placeholder){ placeholder.classList.add('hidden'); }
            updateUI('previewActive');
            if (useOverlay && overlay){
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        async function switchCamera(){
            facingMode = (facingMode === 'user') ? 'environment' : 'user';
            await startCamera();
        }

        async function retake(){
            await startCamera(true);
        }

        if (takeBtn) takeBtn.addEventListener('click', () => startCamera(true));
        if (captureBtn) captureBtn.addEventListener('click', capture);
        if (retakeBtn) retakeBtn.addEventListener('click', retake);
        if (switchBtn) switchBtn.addEventListener('click', switchCamera);
        if (switchOverlayBtn) switchOverlayBtn.addEventListener('click', switchCamera);
        if (captureOverlayBtn) captureOverlayBtn.addEventListener('click', capture);
        if (closeOverlayBtn) closeOverlayBtn.addEventListener('click', async () => {
            await stopCamera();
            if (overlay){ overlay.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
            // reset state back to inline
            useOverlay = false;
            video = videoInline;
            updateUI('default');
        });
        if (uploadBtn && fileInput) uploadBtn.addEventListener('click', () => fileInput.click());
        window.addEventListener('beforeunload', stopCamera);
    })();
</script>
<script>
    // mobile-scroll-into-view (enhanced)
    (function(){
        try {
            var small = window.matchMedia && window.matchMedia('(max-width: 640px)').matches;
            if (!small) return;

            var root = document.querySelector('.max-w-4xl');
            var spacer = null;
            function addSpacer(h){
                try {
                    if (!root) return;
                    if (!spacer) { spacer = document.createElement('div'); }
                    spacer.style.height = (h||'200') + 'px';
                    spacer.style.width = '100%';
                    spacer.style.pointerEvents = 'none';
                    spacer.setAttribute('data-mobile-spacer','true');
                    if (!spacer.isConnected) root.appendChild(spacer);
                } catch(e){}
            }
            function removeSpacer(){
                try { if (spacer && spacer.isConnected) spacer.remove(); } catch(e){}
            }

            // Preferential handling for the Purok select
            var purok = document.querySelector('select[name="purok_id"]');
            if (purok) {
                var pf = function(){
                    // Skip spacer scroll if using custom overlay
                    try { if (document.documentElement && document.documentElement.dataset.purokOverlay === '1') return; } catch(e){}
                    setTimeout(function(){
                        try {
                            addSpacer(320);
                            // Bring control to top area, then nudge further for native dropdown space
                            purok.scrollIntoView({ block: 'start', behavior: 'smooth' });
                            setTimeout(function(){ try { window.scrollBy(0, -200); } catch(e){} }, 160);
                        } catch(e){}
                    }, 30);
                };
                var pc = function(){ removeSpacer(); };
                purok.addEventListener('pointerdown', pf, { passive: true });
                purok.addEventListener('touchstart', pf, { passive: true });
                purok.addEventListener('focus', pf, { passive: true });
                purok.addEventListener('click', pf, { passive: true });
                purok.addEventListener('blur', pc, { passive: true });
                purok.addEventListener('change', pc, { passive: true });
            }

            // Other inputs/selects
            var sels = document.querySelectorAll('select:not([name="purok_id"]), input[type="text"], input[type="email"], input[type="tel"], input[type="number"]');
            sels.forEach(function(el){
                var fn = function(){
                    setTimeout(function(){
                        try { el.scrollIntoView({ block: 'center', behavior: 'smooth' }); } catch(e){}
                    }, 40);
                };
                el.addEventListener('focus', fn, { passive: true });
                el.addEventListener('click', fn, { passive: true });
                el.addEventListener('blur', removeSpacer, { passive: true });
                el.addEventListener('change', removeSpacer, { passive: true });
            });
            window.addEventListener('resize', removeSpacer, { passive: true });
            window.addEventListener('orientationchange', removeSpacer, { passive: true });
        } catch(e) {}
    })();
</script>
<script>
    // Purok Picker Overlay logic (single visible bar)
    (function(){
        try {
            var select = document.getElementById('purok_id');
            if (!select) return;
            var picker = document.getElementById('purok_picker');
            var pickerBg = document.getElementById('purok_picker_bg');
            var closeBtn = document.getElementById('purok_picker_close');
            var search = document.getElementById('purok_picker_search');
            var list = document.getElementById('purok_picker_list');
            var body = document.body;
            var docEl = document.documentElement;
            function isSmall(){ return window.matchMedia && window.matchMedia('(max-width: 640px)').matches; }

            function buildList(query){
                if (!list) return;
                var q = (query||'').toString().toLowerCase().trim();
                var html = '';
                var opts = Array.from(select.options);
                opts.forEach(function(opt, idx){
                    if (idx === 0) return; // skip placeholder
                    var text = opt.text || '';
                    var show = !q || text.toLowerCase().indexOf(q) !== -1;
                    if (!show) return;
                    var selected = (opt.value === select.value);
                    html += '<button type="button" data-value="'+opt.value.replace(/"/g,'&quot;')+'" class="w-full text-left px-3 py-2 rounded-md border border-transparent hover:bg-gray-50' + (selected ? ' bg-blue-50 border-blue-200' : '') + '">'
                         +  '<span class="block text-sm">'+text.replace(/</g,'&lt;').replace(/>/g,'&gt;')+'</span>'
                         +  '</button>';
                });
                list.innerHTML = html || '<div class="px-3 py-4 text-sm text-gray-500">No matches</div>';
            }

            function openPicker(){
                try {
                    if (!isSmall()) return; // only intercept on mobile; desktop keeps native
                    docEl.dataset.purokOverlay = '1';
                    if (picker) picker.classList.remove('hidden');
                    if (body) body.classList.add('overflow-hidden');
                    if (search) { search.value = ''; search.focus(); }
                    buildList('');
                } catch(e){}
            }
            function closePicker(){
                try {
                    delete docEl.dataset.purokOverlay;
                    if (picker) picker.classList.add('hidden');
                    if (body) body.classList.remove('overflow-hidden');
                } catch(e){}
            }
            function chooseValue(val){
                try {
                    if (!select) return;
                    select.value = val;
                    // trigger change for validation/UI updates
                    var evt = new Event('change', { bubbles:true });
                    select.dispatchEvent(evt);
                    closePicker();
                } catch(e){}
            }

            // Wire events
            if (search) search.addEventListener('input', function(){ buildList(search.value); }, { passive:true });
            if (list) list.addEventListener('click', function(e){
                var btn = e.target.closest('button[data-value]');
                if (btn) chooseValue(btn.getAttribute('data-value'));
            });
            if (closeBtn) closeBtn.addEventListener('click', closePicker);
            if (pickerBg) pickerBg.addEventListener('click', closePicker);

            function intercept(e){
                if (!isSmall()) return; // allow native on desktop
                // prevent native dropdown and open overlay
                try { e.preventDefault(); e.stopPropagation(); } catch(_){}
                openPicker();
            }
            select.addEventListener('pointerdown', intercept);
            select.addEventListener('touchstart', intercept, { passive:false });
            select.addEventListener('click', intercept);

            // Cleanup flags on orientation/resize
            window.addEventListener('orientationchange', closePicker, { passive:true });
            window.addEventListener('resize', function(){ if (!isSmall()) closePicker(); }, { passive:true });
        } catch(e) {}
    })();
</script>
</html>

