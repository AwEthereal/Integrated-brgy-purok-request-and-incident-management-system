import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally for Laravel Echo
window.Pusher = Pusher;

// Get CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');

// Initialize Echo with Reverb
console.log('Initializing Echo with configuration:', {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key',
    wsHost: window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws'],
    authEndpoint: '/broadcasting/auth'
});

// Log environment variables
console.log('Environment Variables:', {
    VITE_REVERB_APP_KEY: import.meta.env.VITE_REVERB_APP_KEY,
    VITE_REVERB_HOST: import.meta.env.VITE_REVERB_HOST,
    VITE_REVERB_PORT: import.meta.env.VITE_REVERB_PORT,
    VITE_REVERB_SCHEME: import.meta.env.VITE_REVERB_SCHEME,
    APP_URL: import.meta.env.APP_URL,
    APP_ENV: import.meta.env.APP_ENV
});

// Initialize Echo with Reverb
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key',
    wsHost: window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': token ? token.content : '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
    },
    authEndpoint: '/broadcasting/auth',
});

// Log when Echo is initialized
console.log('Echo initialized successfully with Reverb');

// Log WebSocket URL
const wsUrl = `ws://${window.location.hostname}:${import.meta.env.VITE_REVERB_PORT || 8080}/app/${import.meta.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key'}`;
console.log('WebSocket URL:', wsUrl);

// Log when attempting to connect
console.log('Attempting to connect to WebSocket server...');

// Add connection state logging
const connection = window.Echo.connector.pusher.connection;

connection.bind('connecting', () => {
    console.log('Connecting to WebSocket server...');
});

connection.bind('connected', () => {
    console.log('Successfully connected to WebSocket server');
    console.log('Socket ID:', window.Echo.socketId());
});

connection.bind('unavailable', () => {
    console.error('WebSocket connection unavailable');
});

connection.bind('failed', () => {
    console.error('WebSocket connection failed');
});

connection.bind('message', (message) => {
    console.log('Raw WebSocket message:', message);
});

// Log when the WebSocket is disconnected
connection.bind('disconnected', () => {
    console.log('WebSocket disconnected');
    // Attempt to reconnect after 5 seconds
    setTimeout(() => {
        console.log('Attempting to reconnect...');
        window.Echo.connect();
    }, 5000);
});

// Log any errors
connection.bind('error', (error) => {
    console.error('WebSocket error:', error);
});

// Log when subscribing to channels
const originalSubscribe = window.Echo.connector.subscribe;
window.Echo.connector.subscribe = function(channel) {
    console.log('Subscribing to channel:', channel.name);
    const subscription = originalSubscribe.apply(this, arguments);

    // Log subscription success
    subscription.subscriptionSucceeded = function() {
        console.log('Successfully subscribed to channel:', channel.name);
    };

    return subscription;
};

try {
    // Make Pusher available globally for Laravel Echo
    window.Pusher = Pusher;

    // Initialize Echo with our configuration
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key',
        wsHost: window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
        forceTLS: false,
        enabledTransports: ['ws'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': token ? token.content : '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
        },
        authEndpoint: '/broadcasting/auth',
    });

    console.log('Echo initialized successfully with Reverb');
    console.log('WebSocket URL:', `ws://${window.location.hostname}:${import.meta.env.VITE_REVERB_PORT || 8080}`);

    // Log when the socket is created
    const originalSocket = window.Echo.connector.socket;
    if (originalSocket) {
        console.log('WebSocket instance created');

        // Add event listeners for the socket
        originalSocket.on('connect', () => {
            console.log('Connected to WebSocket server');
        });
        
        originalSocket.on('disconnect', () => {
            console.log('Disconnected from WebSocket server');
        });
        
        originalSocket.on('error', (error) => {
            console.error('WebSocket error:', error);
        });
        
        // Override the socket's connect method to add logging
        const originalSocketConnect = originalSocket.connect;
        originalSocket.connect = function() {
            console.log('Attempting to establish WebSocket connection...');
            return originalSocketConnect.apply(this, arguments);
        };
    } else {
        console.warn('WebSocket instance not immediately available');
    }
    
} catch (error) {
    console.error('Failed to initialize Echo with Reverb:', error);
    
    // Try to initialize with default settings if the first attempt fails
    try {
        console.log('Attempting to initialize with default settings...');
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: 'kalawag_brgy_key',
            wsHost: '127.0.0.1',
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss']
        });
        console.log('Echo reinitialized with default settings');
    } catch (retryError) {
        console.error('Failed to initialize Echo with default settings:', retryError);
    }
}
