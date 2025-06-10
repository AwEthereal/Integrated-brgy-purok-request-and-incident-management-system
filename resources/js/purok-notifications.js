document.addEventListener("DOMContentLoaded", function () {
    console.log("Purok notifications script loaded");

    // Only run on purok leader dashboard
    if (!document.querySelector(".purok-leader-dashboard")) {
        console.log("Not on purok leader dashboard, exiting");
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

    // Check if Echo is available
    if (typeof window.Echo === "undefined") {
        console.error("Laravel Echo is not properly initialized");
        return;
    }
    
    // Get the purok ID from the meta tag
    const purokId = document.querySelector('meta[name="purok-id"]')?.getAttribute("content");
    if (!purokId) {
        console.error("Purok ID not found in meta tags");
        return;
    }
    
    console.log("Setting up WebSocket listener for purok ID:", purokId);
    
    // Wait for Echo to be fully initialized
    const waitForEcho = setInterval(() => {
        if (window.Echo.connector.pusher.connection.state === 'connected') {
            clearInterval(waitForEcho);
            setupWebSocket();
        } else if (window.Echo.connector.pusher.connection.state === 'failed') {
            clearInterval(waitForEcho);
            console.error('Failed to connect to WebSocket server');
        }
    }, 100);
    
    // Set a timeout in case the connection never succeeds
    setTimeout(() => {
        clearInterval(waitForEcho);
        if (window.Echo.connector.pusher.connection.state !== 'connected') {
            console.error('Timed out waiting for WebSocket connection');
            // Try to reconnect
            setupWebSocket();
        }
    }, 5000);

    console.log("Setting up WebSocket listener for purok ID:", purokId);

    // Function to update the pending requests badge and card
    function updatePendingBadge(count) {
        console.log('Updating pending badge to:', count);
        
        // Update all pending request badges
        document.querySelectorAll('.pending-requests-badge').forEach(badge => {
            badge.textContent = count;
            // Add ping animation
            badge.classList.add('animate-ping', 'opacity-75');
            setTimeout(() => {
                badge.classList.remove('animate-ping', 'opacity-75');
            }, 1000);
        });
        
        // Update the pending requests card count if it exists
        const pendingCard = document.querySelector('.pending-requests-card .text-3xl.font-bold');
        if (pendingCard) {
            pendingCard.textContent = count;
        }
        
        // Update browser tab title if there are pending requests
        if (count > 0) {
            document.title = `(${count}) ${document.title.replace(/\(\d+\)\s*/, '')}`;
        } else {
            document.title = document.title.replace(/\(\d+\)\s*/, '');
        }
    }

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

            // Ensure Echo connector is properly initialized
            if (!window.Echo.connector || !window.Echo.connector.pusher || !window.Echo.connector.pusher.config) {
                console.error('Echo connector not properly initialized');
                setTimeout(setupWebSocket, 3000);
                return;
            }

            // Initialize auth headers if they don't exist
            if (!window.Echo.connector.pusher.config.auth) {
                window.Echo.connector.pusher.config.auth = {};
            }
            if (!window.Echo.connector.pusher.config.auth.headers) {
                window.Echo.connector.pusher.config.auth.headers = {};
            }

            // Get the CSRF token for authentication
            const token = document.head.querySelector('meta[name="csrf-token"]');
            if (token && token.content) {
                window.Echo.connector.pusher.config.auth.headers['X-CSRF-TOKEN'] = token.content;
                console.log('CSRF token set for WebSocket connection');
            } else {
                console.warn('CSRF token not found in meta tags');
            }

            // Listen for new request events
            const channelName = `purok.${purokId}`;
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
                
                // Update the badge immediately after subscription
                const pendingCount = document.querySelector('.pending-requests-badge')?.textContent;
                if (pendingCount) {
                    updatePendingBadge(pendingCount);
                }
            });

            // Listen for the specific event
            channel.listen('.new-request', (data) => {
                console.log('New request event received:', data);
                if (data && typeof data.requestCount !== 'undefined') {
                    // Update the badges and card
                    updatePendingBadge(data.requestCount);
                    
                    // Show desktop notification
                    showNewRequestNotification(data.requestCount);
                    
                    // If we're on the dashboard, refresh the approvals table
                    if (window.location.pathname.includes('dashboard')) {
                        refreshApprovalsTable();
                    } else {
                        // If we're not on the dashboard, refresh the entire page
                        window.location.reload();
                    }
                } else {
                    console.warn('Received new-request event with invalid data:', data);
                }
            });
            
            // Listen for request status updates
            channel.listen('.request-status-updated', (data) => {
                console.log('Request status updated event received:', data);
                // Refresh the approvals table when a request status is updated
                if (window.location.pathname.includes('dashboard')) {
                    refreshApprovalsTable();
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
            const connection = window.Echo.connector.pusher.connection;
            connection.bind('state_change', (states) => {
                console.log('Connection state changed:', states);
                
                if (states.current === 'failed' || states.current === 'unavailable') {
                    console.error(`Connection ${states.current}, attempting to reconnect...`);
                    setTimeout(setupWebSocket, 3000);
                }
                // If reconnected, resubscribe to the channel
                else if (states.current === 'connected' && states.previous === 'disconnected') {
                    console.log('Reconnected to WebSocket, resubscribing to channel...');
                    setupWebSocket();
                }
            });

            // Log all events for debugging
            channel.listen('*', (event, data) => {
                console.log('Received event on channel:', event, data);
            });

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

    // Setup connection status listeners
    const initializeWebSocket = () => {
        if (window.Echo && window.Echo.connector) {
            // If we have a socket connection
            if (window.Echo.connector.socket) {
                window.Echo.connector.socket.on("connect", () => {
                    console.log("Successfully connected to WebSockets server");
                    setupWebSocket();
                });

                window.Echo.connector.socket.on("error", (error) => {
                    console.error("WebSocket connection error:", error);
                });

                // If already connected, setup the WebSocket
                if (window.Echo.connector.socket.connected) {
                    setupWebSocket();
                }
            } else {
                // Try to initialize after a delay if socket is not available yet
                console.log("Waiting for WebSocket connection...");
                setTimeout(initializeWebSocket, 1000);
            }
        } else {
            console.warn("Echo not properly initialized yet, retrying...");
            setTimeout(initializeWebSocket, 1000);
        }
    };

    // Start the WebSocket initialization process
    initializeWebSocket();

    // Function to refresh the approvals table via AJAX
    function refreshApprovalsTable() {
        console.log('Refreshing approvals table...');
        
        // Get the current URL and any query parameters
        const currentUrl = new URL(window.location.href);
        const params = new URLSearchParams(currentUrl.search);
        
        // Add a timestamp to prevent caching
        params.set('_', Date.now());
        
        // Create the fetch URL
        const fetchUrl = `${window.location.pathname}?${params.toString()}`;
        
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
            
            // Find the approvals table in the new HTML
            const newTable = doc.querySelector('table');
            const currentTable = document.querySelector('table');
            
            // Find the pagination container
            const newPagination = doc.querySelector('.pagination');
            const currentPagination = document.querySelector('.pagination');
            
            // Update the table if it exists
            if (newTable && currentTable) {
                currentTable.parentNode.replaceChild(
                    document.importNode(newTable, true),
                    currentTable
                );
                console.log('Approvals table updated');
            }
            
            // Update pagination if it exists
            if (newPagination && currentPagination) {
                currentPagination.parentNode.replaceChild(
                    document.importNode(newPagination, true),
                    currentPagination
                );
                console.log('Pagination updated');
            }
            
            // Update the active filter badge if it exists
            const newFilterBadge = doc.querySelector('.mb-4.flex.items-center');
            const currentFilterBadge = document.querySelector('.mb-4.flex.items-center');
            
            if (newFilterBadge && currentFilterBadge) {
                currentFilterBadge.outerHTML = newFilterBadge.outerHTML;
            } else if (newFilterBadge) {
                // If there's a new filter badge but none currently, insert it
                const container = document.querySelector('.container.mx-auto.px-4.py-8');
                if (container) {
                    container.insertBefore(
                        document.importNode(newFilterBadge, true),
                        container.firstChild
                    );
                }
            } else if (currentFilterBadge) {
                // If there's a current filter badge but none in the new content, remove it
                currentFilterBadge.remove();
            }
            
        })
        .catch(error => {
            console.error('Error refreshing approvals table:', error);
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

    // Function to show a notification
    function showNewRequestNotification(count) {
        // Play notification sound
        playNotificationSound();
        
        // Check if the browser supports notifications
        if (!('Notification' in window)) {
            console.log('This browser does not support desktop notification');
            return;
        }

        // Use a fallback icon from a CDN if local one is not found
        const notificationIcon = 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/1f4e2.png';
        const notificationTitle = 'New Request Submitted';
        const notificationBody = `You have ${count} pending request${count > 1 ? 's' : ''} to review`;

        // Function to create and show the notification
        const showNotification = () => {
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
        };


        // Check notification permission and show notification
        if (Notification.permission === 'granted') {
            showNotification();
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    showNotification();
                }
            }).catch(error => {
                console.error('Error requesting notification permission:', error);
            });
        }

        // Also show an in-app notification
        const notification = document.createElement("div");
        notification.className =
            "fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm z-50 border-l-4 border-blue-500 animate-fade-in-up";
        
        // Add fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.3s ease-out forwards;
            }
        `;
        document.head.appendChild(style);
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-0.5">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">New Request Received</p>
                    <p class="mt-1 text-sm text-gray-500">You have ${count} pending request${
            count > 1 ? "s" : ""
        } for approval</p>
                </div>
                <button type="button" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;

        // Add close button functionality
        const closeButton = notification.querySelector("button");
        closeButton.addEventListener("click", () => {
            notification.remove();
        });

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification && notification.parentNode) {
                notification.remove();
            }
        }, 5000);

        // Add to the page
        document.body.appendChild(notification);
    }
});
