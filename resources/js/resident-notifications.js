document.addEventListener("DOMContentLoaded", function () {
    console.log("Resident notifications script loaded");

    // Only run on resident dashboard
    if (!document.querySelector(".resident-dashboard")) {
        console.log("Not on resident dashboard, exiting");
        return;
    }
    
    // Create and preload the audio element
    const notificationSound = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
    notificationSound.preload = 'auto';
    notificationSound.volume = 0.5; // Start with 50% volume
    
    // Track if we can play sound (will be true after first user interaction)
    let canPlaySound = false;
    
    // Function to enable sound after user interaction
    function enableSound() {
        if (!canPlaySound) {
            canPlaySound = true;
            // Try to play/pause immediately to unlock audio
            notificationSound.play().then(() => {
                notificationSound.pause();
                notificationSound.currentTime = 0;
            }).catch(e => {
                console.log('Audio playback not ready yet:', e);
            });
            // Remove the event listeners after first interaction
            document.removeEventListener('click', enableSound);
            document.removeEventListener('keydown', enableSound);
        }
    }
    
    // Enable sound on any user interaction
    document.addEventListener('click', enableSound, { once: true });
    document.addEventListener('keydown', enableSound, { once: true });

    // Get the user ID from the meta tag
    const userId = document.querySelector('meta[name="user-id"]')?.getAttribute("content");
    if (!userId) {
        console.error("User ID not found in meta tags");
        return;
    }

    console.log("Setting up WebSocket listener for user ID:", userId);

    // Wait for Echo to be fully initialized and connected
    window.waitForEcho(function(Echo) {
        console.log('Echo is ready and connected!');
        setupWebSocket();
    });

    // Function to setup the WebSocket connection
    const setupWebSocket = () => {
        try {
            if (!window.Echo) {
                console.error('Echo not available');
                return;
            }

            console.log('Setting up WebSocket connection...');
            
            // Ensure we have a valid socket ID
            const socketId = window.Echo.socketId();
            console.log('Socket ID:', socketId);
            
            if (!socketId) {
                console.error('No socket ID available, cannot subscribe to channel');
                // Try to reconnect after a delay
                setTimeout(setupWebSocket, 3000);
                return;
            }

            // Listen for request status updates on user's private channel
            const channelName = `App.Models.User.${userId}`;
            console.log('Attempting to subscribe to private channel:', channelName);
            
            // Clean up any existing channel subscription
            if (window.Echo.connector.channel(channelName)) {
                console.log('Leaving existing channel before resubscribing');
                window.Echo.leave(channelName);
            }
            
            let channel = window.Echo.private(channelName);

            // Log subscription success
            channel.subscribed(() => {
                console.log('Successfully subscribed to channel:', channelName);
                console.log('Current socket ID:', window.Echo.socketId());
            });

            // Listen for request status update events
            channel.listen('.request-status-updated', (data) => {
                console.log('Request status updated event received:', data);
                
                // Show notification
                showRequestUpdateNotification(data);
                
                // If we're on the dashboard, refresh the activity feed
                if (window.location.pathname.includes('dashboard')) {
                    setTimeout(() => {
                        refreshDashboardContent();
                    }, 1000); // Small delay to ensure database is updated
                } else {
                    // If we're not on the dashboard, just update the navigation dot
                    updateDashboardDot();
                }
            });

            // Handle subscription errors
            channel.error((error) => {
                console.error('Error subscribing to channel:', error);
                
                // Clean up the old channel before reconnecting
                if (channel) {
                    try {
                        window.Echo.leave(channelName);
                    } catch (e) {
                        console.error('Error leaving channel:', e);
                    }
                    channel = null;
                }
                
                // Attempt to resubscribe after a delay
                console.log('Will attempt to resubscribe in 3 seconds...');
                setTimeout(setupWebSocket, 3000);
            });

            // Handle connection state changes
            const pusherConnection = window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection;
            if (pusherConnection && typeof pusherConnection.on === 'function') {
                pusherConnection.on('state_change', (states) => {
                    console.log('Pusher connection state changed:', states);
                    if (states.current === 'failed' || states.current === 'unavailable') {
                        console.error(`Connection ${states.current}, attempting to reconnect...`);
                        setTimeout(setupWebSocket, 3000);
                    } else if (states.current === 'connected' && states.previous === 'disconnected') {
                        console.log('Reconnected to WebSocket, resubscribing to channel...');
                        setupWebSocket();
                    }
                });
            }

        } catch (error) {
            console.error('Error setting up WebSocket listener:', error);
            console.error('Error details:', {
                message: error.message,
                name: error.name,
                stack: error.stack
            });
            // Retry on error
            setTimeout(setupWebSocket, 5000);
        }
    };

    // Function to refresh dashboard content via AJAX
    function refreshDashboardContent() {
        console.log('Refreshing dashboard content...');
        
        // Create the fetch URL
        const fetchUrl = `${window.location.pathname}?_=${Date.now()}`;
        
        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/xhtml+xml'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            // Create a temporary div to parse the HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Find the recent activity section in the new HTML
            const newActivity = doc.querySelector('#recentActivity-content');
            const currentActivity = document.querySelector('#recentActivity-content');
            
            // Update the activity feed if it exists
            if (newActivity && currentActivity) {
                currentActivity.innerHTML = newActivity.innerHTML;
                console.log('Recent activity updated');
            }
            
            // Update the status cards
            const newCards = doc.querySelectorAll('.bg-white.rounded-lg.shadow-sm');
            const currentCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm');
            
            if (newCards.length === currentCards.length) {
                newCards.forEach((newCard, index) => {
                    if (currentCards[index]) {
                        currentCards[index].outerHTML = newCard.outerHTML;
                    }
                });
                console.log('Status cards updated');
            }
            
        })
        .catch(error => {
            console.error('Error refreshing dashboard content:', error);
            // Fallback to full page reload if AJAX fails
            window.location.reload();
        });
    }

    // Function to play notification sound
    function playNotificationSound() {
        if (!canPlaySound) {
            console.log('Sound not enabled yet - waiting for user interaction');
            return;
        }
        
        try {
            // Clone the audio element to allow multiple overlapping sounds
            const audio = notificationSound.cloneNode(true);
            audio.volume = 0.5; // Set volume to 50%
            audio.play().catch(error => {
                console.error('Error playing notification sound:', error);
                // If we get an error, try to re-enable sound for next time
                canPlaySound = false;
                enableSound();
            });
        } catch (error) {
            console.error('Error initializing audio:', error);
        }
    }

    // Function to show a notification for request updates
    function showRequestUpdateNotification(data) {
        // Play notification sound
        playNotificationSound();
        
        // Check if the browser supports notifications
        if ('Notification' in window && Notification.permission === 'granted') {
            const notificationTitle = 'Request Update';
            const notificationBody = data.message || 'Your request status has been updated';
            const notificationIcon = 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/1f4e2.png';

            try {
                const notification = new Notification(notificationTitle, {
                    body: notificationBody,
                    icon: notificationIcon
                });

                // Close the notification after 5 seconds
                setTimeout(() => {
                    notification.close();
                }, 5000);

                // Focus the window when notification is clicked
                notification.onclick = function() {
                    window.focus();
                    this.close();
                };
            } catch (error) {
                console.error('Error showing notification:', error);
            }
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission();
        }

        // Show in-app toast notification
        showToast('Request Update', data.message || 'Your request status has been updated', data.status || 'info');
    }

    // Function to show toast notification
    function showToast(title, message, status = 'info') {
        const statusColors = {
            'purok_approved': 'bg-green-600',
            'barangay_approved': 'bg-blue-600',
            'rejected': 'bg-red-600',
            'completed': 'bg-purple-600',
            'info': 'bg-gray-600'
        };
        
        const bgColor = statusColors[status] || statusColors['info'];
        
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-up`;
        
        // Add slide-up animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-slide-up {
                animation: slideUp 0.3s ease-out forwards;
            }
        `;
        if (!document.querySelector('style[data-toast-styles]')) {
            style.setAttribute('data-toast-styles', 'true');
            document.head.appendChild(style);
        }
        
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <div>
                    <div class="font-bold">${title}</div>
                    <div class="text-sm">${message}</div>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
    
    // Function to update dashboard yellow dot in navigation
    function updateDashboardDot() {
        const dashboardLink = document.querySelector('a[href*="dashboard"]');
        if (!dashboardLink || window.location.pathname.includes('dashboard')) return;
        
        // Remove existing dot
        const existingDot = dashboardLink.querySelector('.bg-yellow-500');
        if (existingDot && existingDot.parentElement) {
            existingDot.parentElement.remove();
        }
        
        // Add new dot
        const dot = document.createElement('span');
        dot.className = 'ml-2 relative inline-flex';
        dot.innerHTML = `
            <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75 animate-ping"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
        `;
        dashboardLink.appendChild(dot);
    }
});
