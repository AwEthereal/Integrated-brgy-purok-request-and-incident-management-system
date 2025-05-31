<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Report</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#bbf7d0] min-h-screen flex items-center justify-center py-6 px-4">

    <form action="{{ route('incident_reports.store') }}" method="POST" enctype="multipart/form-data"
        class="w-full max-w-xl bg-white p-6 md:p-8 rounded-2xl shadow-lg space-y-6">
        @csrf

        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 border-b pb-2">Submit Incident Report</h2>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="4" required
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
        </div>

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="photo_data" id="photo_data">

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input type="text" name="location" id="location"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                readonly>
            <a id="map-link" href="#" target="_blank"
                class="text-sm text-blue-600 mt-1 underline hidden">View on Google Maps</a>

            <iframe id="gmap-iframe"
                class="w-full mt-2 rounded-md border border-gray-300 hidden"
                width="100%" height="250"
                style="border:0;"
                allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Take a Photo</label>

            <video id="camera" autoplay playsinline
                class="w-full aspect-video rounded-md border border-gray-300"></video>

            <canvas id="snapshot" class="hidden"></canvas>

            <img id="photo-preview" class="hidden w-full mt-2 rounded-md border border-gray-300" alt="Photo preview" />

            <div class="flex gap-2 mt-2">
                <button type="button" onclick="flipCamera()" id="flipButton"
                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">Flip Camera</button>
                <button type="button" onclick="takePhoto()" id="photoButton"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Take Photo</button>
            </div>

            <p id="photo-status" class="text-sm text-green-600 mt-2 hidden">Photo captured!</p>
        </div>

        <div>
            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Or Upload a Photo</label>
            <input type="file" name="photo" id="photo" accept="image/*"
                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200"
                onchange="disableCamera()" />

            <button type="button" onclick="enableCameraAgain()" id="reEnableBtn"
                class="hidden mt-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Use Camera Again</button>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 mt-6">
            <a href="{{ route('dashboard') }}"
                class="w-full sm:w-1/2 text-center bg-gray-300 text-gray-800 py-3 px-6 rounded-lg font-semibold hover:bg-gray-400 transition">
                ← Back
            </a>
            <button type="submit"
                class="w-full sm:w-1/2 bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition">
                Submit Report
            </button>
        </div>

    </form>

    <script>
        const video = document.getElementById('camera');
        const canvas = document.getElementById('snapshot');
        const photoInput = document.getElementById('photo_data');
        const fileInput = document.getElementById('photo');
        const flipButton = document.getElementById('flipButton');
        const photoButton = document.getElementById('photoButton');
        const reEnableBtn = document.getElementById('reEnableBtn');
        const photoPreview = document.getElementById('photo-preview');
        let stream;
        let useFront = true;

        function startCamera() {
            const constraints = {
                video: { facingMode: useFront ? 'user' : 'environment' }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(s => {
                    stream = s;
                    video.srcObject = stream;
                    video.classList.remove('hidden');
                    flipButton.disabled = false;
                    photoButton.disabled = false;
                })
                .catch(() => alert('Camera access denied or not available'));
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }

        function flipCamera() {
            useFront = !useFront;
            stopCamera();
            startCamera();
        }

        function takePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataURL = canvas.toDataURL('image/jpeg');

            photoInput.value = dataURL;

            photoPreview.src = dataURL;
            photoPreview.classList.remove('hidden');

            fileInput.disabled = true;
            fileInput.classList.add('opacity-50', 'cursor-not-allowed');

            document.getElementById('photo-status').textContent = "Photo captured!";
            document.getElementById('photo-status').classList.remove('hidden');
        }

        function disableCamera() {
            photoInput.value = '';
            photoPreview.classList.add('hidden');

            stopCamera();
            video.classList.add('hidden');

            photoButton.disabled = true;
            photoButton.classList.add('opacity-50', 'cursor-not-allowed');
            flipButton.disabled = true;

            reEnableBtn.classList.remove('hidden');
        }

        function enableCameraAgain() {
            fileInput.value = '';
            fileInput.disabled = false;
            fileInput.classList.remove('opacity-50', 'cursor-not-allowed');

            reEnableBtn.classList.add('hidden');
            document.getElementById('photo-status').classList.add('hidden');
            startCamera();
        }

        document.addEventListener('DOMContentLoaded', function () {
            startCamera();

            const latInput = document.getElementById('latitude');
            const lonInput = document.getElementById('longitude');
            const locationInput = document.getElementById('location');
            const gmapLink = document.getElementById('map-link');
            const gmapIframe = document.getElementById('gmap-iframe');

            if (!navigator.geolocation) {
                locationInput.value = 'Geolocation not supported';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lon = position.coords.longitude.toFixed(6);

                    latInput.value = lat;
                    lonInput.value = lon;

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(res => res.json())
                        .then(data => {
                            locationInput.value = data.display_name || 'Location not found';
                        })
                        .catch(() => {
                            locationInput.value = 'Error fetching location';
                        });

                    gmapLink.href = `https://www.google.com/maps?q=${lat},${lon}`;
                    gmapLink.classList.remove('hidden');

                    gmapIframe.src = `https://www.google.com/maps?q=${lat},${lon}&z=17&output=embed`;
                    gmapIframe.classList.remove('hidden');
                },
                function () {
                    locationInput.value = 'Permission denied or unavailable';
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    </script>

</body>
</html>
