// Function to play notification sound
function playNotificationSound() {
    const audio = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
    audio.play().catch(error => {
        console.error('Error playing notification sound:', error);
    });
}

// Listen for new request events
if (window.Echo) {
    // Listen for new request event
    window.Echo.private(`purok.${window.Laravel.user?.purok_id}`)
        .listen('NewRequestCreated', (e) => {
            // Play sound when a new request is received
            playNotificationSound();
            
            // Show notification (using toastr as an example)
            if (typeof toastr !== 'undefined') {
                toastr.success('New request received!', 'New Request', {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right'
                });
            }
            
            // You can also update the UI here if needed
            updateRequestCount();
        });
}

// Function to update request count (if you have a counter)
function updateRequestCount() {
    // Example: Update a counter in the navbar
    const counterElement = document.getElementById('pending-requests-count');
    if (counterElement) {
        const currentCount = parseInt(counterElement.textContent) || 0;
        counterElement.textContent = currentCount + 1;
        counterElement.classList.remove('hidden');
    }
}

// Make the function available globally
window.playNotificationSound = playNotificationSound;
