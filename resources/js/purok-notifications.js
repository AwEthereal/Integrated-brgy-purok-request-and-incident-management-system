document.addEventListener('DOMContentLoaded', function() {
    console.log('Purok notifications script loaded');
    
    // Only run on purok leader dashboard
    if (!document.querySelector('.purok-leader-dashboard')) {
        console.log('Not on purok leader dashboard, exiting');
        return;
    }

    // Check if Echo is available
    if (typeof window.Echo === 'undefined') {
        console.error('Laravel Echo is not properly initialized');
        return;
    }

    // Get the purok ID from the meta tag
    const purokId = document.querySelector('meta[name="purok-id"]')?.getAttribute('content');
    
    if (!purokId) {
        console.error('Purok ID not found in meta tags');
        return;
    }

    console.log('Setting up WebSocket listener for purok ID:', purokId);

    try {
        // Listen for new request events
        const channel = window.Echo.private(`purok.${purokId}`);
        
        console.log('Subscribed to channel:', `purok.${purokId}`);
        
        channel
            .listen('.new-request', (data) => {
                console.log('New request event received:', data);
                
                // Update the pending requests count
                const pendingBadge = document.querySelector('.pending-requests-badge');
                if (pendingBadge) {
                    console.log('Updating pending requests count to:', data.requestCount);
                    pendingBadge.textContent = data.requestCount;
                    
                    // Add animation
                    pendingBadge.classList.add('animate-ping');
                    setTimeout(() => {
                        pendingBadge.classList.remove('animate-ping');
                    }, 1000);
                } else {
                    console.warn('Pending requests badge not found in the DOM');
                }
                
                // Show a notification
                showNewRequestNotification(data.requestCount);
            })
            .error((error) => {
                console.error('Error subscribing to channel:', error);
            });
            
        // Log connection status
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('Successfully connected to WebSockets server');
        });
        
        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('WebSocket connection error:', error);
        });
    } catch (error) {
        console.error('Error setting up WebSocket listener:', error);
    }

    // Function to show a notification
    function showNewRequestNotification(count) {
        console.log('Showing notification for', count, 'pending requests');
        
        // Check if notifications are supported
        if (!('Notification' in window)) {
            console.log('This browser does not support desktop notifications');
            return;
        }
        
        // Check if notification permission is granted
        if (Notification.permission === 'granted') {
            try {
                const notification = new Notification('New Request Received', {
                    body: `You have ${count} pending request${count !== 1 ? 's' : ''} for approval`,
                    icon: '/images/notification-icon.png'
                });
                
                notification.onclick = function() {
                    window.focus();
                    this.close();
                };
                
            } catch (error) {
                console.error('Error showing notification:', error);
            }
        } else if (Notification.permission !== 'denied') {
            // Request permission if not already asked
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    try {
                        const notification = new Notification('New Request Received', {
                            body: `You have ${count} pending request${count !== 1 ? 's' : ''} for approval`,
                            icon: '/images/notification-icon.png'
                        });
                        
                        notification.onclick = function() {
                            window.focus();
                            this.close();
                        };
                    } catch (error) {
                        console.error('Error showing notification after permission granted:', error);
                    }
                }
            }).catch(error => {
                console.error('Error requesting notification permission:', error);
            });
        }
        
        // Also show an in-app notification
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm z-50 border-l-4 border-blue-500';
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-0.5">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">New Request Received</p>
                    <p class="mt-1 text-sm text-gray-500">You have ${count} pending request${count > 1 ? 's' : ''} for approval</p>
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
        const closeButton = notification.querySelector('button');
        closeButton.addEventListener('click', () => {
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
