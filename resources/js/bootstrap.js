import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Setup axios defaults
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    const csrfToken = token.getAttribute('content');
    // Set Axios default headers
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    
    // Log CSRF token status
    if (!csrfToken) {
        console.warn('CSRF token found but empty. Please ensure meta[name="csrf-token"] has a valid token.');
    }
} else {
    console.warn('CSRF token not found. Please ensure meta[name="csrf-token"] is set in your HTML head.');
}

// Only initialize Echo if WebSocket is supported
const initializeEcho = () => {
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        const echoConfig = {
            // Pusher cloud configuration
            broadcaster: 'pusher',
            key: '39ed0339a3ef1d378fa6',
            cluster: 'ap1',
            forceTLS: true,

            // Explicitly set the auth endpoint and disable Pusher's default
            authEndpoint: '/broadcasting/auth',
            disableStats: true,
            
            // Authentication configuration
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            },
            
            // Override Pusher's default auth endpoint
            authorizer: (channel, options) => ({
                authorize: (socketId, callback) => {
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    }, options.auth)
                    .then(response => callback(false, response.data))
                    .catch(error => callback(true, error));
                }
            }),
        };

        console.log('Initializing Echo with config:', {
            ...echoConfig,
            auth: { headers: { ...echoConfig.auth.headers, 'X-CSRF-TOKEN': '***' } } // Hide token in logs
        });

        // Create a new Echo instance with Pusher
        window.Echo = new Echo(echoConfig);

        // Pusher connection state logging
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                console.log('Pusher connection state changed:', states);
            });
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('Pusher connected!');
            });
            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('Pusher connection error:', err);
            });
        } else {
            console.warn('Pusher client not available for connection logging.');
        }

        console.log('Echo instance created');
        setupEchoListeners();
        
    } catch (error) {
        console.error('Error initializing Echo:', error);
        // Don't throw to prevent breaking the application
    }
};

// Setup connection event listeners
const setupEchoListeners = () => {
    if (!window.Echo) {
        console.warn('Echo not available');
        return false;
    }

    console.log('Echo initialized');
    
    return true;
};

// Initialize Echo when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        initializeEcho();
    }
});

// Also try initializing immediately if DOM is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(() => {
        try {
            initializeEcho();
        } catch (error) {
            console.error('Failed to initialize Echo:', error);
        }
    }, 100);
}

// Export for debugging
window.initializeEcho = initializeEcho;

// Helper: Wait for Echo to be ready and connected (Pusher Cloud compatible)
window.waitForEcho = function (callback) {
    if (
        window.Echo &&
        window.Echo.connector &&
        window.Echo.connector.pusher &&
        window.Echo.connector.pusher.connection.state === 'connected'
    ) {
        callback(window.Echo);
    } else if (
        window.Echo &&
        window.Echo.connector &&
        window.Echo.connector.pusher
    ) {
        // Bind once to 'connected' event
        const onConnected = function () {
            callback(window.Echo);
            window.Echo.connector.pusher.connection.unbind('connected', onConnected);
        };
        window.Echo.connector.pusher.connection.bind('connected', onConnected);
    } else {
        // Try again in a moment if Echo isn't ready yet
        setTimeout(() => window.waitForEcho(callback), 200);
    }
};
