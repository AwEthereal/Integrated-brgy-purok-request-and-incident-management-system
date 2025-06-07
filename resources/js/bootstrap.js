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
    // Make Pusher available globally
    window.Pusher = Pusher;
    
    // Log CSRF token status
    if (!csrfToken) {
        console.warn('CSRF token found but empty. Please ensure meta[name="csrf-token"] has a valid token.');
    }
} else {
    console.warn('CSRF token not found. Please ensure meta[name="csrf-token"] is set in your HTML head.');
}

// Initialize Echo with Reverb
const echoConfig = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key',
    wsHost: '127.0.0.1',  // Use explicit IP to avoid DNS resolution issues
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
        }
    },
    authEndpoint: '/broadcasting/auth'
};

console.log('Initializing Echo with config:', {
    ...echoConfig,
    auth: { headers: { ...echoConfig.auth.headers, 'X-CSRF-TOKEN': '***' } } // Hide token in logs
});

window.Echo = new Echo(echoConfig);

// Log connection status
console.log('Initializing Echo with configuration:', {
    host: window.location.hostname,
    port: import.meta.env.VITE_REVERB_PORT || 8080,
    key: import.meta.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key'
});

// Setup connection event listeners
const setupEchoListeners = () => {
    if (!window.Echo || !window.Echo.connector) {
        console.warn('Echo or Echo.connector not available');
        return false;
    }

    console.log('Echo connector initialized');
    
    // Listen for connection events
    if (window.Echo.connector.pusher) {
        const { pusher } = window.Echo.connector;
        
        // Connection state changes
        pusher.connection.bind('state_change', (states) => {
            console.log('Pusher state changed:', states);
        });
        
        // Connected
        pusher.connection.bind('connected', () => {
            console.log('Successfully connected to WebSockets server');
        });
        
        // Disconnected
        pusher.connection.bind('disconnected', () => {
            console.log('Disconnected from WebSockets server');
        });
        
        // Error
        pusher.connection.bind('error', (error) => {
            console.error('WebSocket connection error:', error);
        });
        
        return true;
    } else {
        console.warn('Pusher instance not available on Echo.connector');
        return false;
    }
};

// Initial setup
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded, setting up Echo listeners...');
    setupEchoListeners();
});

// Also try setting up immediately in case DOM is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(setupEchoListeners, 100);
}

// Export for debugging
window.setupEchoListeners = setupEchoListeners;
