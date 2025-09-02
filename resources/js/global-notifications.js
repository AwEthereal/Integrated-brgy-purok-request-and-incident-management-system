document.addEventListener('DOMContentLoaded', function() {
    // Only run if user is a purok leader or president
    if (!document.querySelector('meta[name="user-role"]') || 
        !['purok_leader', 'purok_president'].includes(document.querySelector('meta[name="user-role"]').content)) {
        return;
    }

    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationCount = document.getElementById('notification-count');
    const notificationList = document.getElementById('notification-list');
    let notificationSound = null;
    let canPlaySound = false;

    // Initialize notification sound
    function initNotificationSound() {
        notificationSound = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
        notificationSound.volume = 0.5;
        
        // Enable sound on first user interaction
        const enableSound = () => {
            if (!canPlaySound) {
                canPlaySound = true;
                // Just prepare the sound, don't play it yet
                const playPromise = notificationSound.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.log('Audio play failed (expected on page load):', error);
                    });
                }
                notificationSound.pause();
                notificationSound.currentTime = 0;
            }
        };
        
        // Try to enable sound on page load
        enableSound();
        
        // Also enable on first user interaction
        const enableOnInteraction = () => {
            if (!canPlaySound) {
                enableSound();
                document.removeEventListener('click', enableOnInteraction);
                document.removeEventListener('keydown', enableOnInteraction);
            }
        };
        
        document.addEventListener('click', enableOnInteraction, { once: true });
        document.addEventListener('keydown', enableOnInteraction, { once: true });
    }

    // Play notification sound
    function playNotificationSound() {
        if (!canPlaySound || !notificationSound) {
            console.log('Sound not ready to play');
            return;
        }
        
        try {
            const sound = new Audio('/sounds/810191__mokasza__notification-chime.mp3');
            sound.volume = 0.5;
            
            // Try to play the sound
            const playPromise = sound.play();
            
            // Handle promise rejection (common on page load)
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log('Could not play sound:', error);
                    // Try one more time with user interaction
                    const playAfterInteraction = () => {
                        sound.play().catch(e => console.log('Still could not play sound:', e));
                        document.removeEventListener('click', playAfterInteraction);
                    };
                    document.addEventListener('click', playAfterInteraction, { once: true });
                });
            }
        } catch (error) {
            console.error('Error playing sound:', error);
        }
    }

    // Update notification count
    function updateNotificationCount(count) {
        if (!notificationCount) return;
        
        if (count > 0) {
            notificationCount.textContent = count > 9 ? '9+' : count;
            notificationCount.classList.remove('hidden');
            document.title = `(${count}) ${document.title.replace(/\(\d+\)\s*/, '')}`;
        } else {
            notificationCount.classList.add('hidden');
            document.title = document.title.replace(/\(\d+\)\s*/, '');
        }
    }

    // Add a new notification
    function addNotification(notification) {
        if (!notificationList) return;
        
        const noNotifications = notificationList.querySelector('.text-gray-500');
        if (noNotifications) {
            notificationList.innerHTML = '';
        }
        
        const notificationElement = document.createElement('div');
        notificationElement.className = 'px-4 py-3 hover:bg-gray-50 border-b border-gray-100';
        notificationElement.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-0.5">
                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${notification.title || 'New Notification'}</p>
                    <p class="text-sm text-gray-500">${notification.message || ''}</p>
                    <p class="text-xs text-gray-400 mt-1">Just now</p>
                </div>
            </div>
        `;
        
        notificationList.insertBefore(notificationElement, notificationList.firstChild);
    }

    // Toggle notification dropdown
    if (notificationBell && notificationDropdown) {
        notificationBell.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (notificationDropdown && !notificationDropdown.contains(e.target) && 
            notificationBell && !notificationBell.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });

    // Listen for WebSocket notifications
    console.log('Global notifications script loaded');
    window.waitForEcho(function(Echo) {
        console.log('Echo is ready and connected!');
        const purokId = document.querySelector('meta[name="purok-id"]')?.content;
        console.log('Purok ID from meta:', purokId);

        if (purokId) {
            console.log('Setting up WebSocket listeners for purok channel:', `purok.${purokId}`);

            // Debug Echo/Pusher connection
            if (Echo.connector && Echo.connector.pusher) {
                const pusherConn = Echo.connector.pusher.connection;
                console.log('Pusher connection state:', pusherConn.state);
                if (typeof pusherConn.on === 'function') {
                    pusherConn.on('connected', () => {
                        console.log('Pusher connection event: connected, socket ID:', Echo.socketId());
                    });
                    pusherConn.on('disconnected', () => {
                        console.log('Pusher connection event: disconnected');
                    });
                } else if (typeof pusherConn.bind === 'function') {
                    pusherConn.bind('connected', () => {
                        console.log('Pusher connection event: connected, socket ID:', Echo.socketId());
                    });
                    pusherConn.bind('disconnected', () => {
                        console.log('Pusher connection event: disconnected');
                    });
                } else {
                    console.warn('Pusher connection object does not support .on or .bind');
                }
            }

            try {
                // Listen for new purok clearance requests
                const channel = Echo.private(`purok.${purokId}`);
                console.log('Channel created:', channel);

                channel.listen('.request-status-updated', (data) => {
                    console.log('Received notification:', data);

                    // Play sound
                    playNotificationSound();

                    // Update notification count
                    const count = parseInt(notificationCount.textContent || '0') + 1;
                    updateNotificationCount(count);

                    // Add notification
                    addNotification({
                        title: 'New Purok Clearance Request',
                        message: `A new request has been submitted to your purok.`
                    });
                });

                console.log('Added listener for request-status-updated');

                // Listen for purok change requests
                channel.listen('.purok-change-request', (data) => {
                    console.log('Received purok change request:', data);

                    // Play sound
                    playNotificationSound();

                    // Update notification count
                    const count = parseInt(notificationCount.textContent || '0') + 1;
                    updateNotificationCount(count);

                    // Add notification
                    addNotification({
                        title: 'New Purok Change Request',
                        message: `${data.user_name} has requested to change purok.`
                    });
                });

                console.log('Added listener for purok-change-request');

            } catch (error) {
                console.error('Error setting up WebSocket listeners:', error);
            }
        } else {
            console.error('No purok ID found in meta tags');
        }
    });

    // Initialize
    initNotificationSound();
});
